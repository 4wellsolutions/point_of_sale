<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Batch;
use App\Models\BatchStock;
use App\Models\InventoryTransaction;
use App\Models\LedgerEntry;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Booking::with(['customer', 'user'])->latest();

        if ($request->filled('date_from')) {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->paginate(15)->appends($request->all());
        $customers = Customer::orderBy('name')->get();

        return view('bookings.index', compact('bookings', 'customers'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::where('status', 'active')->orderBy('name')->get();
        return view('bookings.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'booking_date' => 'required|date',
            'status' => 'required|in:pending,completed,cancelled',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|exists:products,id',
            'quantity' => 'required|array',
            'quantity.*' => 'required|numeric|min:0.01',
            'unit_price' => 'required|array',
            'unit_price.*' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            $lastBooking = Booking::latest('id')->first();
            $nextId = $lastBooking ? $lastBooking->id + 1 : 1;
            $invoice_no = 'BKG-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

            $total_amount = 0;
            $items = [];
            foreach ($request->product_id as $key => $pid) {
                $qty = $request->quantity[$key];
                $price = $request->unit_price[$key];
                $subtotal = $qty * $price;
                $total_amount += $subtotal;

                $items[] = new BookingItem([
                    'product_id' => $pid,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'subtotal' => $subtotal,
                ]);
            }

            $discount = $request->discount_amount ?? 0;
            $net_amount = $total_amount - $discount;

            $booking = Booking::create([
                'invoice_no' => $invoice_no,
                'customer_id' => $request->customer_id,
                'user_id' => auth()->id(),
                'booking_date' => $request->booking_date,
                'status' => $request->status,
                'total_amount' => $total_amount,
                'discount_amount' => $discount,
                'net_amount' => $net_amount,
                'notes' => $request->notes,
            ]);

            $booking->items()->saveMany($items);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Order Booking created successfully',
                'redirect' =>
                    route('bookings.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function edit(Booking $booking)
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::where('status', 'active')->orderBy('name')->get();
        $booking->load(['items.product']);
        return view('bookings.edit', compact('booking', 'customers', 'products'));
    }

    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'booking_date' => 'required|date',
            'status' => 'required|in:pending,completed,cancelled,converted',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|exists:products,id',
            'quantity' => 'required|array',
            'quantity.*' => 'required|numeric|min:0.01',
            'unit_price' => 'required|array',
            'unit_price.*' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            $total_amount = 0;
            $itemsData = [];
            foreach ($request->product_id as $key => $pid) {
                $qty = $request->quantity[$key];
                $price = $request->unit_price[$key];
                $subtotal = $qty * $price;
                $total_amount += $subtotal;

                $itemsData[] = [
                    'product_id' => $pid,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'subtotal' => $subtotal,
                ];
            }

            $discount = $request->discount_amount ?? 0;
            $net_amount = $total_amount - $discount;

            $booking->update([
                'customer_id' => $request->customer_id,
                'booking_date' => $request->booking_date,
                'status' => $request->status,
                'total_amount' => $total_amount,
                'discount_amount' => $discount,
                'net_amount' => $net_amount,
                'notes' => $request->notes,
            ]);

            $booking->items()->delete();
            $booking->items()->createMany($itemsData);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Order Booking updated successfully',
                'redirect' =>
                    route('bookings.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Booking $booking)
    {
        try {
            DB::beginTransaction();
            $booking->items()->delete();
            $booking->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Order Booking deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show(Booking $booking)
    {
        $booking->load(['customer', 'items.product', 'user', 'sale']);
        return view('bookings.print', compact('booking'));
    }

    public function getProductDetails($id)
    {
        $product = Product::findOrFail($id);
        $availableStock = \App\Models\BatchStock::where('product_id', $product->id)->sum('quantity');

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->sale_price,
            'available_stock' => $availableStock
        ]);
    }

    /**
     * Convert a booking into a full sale.
     * Auto-selects available batch stocks (cheapest-first / FIFO).
     */
    public function convertToSale(Booking $booking)
    {
        if ($booking->status === 'converted') {
            return response()->json(
                ['success' => false, 'message' => 'This booking has already been converted to a sale.'],
                422
            );
        }

        $booking->load(['customer', 'items.product']);

        DB::beginTransaction();
        try {
            // Create the Sale header
            $lastInvoice = Sale::max(\DB::raw('CAST(invoice_no AS UNSIGNED)'));
            $invoiceNo = $lastInvoice ? $lastInvoice + 1 : 1;

            $totalAmount = $booking->total_amount;
            $netAmount = $booking->net_amount;

            $sale = Sale::create([
                'customer_id' => $booking->customer_id,
                'invoice_no' => $invoiceNo,
                'sale_date' => now()->toDateString(),
                'total_amount' => $totalAmount,
                'discount_amount' => $booking->discount_amount,
                'net_amount' => $netAmount,
                'user_id' => auth()->id(),
                'notes' => 'Converted from Booking ' . $booking->invoice_no,
            ]);

            foreach ($booking->items as $item) {
                // Find best available batch stock (largest qty first to minimise splits)
                $batchStock = BatchStock::where('product_id', $item->product_id)
                    ->where('quantity', '>', 0)
                    ->orderByDesc('quantity')
                    ->first();

                if (!$batchStock) {
                    throw new \Exception("No stock available for product: {$item->product->name}");
                }

                if ($batchStock->quantity < $item->quantity) {
                    throw new \Exception("Insufficient stock for product: {$item->product->name}. Available:
        {$batchStock->quantity}, Required: {$item->quantity}");
                }

                $batch = Batch::find($batchStock->batch_id);

                $batchStock->decrement('quantity', $item->quantity);

                $saleItem = $sale->saleItems()->create([
                    'product_id' => $item->product_id,
                    'batch_id' => $batch->id,
                    'location_id' => $batchStock->location_id,
                    'purchase_price' => $batchStock->purchase_price,
                    'sale_price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'total_amount' => $item->subtotal,
                ]);

                InventoryTransaction::create([
                    'product_id' => $item->product_id,
                    'location_id' => $batchStock->location_id,
                    'batch_id' => $batch->id,
                    'quantity' => $item->quantity,
                    'user_id' => auth()->id(),
                    'transactionable_id' => $sale->id,
                    'transactionable_type' => Sale::class,
                ]);
            }

            // Ledger entry (debit — customer owes us)
            $customer = Customer::find($sale->customer_id);
            $prevBalance = LedgerEntry::where('ledgerable_id', $sale->customer_id)
                ->where('ledgerable_type', Customer::class)
                ->latest('id')->value('balance') ?? 0;

            $ledger = new LedgerEntry([
                'transaction_id' => null,
                'date' => $sale->sale_date,
                'description' => 'Sale Invoice #' . $sale->invoice_no . ' (from Booking ' . $booking->invoice_no . ')',
                'debit' => $netAmount,
                'credit' => 0,
                'balance' => $prevBalance + $netAmount,
                'user_id' => auth()->id(),
            ]);
            $ledger->ledgerable()->associate($customer);
            $ledger->save();

            // Mark booking as converted
            $booking->update(['status' => 'converted', 'sale_id' => $sale->id]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking converted to sale successfully.',
                'redirect' => route('sales.show', $sale->id),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}