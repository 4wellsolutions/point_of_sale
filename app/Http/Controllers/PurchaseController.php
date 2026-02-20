<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\Batch;
use App\Models\InventoryTransaction;
use App\Models\PurchasePayment;
use App\Models\LedgerEntry;
use App\Models\Transaction;
use App\Models\PaymentMethod;
use App\Models\Location;
use App\Models\BatchStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Validator;
use Auth;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with(['vendor', 'purchaseItems.product']);
        if ($request->filled('vendor_id'))
            $query->where('vendor_id', $request->vendor_id);
        if ($request->filled('invoice_no'))
            $query->where('invoice_no', 'like', '%' . $request->invoice_no . '%');
        if ($request->filled('from_date'))
            $query->whereDate('purchase_date', '>=', $request->from_date);
        if ($request->filled('to_date'))
            $query->whereDate('purchase_date', '<=', $request->to_date);
        $purchases = $query->latest('purchase_date')->paginate(20);
        $vendors = Vendor::orderBy('name')->get();
        return view('purchases.index', compact('purchases', 'vendors'));
    }
    public function searchPurchases(Request $request)
    {
        $search = $request->input('q'); // Search term
        $page = $request->input('page', 1);
        $perPage = 20;

        $query = Purchase::with('vendor')
            ->where('invoice_no', 'like', '%' . $search . '%')
            ->orWhereHas('vendor', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('purchase_date', 'desc');

        $purchases = $query->paginate($perPage, ['*'], 'page', $page);

        $results = [];
        foreach ($purchases as $purchase) {
            $results[] = [
                'id' => $purchase->id,
                'text' => 'Invoice: ' . $purchase->invoice_no . ' | Vendor: ' . $purchase->vendor->name . ' | Date: ' . $purchase->purchase_date,
            ];
        }

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => $purchases->hasMorePages(),
            ],
        ]);
    }
    /**
     * Show the form for creating a new purchase.
     */
    public function show($id)
    {
        // Retrieve the purchase with related vendor, purchase items, transactions, and ledger entries
        $purchase = Purchase::with([
            'vendor',
            'purchaseItems.product',
            'purchaseItems.location',
            'transactions.paymentMethod',
            'vendor.LedgerEntries' // Assuming Vendor has ledgerEntries relation
        ])->findOrFail($id);

        // Check if the request expects a JSON response (for purchase return)
        if (request()->wantsJson()) {
            return response()->json([
                'vendor_name' => $purchase->vendor->name,
                'purchase_items' => $purchase->purchaseItems,
            ]);
        }

        // Return the view for regular purchase view
        return view('purchases.show', compact('purchase'));
    }
    public function create()
    {
        $vendors = Vendor::all();
        $locations = Location::all();
        $paymentMethods = PaymentMethod::all();
        $invoice_no = $this->generateInvoiceNo();
        return view('purchases.create', compact('vendors', 'paymentMethods', 'locations', 'invoice_no'));
    }

    /**
     * Generate a new invoice number in the format '000001', '000002', etc.
     */
    public function generateInvoiceNo()
    {
        // Get the latest invoice number
        $latestPurchase = Purchase::orderBy('id', 'desc')->first();

        if (!$latestPurchase) {
            $newInvoiceNo = '000001';
        } else {
            // Extract the numeric part and increment
            $latestInvoiceNo = $latestPurchase->invoice_no;
            $numericPart = intval($latestInvoiceNo);
            $newNumericPart = $numericPart + 1;
            $newInvoiceNo = str_pad($newNumericPart, 6, '0', STR_PAD_LEFT);

        }
        return $newInvoiceNo;
    }

    /**
     * Store a newly created purchase in storage via AJAX.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        // 1. Define validation rules
        $rules = [
            // Purchase Information
            'vendor_id' => 'required|exists:vendors,id',
            'invoice_no' => 'required|unique:purchases,invoice_no',
            'purchase_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:purchase_date',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'net_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',

            // Purchase Items
            'purchase_items' => 'required|array|min:1',
            'purchase_items.*.product_id' => 'required|exists:products,id',
            'purchase_items.*.batch_no' => 'required|string|max:255',
            'purchase_items.*.location_id' => 'required|exists:locations,id',
            'purchase_items.*.quantity' => 'required|integer|min:1',
            'purchase_items.*.purchase_price' => 'required|numeric|min:0.01',
            'purchase_items.*.sale_price' => 'required|numeric|min:0.01',

            // Payment Methods - Made Optional
            'payment_methods' => 'nullable|array',
            'payment_methods.*.payment_method_id' => 'required_with:payment_methods.*.amount|exists:payment_methods,id',
            'payment_methods.*.amount' => 'required_with:payment_methods.*.payment_method_id|numeric|min:0.01',
        ];

        // 2. Define custom error messages
        $messages = [
            // Purchase Information
            'vendor_id.required' => 'Please select a vendor.',
            'vendor_id.exists' => 'The selected vendor does not exist.',
            'invoice_no.required' => 'Invoice number is required.',
            'invoice_no.unique' => 'This invoice number has already been used. Please refresh to get a new one.',
            'purchase_date.required' => 'Please select a purchase date.',
            'purchase_date.date' => 'Purchase date must be a valid date.',
            'expiry_date.date' => 'Expiry date must be a valid date.',
            'expiry_date.after_or_equal' => 'Expiry date must be after or equal to the purchase date.',
            'discount_amount.numeric' => 'Discount amount must be a number.',
            'discount_amount.min' => 'Discount amount cannot be negative.',
            'notes.string' => 'Notes must be a valid text.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',

            // Purchase Items
            'purchase_items.required' => 'Please add at least one product to the purchase.',
            'purchase_items.array' => 'Purchase items must be an array.',
            'purchase_items.min' => 'Please add at least one product to the purchase.',
            'purchase_items.*.product_id.required' => 'Please select a product.',
            'purchase_items.*.product_id.exists' => 'Selected product does not exist.',
            'purchase_items.*.batch_no.string' => 'Batch number must be a valid string.',
            'purchase_items.*.batch_no.max' => 'Batch number cannot exceed 255 characters.',
            'purchase_items.*.quantity.required' => 'Please enter the quantity.',
            'purchase_items.*.quantity.integer' => 'Quantity must be an integer.',
            'purchase_items.*.quantity.min' => 'Quantity must be at least 1.',
            'purchase_items.*.purchase_price.required' => 'Please enter the purchase price.',
            'purchase_items.*.purchase_price.numeric' => 'Purchase price must be a number.',
            'purchase_items.*.purchase_price.min' => 'Purchase price cannot be negative.',
            'purchase_items.*.sale_price.required' => 'Please enter the sale price.',
            'purchase_items.*.sale_price.numeric' => 'Sale price must be a number.',
            'purchase_items.*.sale_price.min' => 'Sale price cannot be negative.',
            'purchase_items.*.location_id.required' => 'Please select a location.',
            'purchase_items.*.location_id.exists' => 'Selected location does not exist.',

            // Calculated Fields
            'total_amount.required' => 'Total amount is required.',
            'total_amount.numeric' => 'Total amount must be a number.',
            'total_amount.min' => 'Total amount cannot be negative.',
            'net_amount.required' => 'Net amount is required.',
            'net_amount.numeric' => 'Net amount must be a number.',
            'net_amount.min' => 'Net amount cannot be negative.',

            // Payment Methods - Updated Messages
            'payment_methods.array' => 'Payment methods must be an array.',
            'payment_methods.*.payment_method_id.required_with' => 'Please select a payment method.',
            'payment_methods.*.payment_method_id.exists' => 'Selected payment method does not exist.',
            'payment_methods.*.amount.required_with' => 'Please enter the payment amount.',
            'payment_methods.*.amount.numeric' => 'Payment amount must be a number.',
            'payment_methods.*.amount.min' => 'Payment amount must be at least 0.01.',
        ];

        // 3. Create validator instance
        $validator = Validator::make($request->all(), $rules, $messages);

        // 4. After validation to check:
        //    - Sum of purchase items matches total_amount and net_amount
        $validator->after(function ($validator) use ($request) {
            if (is_array($request->purchase_items)) {
                $calculatedTotal = 0;
                foreach ($request->purchase_items as $item) {
                    $calculatedTotal += ($item['quantity'] ?? 0) * ($item['purchase_price'] ?? 0); // Changed from cost_per_piece to purchase_price
                }

                $submittedTotal = round($request->total_amount, 2);
                $expectedTotal = round($calculatedTotal, 2);

                if ($submittedTotal !== $expectedTotal) {
                    $validator->errors()->add('total_amount', 'The total amount does not match the sum of all purchase items.');
                }

                $calculatedNet = $calculatedTotal - ($request->discount_amount ?? 0);
                $submittedNet = round($request->net_amount, 2);
                $expectedNet = round($calculatedNet, 2);

                if ($submittedNet !== $expectedNet) {
                    $validator->errors()->add('net_amount', 'The net amount does not match the total amount minus the discount.');
                }
            }
        });

        // 5. Check validation
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'There were some errors with your submission.',
                'errors' => $validator->errors()
            ], 422);
        }

        // 6. Proceed to store the purchase and related data
        DB::beginTransaction();

        try {

            // Create the purchase
            $purchase = Purchase::create([
                'vendor_id' => $request->vendor_id,
                'invoice_no' => $request->invoice_no,
                'purchase_date' => $request->purchase_date,
                'discount_amount' => $request->discount_amount,
                'total_amount' => $request->total_amount,
                'net_amount' => $request->net_amount,
                'notes' => $request->notes,
                'user_id' => auth()->id(),
            ]);

            // Iterate through each purchase item and create PurchaseItem records
            foreach ($request->purchase_items as $item) {
                $itemTotal = $item['quantity'] * $item['purchase_price'];

                // Find the batch
                $batch = Batch::where('product_id', $item['product_id'])
                    ->where('batch_no', $item['batch_no'])
                    ->first();

                if ($batch) {
                    // Check if a corresponding BatchStock entry exists for this location
                    $batchStock = BatchStock::where('batch_id', $batch->id)
                        ->where('location_id', $item['location_id'])
                        ->first();

                    if ($batchStock) {
                        // Update the existing BatchStock quantity (don't overwrite purchase_price)
                        $batchStock->update([
                            'quantity' => $batchStock->quantity + $item['quantity'],
                        ]);
                    } else {
                        // Create new BatchStock entry if it doesn't exist for this location
                        BatchStock::create([
                            'batch_id' => $batch->id,
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                            'purchase_price' => $item['purchase_price'],
                            'sale_price' => $item['sale_price'],
                            'location_id' => $item['location_id'],
                            'expiry_date' => $item['expiry_date'] ?? null,
                        ]);
                    }
                } else {
                    // If the batch doesn't exist, create a new batch
                    $batch = Batch::create([
                        'product_id' => $item['product_id'],
                        'batch_no' => $item['batch_no'],
                        'purchase_date' => $request->purchase_date,
                        'invoice_no' => $request->invoice_no,
                    ]);

                    // Create the new BatchStock entry for the new batch
                    BatchStock::create([
                        'batch_id' => $batch->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'purchase_price' => $item['purchase_price'],
                        'sale_price' => $item['sale_price'],
                        'location_id' => $item['location_id'],
                        'expiry_date' => $item['expiry_date'] ?? null,
                    ]);
                }

                // Create the purchase item record
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'batch_no' => $item['batch_no'],
                    'location_id' => $item['location_id'],
                    'expiry_date' => $item['expiry_date'] ?? null,
                    'quantity' => $item['quantity'],
                    'purchase_price' => $item['purchase_price'],
                    'sale_price' => $item['sale_price'],
                    'total_amount' => $itemTotal,
                ]);

                // Create the InventoryTransaction instance
                InventoryTransaction::create([
                    'product_id' => $item['product_id'],
                    'location_id' => $item['location_id'],
                    'batch_id' => $batch->id,
                    'quantity' => $item['quantity'],
                    'user_id' => auth()->id(),
                    'transactionable_id' => $purchase->id,
                    'transactionable_type' => get_class($purchase),
                ]);

            }


            // Update total_amount and net_amount in the purchase record
            $purchase->update([
                'total_amount' => $request->total_amount,
                'net_amount' => $request->net_amount,
            ]);

            // 7. Create Ledger Entry for the Purchase (Debit)
            $purchaseLedger = new LedgerEntry([
                'transaction_id' => null,
                'date' => now(),
                'description' => 'Purchase Invoice #' . $purchase->invoice_no,
                'debit' => $request->net_amount + $request->discount_amount,
                'credit' => 0,
                'balance' => $this->calculateNewBalance($purchase->vendor_id, $request->net_amount + $request->discount_amount, 'debit'),
                'user_id' => auth()->id(),
            ]);

            // Now set the polymorphic relation to the vendor
            $vendor = Vendor::find($purchase->vendor_id);
            $purchaseLedger->ledgerable()->associate($vendor);

            // Save the ledger entry
            $purchaseLedger->save();

            // 8. Handle Payment Methods only if provided
            if (is_array($request->payment_methods) && count($request->payment_methods) > 0) {
                foreach ($request->payment_methods as $payment) {
                    // Create Transaction (Credit) for each payment
                    $transaction = Transaction::create([
                        'payment_method_id' => $payment['payment_method_id'],
                        'vendor_id' => $purchase->vendor_id,
                        'customer_id' => null,
                        'amount' => $payment['amount'],
                        'transactionable_id' => $purchase->id,
                        'transactionable_type' => get_class($purchase),
                        'transaction_type' => 'credit',
                        'transaction_date' => now(),
                    ]);

                    // Create Ledger Entry for the Payment (Credit)
                    $paymentLedger = new LedgerEntry([
                        'transaction_id' => $transaction->id,
                        'date' => now(),
                        'description' => 'Payment for Purchase Invoice #' . $purchase->invoice_no,
                        'debit' => 0,
                        'credit' => $payment['amount'], // Money coming in
                        'balance' => $this->calculateNewBalance($purchase->vendor_id, $payment['amount'], 'credit'),
                        'user_id' => auth()->id(),
                    ]);

                    // Associate the ledger with the Vendor (polymorphic relationship)
                    $vendor = Vendor::find($purchase->vendor_id); // Get the Vendor model instance
                    $paymentLedger->ledgerable()->associate($vendor);

                    // Save the payment ledger entry
                    $paymentLedger->save();

                }
            }

            // 9. Commit the transaction
            DB::commit();

            // 10. Return success response
            return response()->json([
                'success' => true,
                'message' => 'Purchase has been successfully saved.',
                'redirect' => route('purchases.index')
            ], 200);

        } catch (\Exception $e) {
            // 11. Rollback the transaction on error
            DB::rollBack();

            // Log the error for debugging
            \Log::error('Purchase Store Error: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while saving the purchase. Please try again later.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function generatePdf($purchaseId)
    {
        $purchase = Purchase::with(['vendor', 'purchaseItems.product', 'purchaseItems.location', 'transactions.paymentMethod'])->findOrFail($purchaseId);

        $pdf = PDF::loadView('purchases.pdf', compact('purchase'));

        // Display PDF in browser (inline)
        return $pdf->stream('purchase-details-' . $purchase->invoice_no . '.pdf');
    }

    protected function calculateNewBalance($vendorId, $amount, $type)
    {
        // Fetch the latest ledger entry balance for the vendor via the polymorphic relationship
        $latestLedger = LedgerEntry::where('ledgerable_id', $vendorId)
            ->where('ledgerable_type', Vendor::class) // Ensure it’s a vendor
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
     * Update the specified purchase in storage (header-only edit).
     */
    public function update(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);

        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'purchase_date' => 'required|date',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $oldNetAmount = $purchase->net_amount;
        $newNetAmount = $purchase->total_amount - ($validated['discount_amount'] ?? 0);

        DB::beginTransaction();
        try {
            $purchase->update([
                'vendor_id' => $validated['vendor_id'],
                'purchase_date' => $validated['purchase_date'],
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'net_amount' => $newNetAmount,
                'notes' => $validated['notes'] ?? null,
            ]);

            // If net amount changed, create an adjustment ledger entry
            $difference = $newNetAmount - $oldNetAmount;
            if (abs($difference) > 0.001) {
                $vendor = Vendor::find($purchase->vendor_id);
                $adjustmentLedger = new LedgerEntry([
                    'transaction_id' => null,
                    'date' => now(),
                    'description' => 'Purchase Adjustment - Invoice #' . $purchase->invoice_no,
                    'debit' => $difference > 0 ? $difference : 0,
                    'credit' => $difference < 0 ? abs($difference) : 0,
                    'balance' => $this->calculateNewBalance($purchase->vendor_id, abs($difference), $difference > 0 ? 'debit' : 'credit'),
                    'user_id' => auth()->id(),
                ]);
                $adjustmentLedger->ledgerable()->associate($vendor);
                $adjustmentLedger->save();
            }

            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'Purchase updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error updating purchase: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified purchase from storage with full reversal.
     */
    public function destroy($id)
    {
        $purchase = Purchase::with(['purchaseItems', 'transactions'])->findOrFail($id);

        DB::beginTransaction();
        try {
            // 1. Reverse stock for each purchase item
            foreach ($purchase->purchaseItems as $item) {
                $batch = Batch::where('product_id', $item->product_id)
                    ->where('batch_no', $item->batch_no)
                    ->first();

                if ($batch) {
                    $batchStock = BatchStock::where('batch_id', $batch->id)
                        ->where('location_id', $item->location_id)
                        ->first();

                    if ($batchStock) {
                        $batchStock->decrement('quantity', $item->quantity);
                        // Delete batch stock if quantity reaches zero
                        if ($batchStock->quantity <= 0) {
                            $batchStock->delete();
                        }
                    }

                    // Create reversal inventory transaction
                    InventoryTransaction::create([
                        'product_id' => $item->product_id,
                        'location_id' => $item->location_id,
                        'batch_id' => $batch->id,
                        'quantity' => -$item->quantity,
                        'user_id' => auth()->id(),
                        'transactionable_id' => $purchase->id,
                        'transactionable_type' => Purchase::class,
                    ]);
                }

                $item->delete();
            }

            // 2. Reverse ledger entries for this purchase
            $purchaseLedgers = LedgerEntry::where('ledgerable_id', $purchase->vendor_id)
                ->where('ledgerable_type', Vendor::class)
                ->where('description', 'LIKE', '%Invoice #' . $purchase->invoice_no . '%')
                ->get();

            foreach ($purchaseLedgers as $ledger) {
                $reversalLedger = new LedgerEntry([
                    'transaction_id' => $ledger->transaction_id,
                    'date' => now(),
                    'description' => 'Reversal: ' . $ledger->description,
                    'debit' => $ledger->credit,
                    'credit' => $ledger->debit,
                    'balance' => $this->calculateNewBalance($purchase->vendor_id, $ledger->debit ?: $ledger->credit, $ledger->debit > 0 ? 'credit' : 'debit'),
                    'user_id' => auth()->id(),
                ]);
                $vendor = Vendor::find($purchase->vendor_id);
                $reversalLedger->ledgerable()->associate($vendor);
                $reversalLedger->save();
            }

            // 3. Delete transactions/payments
            foreach ($purchase->transactions as $transaction) {
                $transaction->delete();
            }

            // 4. Delete the purchase (soft delete)
            $purchase->delete();

            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'Purchase deleted and all effects reversed.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Purchase Delete Error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error deleting purchase: ' . $e->getMessage()]);
        }
    }

    public function exportPdf(Request $request)
    {
        $query = Purchase::with('vendor');
        if ($request->filled('vendor_id'))
            $query->where('vendor_id', $request->vendor_id);
        if ($request->filled('from_date'))
            $query->whereDate('purchase_date', '>=', $request->from_date);
        if ($request->filled('to_date'))
            $query->whereDate('purchase_date', '<=', $request->to_date);
        $purchases = $query->latest('purchase_date')->get();

        $pdf = PDF::loadView('exports.purchases', [
            'purchases' => $purchases,
            'title' => 'Purchases Report',
            'filters' => array_filter([
                $request->from_date ? 'From: ' . $request->from_date : null,
                $request->to_date ? 'To: ' . $request->to_date : null,
            ]),
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('purchases-report.pdf');
    }

    public function exportCsv(Request $request)
    {
        $query = Purchase::with('vendor');
        if ($request->filled('vendor_id'))
            $query->where('vendor_id', $request->vendor_id);
        if ($request->filled('from_date'))
            $query->whereDate('purchase_date', '>=', $request->from_date);
        if ($request->filled('to_date'))
            $query->whereDate('purchase_date', '<=', $request->to_date);
        $purchases = $query->latest('purchase_date')->get();

        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="purchases-report.csv"'];
        $callback = function () use ($purchases) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['#', 'Invoice No', 'Vendor', 'Date', 'Total', 'Discount', 'Net Amount']);
            foreach ($purchases as $i => $p) {
                fputcsv($file, [$i + 1, $p->invoice_no, $p->vendor->name ?? '', $p->purchase_date, $p->total_amount, $p->discount_amount, $p->net_amount]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

}
