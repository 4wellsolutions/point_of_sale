<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Customer;
use App\Models\Product;
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
        // Return products that are active and have positive reorder level, or just all active. Let's return all active items so salesman can see all products.
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

            // Generate Invoice No (Booking)
            $lastBooking = Booking::latest('id')->first();
            $nextId = $lastBooking ? $lastBooking->id + 1 : 1;
            $invoice_no = 'BKG-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

            // Calculate Totals
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
            return response()->json(['success' => true, 'message' => 'Order Booking created successfully', 'redirect' => route('bookings.index')]);
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

            // Recalculate Totals
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

            // Sync items (delete old, insert new)
            $booking->items()->delete();
            $booking->items()->createMany($itemsData);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Order Booking updated successfully', 'redirect' => route('bookings.index')]);
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
        $booking->load(['customer', 'items.product', 'user']);
        return view('bookings.print', compact('booking'));
    }

    public function getProductDetails($id)
    {
        $product = Product::findOrFail($id);

        // Calculate available stock
        $availableStock = \App\Models\BatchStock::where('product_id', $product->id)->sum('quantity');

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->sale_price,
            'available_stock' => $availableStock
        ]);
    }
}
