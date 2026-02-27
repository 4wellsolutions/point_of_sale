<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Batch;
use App\Models\BatchStock;
use App\Models\InventoryTransaction;
use App\Models\Transaction;
use App\Models\LedgerEntry;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Sale::query();

        // Filter by customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Filter by sale date range
        if ($request->filled('from_date')) {
            $query->whereDate('sale_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('sale_date', '<=', $request->to_date);
        }

        // Optional: Filter by sale type (sell/return) via sale items relationship
        if ($request->filled('sale_type')) {
            $query->whereHas('saleItems', function ($q) use ($request) {
                $q->where('sale_type', $request->sale_type);
            });
        }

        // Optional: Filter by invoice number if your sales have one
        if ($request->filled('invoice_no')) {
            $query->where('invoice_no', 'LIKE', '%' . $request->invoice_no . '%');
        }

        // Eager load customer relationship for display purposes
        $sales = $query->with('customer')->latest()->paginate(15)->appends($request->all());

        // For filtering, we might want to list customers in a dropdown
        $customers = Customer::orderBy('name')->get();

        return view('sales.index', compact('sales', 'customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Retrieve available payment methods for the dropdown.
        $paymentMethods = PaymentMethod::all();

        // Retrieve the maximum invoice_no from the sales table.
        $lastInvoice = Sale::max('invoice_no');

        // If there’s no previous invoice, start from 1; otherwise, add 1.
        $invoice_no = $lastInvoice ? $lastInvoice + 1 : 1;

        // Pass the new invoice number along with the payment methods to the view.
        return view('sales.create', compact('paymentMethods', 'invoice_no'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sale_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'net_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable',
            'sale_items' => 'required|array|min:1',
            'sale_items.*.product_id' => 'required|exists:products,id',
            'sale_items.*.batch_no' => 'required|string',
            'sale_items.*.location_id' => 'required|exists:locations,id',
            'sale_items.*.purchase_price' => 'required|numeric|min:0',
            'sale_items.*.sale_price' => 'required|numeric|min:0',
            'sale_items.*.discount' => 'nullable|numeric|min:0',
            'sale_items.*.quantity' => 'required|numeric|min:1',
            'sale_items.*.total_amount' => 'required|numeric|min:0',
            'payment_methods' => 'nullable|array',
            'payment_methods.*.payment_method_id' => 'required_with:payment_methods|exists:payment_methods,id',
            'payment_methods.*.amount' => 'required_with:payment_methods|numeric|min:0.01',
        ]);

        \DB::beginTransaction();

        try {
            // Generate invoice number
            $lastInvoice = Sale::max('invoice_no');
            $invoiceNo = $lastInvoice ? $lastInvoice + 1 : 1;

            // Create the sale record
            $sale = Sale::create([
                'customer_id' => $validatedData['customer_id'],
                'invoice_no' => $invoiceNo,
                'sale_date' => $validatedData['sale_date'],
                'total_amount' => $validatedData['total_amount'],
                'discount_amount' => $validatedData['discount_amount'],
                'net_amount' => $validatedData['net_amount'],
                'user_id' => auth()->id(),
                'notes' => $validatedData['notes'] ?? null,
            ]);

            // Iterate through each sale item
            foreach ($validatedData['sale_items'] as $itemData) {
                // Find the batch
                $batch = Batch::where('product_id', $itemData['product_id'])
                    ->where('batch_no', $itemData['batch_no'])
                    ->first();

                if (!$batch) {
                    $product = \App\Models\Product::find($itemData['product_id']);
                    throw new \Exception("Batch '{$itemData['batch_no']}' not found for product '{$product->name}'.");
                }

                // Find the corresponding BatchStock entry
                $batchStock = BatchStock::where('batch_id', $batch->id)
                    ->where('location_id', $itemData['location_id'])
                    ->first();

                if ($batchStock) {
                    // Check if there's enough stock
                    if ($batchStock->quantity < $itemData['quantity']) {
                        $product = \App\Models\Product::find($itemData['product_id']);
                        throw new \Exception("Not enough stock for '{$product->name}' (Batch: {$itemData['batch_no']}). Available: {$batchStock->quantity}, Requested: {$itemData['quantity']}.");
                    }

                    // Reduce the stock quantity
                    $batchStock->decrement('quantity', $itemData['quantity']);
                } else {
                    $product = \App\Models\Product::find($itemData['product_id']);
                    $location = \App\Models\Location::find($itemData['location_id']);
                    throw new \Exception("No stock found for '{$product->name}' (Batch: {$itemData['batch_no']}) at location '{$location->name}'.");
                }

                // Create sale item record
                $sale->saleItems()->create([
                    'product_id' => $itemData['product_id'],
                    'batch_id' => $batch->id,
                    'location_id' => $itemData['location_id'],
                    'purchase_price' => $itemData['purchase_price'],
                    'sale_price' => $itemData['sale_price'],
                    'discount' => $itemData['discount'] ?? 0,
                    'quantity' => $itemData['quantity'],
                    'total_amount' => $itemData['total_amount'],
                ]);

                // Create the inventory transaction for sale
                InventoryTransaction::create([
                    'product_id' => $itemData['product_id'],
                    'location_id' => $itemData['location_id'],
                    'batch_id' => $batch->id,
                    'quantity' => $itemData['quantity'],
                    'user_id' => auth()->id(),
                    'transactionable_id' => $sale->id,
                    'transactionable_type' => get_class($sale),
                ]);
            }

            // 7. Create Ledger Entry for the Sale (DEBIT — customer owes us)
            $saleAmount = $request->net_amount + ($request->discount_amount ?? 0);
            $customer = Customer::find($sale->customer_id);
            $saleLedger = new LedgerEntry([
                'transaction_id' => null,
                'date' => $sale->sale_date,
                'description' => 'Sale Invoice #' . $sale->invoice_no,
                'debit' => $saleAmount,   // customer now owes us
                'credit' => 0,
                'balance' => $this->calculateNewBalance($sale->customer_id, $saleAmount, 'debit'),
                'user_id' => auth()->id(),
            ]);
            $saleLedger->ledgerable()->associate($customer);
            $saleLedger->save();

            // Create payment methods if provided
            // 8. Handle Payment Methods only if provided
            if (is_array($request->payment_methods) && count($request->payment_methods) > 0) {
                foreach ($request->payment_methods as $payment) {
                    // Create Transaction (Credit) for each payment
                    $transaction = Transaction::create([
                        'payment_method_id' => $payment['payment_method_id'],
                        'vendor_id' => null,
                        'customer_id' => $sale->customer_id,
                        'amount' => $payment['amount'],
                        'transactionable_id' => $sale->id,
                        'transactionable_type' => get_class($sale),
                        'transaction_type' => 'debit',
                        'transaction_date' => $sale->sale_date,
                    ]);

                    // Create Ledger Entry for the Payment (CREDIT — customer reduces their debt)
                    $paymentLedger = new LedgerEntry([
                        'transaction_id' => $transaction->id,
                        'date' => $sale->sale_date,
                        'description' => 'Payment for Sales Invoice #' . $sale->invoice_no,
                        'debit' => 0,
                        'credit' => $payment['amount'],   // reduces what customer owes
                        'balance' => $this->calculateNewBalance($sale->customer_id, $payment['amount'], 'credit'),
                        'user_id' => auth()->id(),
                    ]);
                    $paymentLedger->ledgerable()->associate($customer);
                    $paymentLedger->save();

                }
            }

            // Commit the transaction
            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale created successfully.',
                'redirect' => route('sales.index'),
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the sale.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    protected function calculateNewBalance($customerId, $amount, $type)
    {
        // Fetch the latest ledger entry balance for the customer
        $latestLedger = LedgerEntry::where('ledgerable_id', $customerId)
            ->where('ledgerable_type', Customer::class)
            ->latest('id')
            ->first();

        $previousBalance = $latestLedger ? $latestLedger->balance : 0;

        if ($type === 'debit') {
            return $previousBalance + $amount;
        } elseif ($type === 'credit') {
            return $previousBalance - $amount;
        }

        return $previousBalance;
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Retrieve the sale with related customer, sale items, transactions, and payment methods
        $sale = Sale::with([
            'customer',
            'saleItems.product',
            'saleItems.location',
            'transactions.paymentMethod',
            'customer.LedgerEntries' // Assuming Customer has ledgerEntries relation
        ])->findOrFail($id);

        // Check if the request expects a JSON response (for sale return)
        if (request()->wantsJson()) {
            return response()->json([
                'customer_name' => $sale->customer->name,
                'sale_items' => $sale->saleItems,
            ]);
        }

        // Return the view for regular sale view
        return view('sales.show', compact('sale'));
    }

    public function generatePdf($saleId)
    {
        // Retrieve the sale with related customer, sale items, transactions, and payment methods
        $sale = Sale::with(['customer', 'saleItems.product', 'saleItems.location', 'transactions.paymentMethod'])->findOrFail($saleId);

        // Load the PDF view with the sale data
        $pdf = PDF::loadView('sales.pdf', compact('sale'));

        // Display the PDF in the browser (inline)
        return $pdf->stream('sale-details-' . $sale->invoice_no . '.pdf');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $sale = Sale::with(['customer', 'saleItems.product', 'saleItems.location', 'transactions.paymentMethod'])->findOrFail($id);
        $paymentMethods = PaymentMethod::all();
        $locations = \App\Models\Location::all();
        return view('sales.edit', compact('sale', 'paymentMethods', 'locations'));
    }

    /**
     * Update the specified sale — full multi-item edit with stock & ledger reversal.
     */
    public function update(Request $request, string $id)
    {
        $sale = Sale::with(['saleItems', 'transactions'])->findOrFail($id);

        // ── 1. Validate ──
        $rules = [
            'customer_id' => 'required|exists:customers,id',
            'sale_date' => 'required|date',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'net_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'sale_items' => 'required|array|min:1',
            'sale_items.*.product_id' => 'required|exists:products,id',
            'sale_items.*.batch_no' => 'required|string',
            'sale_items.*.location_id' => 'required|exists:locations,id',
            'sale_items.*.purchase_price' => 'required|numeric|min:0',
            'sale_items.*.sale_price' => 'required|numeric|min:0',
            'sale_items.*.discount' => 'nullable|numeric|min:0',
            'sale_items.*.quantity' => 'required|integer|min:1',
            'sale_items.*.total_amount' => 'required|numeric|min:0',
            'payment_methods' => 'nullable|array',
            'payment_methods.*.payment_method_id' => 'required_with:payment_methods.*.amount|exists:payment_methods,id',
            'payment_methods.*.amount' => 'required_with:payment_methods.*.payment_method_id|numeric|min:0.01',
        ];

        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'There were some errors with your submission.',
                'errors' => $validator->errors(),
            ], 422);
        }

        \DB::beginTransaction();
        try {
            // ── 2. REVERSE OLD STOCK & DELETE OLD ITEMS ──
            foreach ($sale->saleItems as $item) {
                $batch = Batch::where('product_id', $item->product_id)
                    ->where('batch_no', $item->batch_no)
                    ->first();

                if ($batch) {
                    $batchStock = BatchStock::where('batch_id', $batch->id)
                        ->where('location_id', $item->location_id)
                        ->first();

                    if ($batchStock) {
                        $batchStock->increment('quantity', $item->quantity);
                    } else {
                        BatchStock::create([
                            'batch_id' => $batch->id,
                            'product_id' => $item->product_id,
                            'location_id' => $item->location_id,
                            'quantity' => $item->quantity,
                            'purchase_price' => $item->purchase_price,
                            'sale_price' => $item->sale_price,
                        ]);
                    }

                    InventoryTransaction::create([
                        'product_id' => $item->product_id,
                        'location_id' => $item->location_id,
                        'batch_id' => $batch->id,
                        'quantity' => $item->quantity, // positive = stock returned
                        'user_id' => auth()->id(),
                        'transactionable_id' => $sale->id,
                        'transactionable_type' => Sale::class,
                    ]);
                }

                $item->delete();
            }

            // ── 3. REVERSE OLD LEDGER ENTRIES ──
            $oldLedgers = LedgerEntry::where('ledgerable_id', $sale->customer_id)
                ->where('ledgerable_type', Customer::class)
                ->where('description', 'LIKE', '%Invoice #' . $sale->invoice_no . '%')
                ->get();

            $customer = Customer::find($sale->customer_id);
            foreach ($oldLedgers as $ledger) {
                $reversalLedger = new LedgerEntry([
                    'transaction_id' => $ledger->transaction_id,
                    'date' => now(),
                    'description' => 'Reversal: ' . $ledger->description,
                    'debit' => $ledger->credit,
                    'credit' => $ledger->debit,
                    'balance' => $this->calculateNewBalance(
                        $sale->customer_id,
                        $ledger->debit ?: $ledger->credit,
                        $ledger->debit > 0 ? 'credit' : 'debit'
                    ),
                    'user_id' => auth()->id(),
                ]);
                $reversalLedger->ledgerable()->associate($customer);
                $reversalLedger->save();
            }

            // ── 4. DELETE OLD TRANSACTIONS ──
            foreach ($sale->transactions as $transaction) {
                $transaction->delete();
            }

            // ── 5. UPDATE SALE HEADER ──
            $sale->update([
                'customer_id' => $request->customer_id,
                'sale_date' => $request->sale_date,
                'discount_amount' => $request->discount_amount ?? 0,
                'total_amount' => $request->total_amount,
                'net_amount' => $request->net_amount,
                'notes' => $request->notes,
            ]);

            // ── 6. CREATE NEW ITEMS ──
            foreach ($request->sale_items as $itemData) {
                $itemTotal = $itemData['quantity'] * $itemData['sale_price'];

                $batch = Batch::where('product_id', $itemData['product_id'])
                    ->where('batch_no', $itemData['batch_no'])
                    ->first();

                if (!$batch) {
                    throw new \Exception("Batch '{$itemData['batch_no']}' not found.");
                }

                $batchStock = BatchStock::where('batch_id', $batch->id)
                    ->where('location_id', $itemData['location_id'])
                    ->first();

                if (!$batchStock || $batchStock->quantity < $itemData['quantity']) {
                    throw new \Exception("Insufficient stock in the selected location.");
                }

                $batchStock->decrement('quantity', $itemData['quantity']);

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $itemData['product_id'],
                    'batch_id' => $batch->id,
                    'batch_no' => $itemData['batch_no'],
                    'location_id' => $itemData['location_id'],
                    'purchase_price' => $itemData['purchase_price'],
                    'sale_price' => $itemData['sale_price'],
                    'discount' => $itemData['discount'] ?? 0,
                    'quantity' => $itemData['quantity'],
                    'total_amount' => $itemTotal,
                ]);

                InventoryTransaction::create([
                    'product_id' => $itemData['product_id'],
                    'location_id' => $itemData['location_id'],
                    'batch_id' => $batch->id,
                    'quantity' => $itemData['quantity'],
                    'user_id' => auth()->id(),
                    'transactionable_id' => $sale->id,
                    'transactionable_type' => Sale::class,
                ]);
            }

            // ── 7. NEW LEDGER ENTRY (debit) ──
            $customer = Customer::find($request->customer_id);
            $saleLedger = new LedgerEntry([
                'transaction_id' => null,
                'date' => now(),
                'description' => 'Sale Invoice #' . $sale->invoice_no,
                'debit' => $request->net_amount,
                'credit' => 0,
                'balance' => $this->calculateNewBalance($request->customer_id, $request->net_amount, 'debit'),
                'user_id' => auth()->id(),
            ]);
            $saleLedger->ledgerable()->associate($customer);
            $saleLedger->save();

            // ── 8. NEW PAYMENT ENTRIES ──
            if (is_array($request->payment_methods) && count($request->payment_methods) > 0) {
                foreach ($request->payment_methods as $payment) {
                    $transaction = Transaction::create([
                        'payment_method_id' => $payment['payment_method_id'],
                        'customer_id' => $request->customer_id,
                        'vendor_id' => null,
                        'amount' => $payment['amount'],
                        'transactionable_id' => $sale->id,
                        'transactionable_type' => Sale::class,
                        'transaction_type' => 'credit',
                        'transaction_date' => now(),
                    ]);

                    $paymentLedger = new LedgerEntry([
                        'transaction_id' => $transaction->id,
                        'date' => now(),
                        'description' => 'Payment for Sale Invoice #' . $sale->invoice_no,
                        'debit' => 0,
                        'credit' => $payment['amount'],
                        'balance' => $this->calculateNewBalance($request->customer_id, $payment['amount'], 'credit'),
                        'user_id' => auth()->id(),
                    ]);
                    $paymentLedger->ledgerable()->associate($customer);
                    $paymentLedger->save();
                }
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale updated successfully.',
                'redirect' => route('sales.index'),
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Sale Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating sale: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $sale = Sale::with(['saleItems', 'transactions'])->findOrFail($id);

        \DB::beginTransaction();
        try {
            // 1. Restore stock for each sale item
            foreach ($sale->saleItems as $item) {
                $batch = Batch::where('product_id', $item->product_id)
                    ->where('batch_no', $item->batch_no)
                    ->first();

                if ($batch) {
                    $batchStock = BatchStock::where('batch_id', $batch->id)
                        ->where('location_id', $item->location_id)
                        ->first();

                    if ($batchStock) {
                        $batchStock->increment('quantity', $item->quantity);
                    } else {
                        // Re-create the batch stock if it was depleted
                        BatchStock::create([
                            'batch_id' => $batch->id,
                            'product_id' => $item->product_id,
                            'location_id' => $item->location_id,
                            'quantity' => $item->quantity,
                            'purchase_price' => $item->purchase_price,
                            'sale_price' => $item->sale_price,
                        ]);
                    }

                    // Create reversal inventory transaction
                    InventoryTransaction::create([
                        'product_id' => $item->product_id,
                        'location_id' => $item->location_id,
                        'batch_id' => $batch->id,
                        'quantity' => $item->quantity,
                        'user_id' => auth()->id(),
                        'transactionable_id' => $sale->id,
                        'transactionable_type' => Sale::class,
                    ]);
                }

                $item->delete();
            }

            // 2. Reverse ledger entries for this sale
            $saleLedgers = LedgerEntry::where('ledgerable_id', $sale->customer_id)
                ->where('ledgerable_type', Customer::class)
                ->where('description', 'LIKE', '%Invoice #' . $sale->invoice_no . '%')
                ->get();

            foreach ($saleLedgers as $ledger) {
                // Create reversal entry
                $reversalLedger = new LedgerEntry([
                    'transaction_id' => $ledger->transaction_id,
                    'date' => now(),
                    'description' => 'Reversal: ' . $ledger->description,
                    'debit' => $ledger->credit,
                    'credit' => $ledger->debit,
                    'balance' => $this->calculateNewBalance($sale->customer_id, $ledger->credit ?: $ledger->debit, $ledger->credit > 0 ? 'debit' : 'credit'),
                    'user_id' => auth()->id(),
                ]);
                $customer = Customer::find($sale->customer_id);
                $reversalLedger->ledgerable()->associate($customer);
                $reversalLedger->save();
            }

            // 3. Delete transactions/payments
            foreach ($sale->transactions as $transaction) {
                $transaction->delete();
            }

            // 4. Delete the sale (soft delete)
            $sale->delete();

            \DB::commit();
            return redirect()->route('sales.index')->with('success', 'Sale deleted and all effects reversed.');
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Sale Delete Error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error deleting sale: ' . $e->getMessage()]);
        }
    }

    public function exportPdf(Request $request)
    {
        $query = Sale::with('customer');
        if ($request->filled('customer_id'))
            $query->where('customer_id', $request->customer_id);
        if ($request->filled('from_date'))
            $query->whereDate('sale_date', '>=', $request->from_date);
        if ($request->filled('to_date'))
            $query->whereDate('sale_date', '<=', $request->to_date);
        $sales = $query->latest('sale_date')->get();

        $pdf = PDF::loadView('exports.sales', [
            'sales' => $sales,
            'title' => 'Sales Report',
            'filters' => array_filter([
                $request->from_date ? 'From: ' . $request->from_date : null,
                $request->to_date ? 'To: ' . $request->to_date : null,
            ]),
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('sales-report.pdf');
    }

    public function exportCsv(Request $request)
    {
        $query = Sale::with('customer');
        if ($request->filled('customer_id'))
            $query->where('customer_id', $request->customer_id);
        if ($request->filled('from_date'))
            $query->whereDate('sale_date', '>=', $request->from_date);
        if ($request->filled('to_date'))
            $query->whereDate('sale_date', '<=', $request->to_date);
        $sales = $query->latest('sale_date')->get();

        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="sales-report.csv"'];
        $callback = function () use ($sales) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['#', 'Invoice No', 'Customer', 'Date', 'Total', 'Discount', 'Net Amount']);
            foreach ($sales as $i => $s) {
                fputcsv($file, [$i + 1, $s->invoice_no, $s->customer->name ?? 'Walk-in', $s->sale_date, $s->total_amount, $s->discount_amount, $s->net_amount]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Minimal HTML invoice view — used by multi-print iframes.
     */
    public function printView(\App\Models\Sale $sale)
    {
        $sale->load(['customer', 'saleItems.product', 'saleItems.location', 'transactions.paymentMethod']);
        return view('sales.print-view', compact('sale'));
    }
}
