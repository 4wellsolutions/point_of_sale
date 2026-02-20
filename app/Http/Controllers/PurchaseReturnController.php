<?php

namespace App\Http\Controllers;

use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\Batch;
use App\Models\InventoryTransaction;
use App\Models\LedgerEntry;
use App\Models\Transaction;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class PurchaseReturnController extends Controller
{
    /**
     * Display a listing of the purchase returns.
     */
    public function index()
    {
        // Eager load vendor and return items relationships for efficiency
        $purchaseReturns = PurchaseReturn::with(['vendor', 'returnItems.product'])->paginate(20);
        return view('purchase_returns.index', compact('purchaseReturns'));
    }

    /**
     * Show the form for creating a new purchase return.
     */
    public function create()
    {
        $invoice_no = $this->generateReturnNo();
        return view('purchase_returns.create',compact("invoice_no"));
    }

    /**
     * Generate a new return number in the format '000001', '000002', etc.
     */
    public function generateReturnNo()
    {
        // Get the latest return number
        $latestReturn = PurchaseReturn::orderBy('id', 'desc')->first();
        if (!$latestReturn) {
            $newReturnNo = '000001';
        } else {
            // Extract the numeric part and increment
            $latestReturnNo = $latestReturn->invoice_no;
            $numericPart = intval($latestReturnNo);
            $newNumericPart = $numericPart + 1;
            $newReturnNo = str_pad($newNumericPart, 6, '0', STR_PAD_LEFT);

        }

        return $newReturnNo;
    }

    /**
     * Store a newly created purchase return in storage via AJAX.
     */
    public function store(Request $request)
    {
        // Step 1: Define validation rules
        $rules = [
            'purchase_id'                => 'required|exists:purchases,id',
            'invoice_no'                 => 'required|unique:purchase_returns,invoice_no',
            'return_date'                => 'required|date',
            'discount_amount'            => 'required|numeric|min:0',
            'notes'                      => 'nullable|string|max:1000',

            'return_items'               => 'required|array|min:1',
            'return_items.*.purchase_item_id' => 'required|exists:purchase_items,id',
            'return_items.*.product_id'        => 'required|exists:products,id',
            'return_items.*.quantity'          => 'required|integer|min:1',
            'return_items.*.unit_price'        => 'required|numeric|min:0',

            'total_amount'      => 'required|numeric|min:0',
            'net_amount'        => 'required|numeric|min:0',

            'payment_methods'                      => 'nullable|array',
            'payment_methods.*.payment_method_id' => 'required_with:payment_methods.*.amount|exists:payment_methods,id',
            'payment_methods.*.amount'            => 'required_with:payment_methods.*.payment_method_id|numeric|min:0.01',
        ];

        // Step 2: Define custom error messages
        $messages = [
            'purchase_id.required'                => 'Purchase ID is required.',
            'purchase_id.exists'                  => 'The selected purchase does not exist.',
            'invoice_no.required'                 => 'Invoice number is required.',
            'invoice_no.unique'                   => 'This invoice number has already been used.',
            'return_date.required'                => 'Please select a return date.',
            'return_date.date'                    => 'Return date must be a valid date.',
            'discount_amount.required'            => 'Discount amount is required.',
            'discount_amount.numeric'             => 'Discount amount must be a number.',
            'discount_amount.min'                 => 'Discount amount cannot be negative.',
            'notes.string'                        => 'Notes must be a valid text.',
            'notes.max'                           => 'Notes cannot exceed 1000 characters.',
            'return_items.required'               => 'Please add at least one product to the return.',
            'return_items.array'                  => 'Return items must be an array.',
            'return_items.min'                    => 'Please add at least one product to the return.',
            'return_items.*.purchase_item_id.required' => 'Please select the original purchase item.',
            'return_items.*.purchase_item_id.exists'   => 'Selected purchase item does not exist.',
            'return_items.*.product_id.required'       => 'Please select a product.',
            'return_items.*.product_id.exists'         => 'Selected product does not exist.',
            'return_items.*.quantity.required'         => 'Please enter the quantity to return.',
            'return_items.*.quantity.integer'          => 'Quantity must be an integer.',
            'return_items.*.quantity.min'              => 'Quantity must be at least 1.',
            'return_items.*.unit_price.required'       => 'Please enter the unit price.',
            'return_items.*.unit_price.numeric'        => 'Unit price must be a number.',
            'return_items.*.unit_price.min'            => 'Unit price cannot be negative.',
            'total_amount.required'     => 'Total amount is required.',
            'total_amount.numeric'      => 'Total amount must be a number.',
            'total_amount.min'          => 'Total amount cannot be negative.',
            'net_amount.required'       => 'Net amount is required.',
            'net_amount.numeric'        => 'Net amount must be a number.',
            'net_amount.min'            => 'Net amount cannot be negative.',
            'payment_methods.array'                         => 'Payment methods must be an array.',
            'payment_methods.*.payment_method_id.required_with' => 'Please select a payment method.',
            'payment_methods.*.payment_method_id.exists'   => 'Selected payment method does not exist.',
            'payment_methods.*.amount.required_with'            => 'Please enter the payment amount.',
            'payment_methods.*.amount.numeric'             => 'Payment amount must be a number.',
            'payment_methods.*.amount.min'                 => 'Payment amount must be at least 0.01.',
        ];

        // Step 3: Create validator instance
        $validator = Validator::make($request->all(), $rules, $messages);

        // Step 4: After validation to check:
        //    - Sum of return items matches total_amount and net_amount
        //    - Ensure that the return quantity does not exceed the original purchase quantity minus already returned quantities
        $validator->after(function ($validator) use ($request) {
            if (is_array($request->return_items)) {
                $calculatedTotal = 0;
                foreach ($request->return_items as $item) {
                    $calculatedTotal += ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0);
                }

                $submittedTotal = round($request->total_amount, 2);
                $expectedTotal  = round($calculatedTotal, 2);

                if ($submittedTotal !== $expectedTotal) {
                    $validator->errors()->add('total_amount', 'The total amount does not match the sum of all return items.');
                }

                $calculatedNet = $calculatedTotal - ($request->discount_amount ?? 0);
                $submittedNet  = round($request->net_amount, 2);
                $expectedNet   = round($calculatedNet, 2);

                if ($submittedNet !== $expectedNet) {
                    $validator->errors()->add('net_amount', 'The net amount does not match the total amount minus the discount.');
                }

                // Additional Validation: Ensure return quantities do not exceed original purchase quantities
                foreach ($request->return_items as $item) {
                    $purchaseItem = PurchaseItem::find($item['purchase_item_id']);
                    if ($purchaseItem) {
                        $totalReturned = PurchaseReturnItem::where('purchase_item_id', $purchaseItem->id)->sum('quantity');
                        $availableToReturn = $purchaseItem->quantity - $totalReturned;
                        if ($item['quantity'] > $availableToReturn) {
                            $validator->errors()->add('return_items.*.quantity', "Return quantity for product exceeds the available quantity to return.");
                        }
                    }
                }
            }
        });

        // Step 5: Check validation
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'There were some errors with your submission.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Step 6: Proceed to store the purchase return and related data
        // try {
            DB::beginTransaction();

            // Recalculate totals on the server side
            $calculatedTotal = 0;
            foreach ($request->return_items as $item) {
                $calculatedTotal += $item['quantity'] * $item['unit_price'];
            }

            $calculatedDiscount = $request->discount_amount ?? 0;
            $calculatedNet = $calculatedTotal - $calculatedDiscount;

            $purchase = Purchase::findOrFail($request->purchase_id);

            // Create the purchase return
            $purchaseReturn = PurchaseReturn::create([
                'purchase_id'     => $request->purchase_id,
                'invoice_no'      => $request->invoice_no,
                'vendor_id'       => $purchase->vendor_id,
                'return_date'     => $request->return_date,
                'discount_amount' => $calculatedDiscount,
                'notes'           => $request->notes,
                'total_amount'    => $calculatedTotal,
                'net_amount'      => $calculatedNet,
                'user_id'         => auth()->id(),
            ]);

            // Iterate through each return item and create PurchaseReturnItem records
            foreach ($request->return_items as $item) {
                $itemTotal = $item['quantity'] * $item['unit_price'];

                // Fetch the original purchase item
                $purchaseItem = PurchaseItem::findOrFail($item['purchase_item_id']);

                // Update the purchase item to reflect the returned quantity
                $purchaseItem->quantity = ($purchaseItem->quantity ?? 0) + $item['quantity'];
                $purchaseItem->save();

                // Update the batch quantity
                if ($purchaseItem->batch_no) {
                    $batch = Batch::where('product_id', $item['product_id'])
                                  ->where('batch_no', $purchaseItem->batch_no)
                                  ->first();

                    if ($batch) {
                        // Get the batch stock for the current batch
                        $batchStock = $batch->stock; // Assuming the relationship is defined with batch_stock

                        if ($batchStock && $batchStock->quantity > 0) {
                            // Skip expired batches
                            if ($batch->expiry_date && $batch->expiry_date < now()->toDateString()) {
                                // Skip expired batch or handle differently (optional)
                                continue;
                            }

                            // Decrement the batch stock quantity based on the return quantity
                            $batchStock->decrement('quantity', $item['quantity']);

                            // Optional: Delete the batch stock if quantity becomes zero
                            if ($batchStock->quantity <= 0) {
                                $batchStock->delete();
                            }
                        } else {
                            // Handle the case where batch stock is not found or quantity is zero
                            // You might want to log or handle this scenario
                        }
                    }
                }


                // Create purchase return item
                PurchaseReturnItem::create([
                    'purchase_return_id' => $purchaseReturn->id,
                    'purchase_item_id'   => $item['purchase_item_id'],
                    'product_id'         => $item['product_id'],
                    'quantity'           => $item['quantity'],
                    'unit_price'         => $item['unit_price'],
                    'total_amount'       => $itemTotal,
                ]);

                // Log the inventory transaction (reverse the purchase)
                InventoryTransaction::create([
                    'product_id'               => $item['product_id'],
                    'batch_id'                 => $batch->id ?? null,
                    'qty_change'               => -$item['quantity'], // Negative for return
                    'transaction_type'         => 'purchase_return',
                    'transaction_reference_id' => $purchaseReturn->id,
                    'user_id'                  => auth()->id(),
                ]);
            }

            // Step 7: Create Ledger Entry for the Purchase Return (Credit)
            $purchaseReturnLedger = new LedgerEntry([
                'transaction_id' => null, // To be associated later if needed
                'date'           => now(),
                'description'    => 'Purchase Return Invoice #' . $purchaseReturn->invoice_no,
                'debit'          => 0,
                'credit'         => $calculatedNet + $calculatedDiscount, // Money coming in
                'balance'        => $this->calculateNewBalance($purchase->vendor_id, $calculatedNet + $calculatedDiscount, 'credit'),
                'vendor_id'      => $purchase->vendor_id,
                'customer_id'    => null,
            ]);

            // Associate the ledger with the Vendor
            $purchaseReturnLedger->ledgerable()->associate($purchaseReturn);
            $purchaseReturnLedger->save();

            // Step 8: Handle Payment Methods only if provided
            if (is_array($request->payment_methods) && count($request->payment_methods) > 0) {
                foreach ($request->payment_methods as $payment) {

                    // Create Transaction (Debit) for each payment method used in the return
                    $transaction = Transaction::create([
                        'payment_method_id'    => $payment['payment_method_id'],
                        'vendor_id'            => $purchase->vendor_id,
                        'customer_id'          => null,
                        'amount'               => $payment['amount'],
                        'transactionable_id'   => $purchaseReturn->id,
                        'transactionable_type' => PurchaseReturn::class,
                        'transaction_type'     => 'debit', // Debit for payment in return
                        'transaction_date'     => now(),
                    ]);

                    // Create Ledger Entry for the Payment (Debit)
                    $paymentLedger = new LedgerEntry([
                        'transaction_id' => $transaction->id,
                        'date'           => now(),
                        'description'    => 'Payment for Purchase Return Invoice #' . $purchaseReturn->invoice_no,
                        'debit'          => $payment['amount'], // Money going out
                        'credit'         => 0,
                        'balance'        => $this->calculateNewBalance($purchase->vendor_id, $payment['amount'], 'debit'),
                        'vendor_id'      => $purchase->vendor_id,
                        'customer_id'    => null,
                    ]);

                    // Associate the ledger with the Vendor
                    $paymentLedger->ledgerable()->associate($purchase->vendor);
                    $paymentLedger->save();
                }
            }

            // Step 9: Commit the transaction
            DB::commit();

            // Step 10: Return success response
            return response()->json([
                'success' => true,
                'message' => 'Purchase return has been successfully saved.',
                'redirect' => route('purchase-returns.index')
            ], 200);

        // } catch (\Exception $e) {
        //     // Step 11: Rollback the transaction on error
        //     DB::rollBack();

        //     // Log the error for debugging
        //     \Log::error('Purchase Return Store Error: ' . $e->getMessage());

        //     // Return error response
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'An unexpected error occurred while saving the purchase return. Please try again later.',
        //     ], 500);
        // }
    }


    /**
     * Calculate the new balance for the vendor in the ledger.
     *
     * @param int $vendorId
     * @param float $amount
     * @param string $type ('debit' or 'credit')
     * @return float
     */
    public function calculateNewBalance($vendorId, $amount, $type)
    {
        // Fetch the latest ledger entry for the vendor to get the current balance
        $latestLedger = LedgerEntry::where('ledgerable_type', \App\Models\Vendor::class)
                              ->where('ledgerable_id', $vendorId)
                              ->orderBy('date', 'desc')
                              ->first();

        $previousBalance = $latestLedger ? $latestLedger->balance : 0;

        // Update balance based on transaction type
        if ($type === 'debit') {
            return $previousBalance + $amount;
        } elseif ($type === 'credit') {
            return $previousBalance - $amount;
        }

        // Default to previous balance if type is unrecognized
        return $previousBalance;
    }

    /**
     * Display the specified purchase return.
     */
    public function show(PurchaseReturn $purchaseReturn)
    {
        // Eager load vendor, return items, and related purchase
        $purchaseReturn->load(['vendor', 'returnItems.product', 'purchase','ledgerEntries']);
        return view('purchase_returns.show', compact('purchaseReturn'));
    }

    /**
     * Show the form for editing the specified purchase return.
     */
    public function edit(PurchaseReturn $purchaseReturn)
    {
        $purchaseReturn->load(['returnItems']);
        $purchases = Purchase::with(['vendor', 'purchaseItems'])->get();
        return view('purchase_returns.edit', compact('purchaseReturn', 'purchases'));
    }

    /**
     * Update the specified purchase return in storage via AJAX.
     */
    public function update(Request $request, PurchaseReturn $purchaseReturn)
    {
        // Define validation rules
        $rules = [
            // Purchase Return Information
            'purchase_id'                => 'required|exists:purchases,id',
            'return_no'                  => 'required|unique:purchase_returns,return_no,' . $purchaseReturn->id,
            'return_date'                => 'required|date',
            'discount_amount'            => 'required|numeric|min:0',
            'notes'                      => 'nullable|string|max:1000',

            // Return Items
            'return_items'               => 'required|array|min:1',
            'return_items.*.purchase_item_id' => 'required|exists:purchase_items,id',
            'return_items.*.product_id'        => 'required|exists:products,id',
            'return_items.*.batch_no'          => 'required|string|max:255',
            'return_items.*.quantity'          => 'required|integer|min:1',
            'return_items.*.unit_price'        => 'required|numeric|min:0',
            
            // Calculated Fields
            'total_amount'      => 'required|numeric|min:0',
            'net_amount'        => 'required|numeric|min:0',

            // Payment Methods - Made Optional
            'payment_methods'                      => 'nullable|array',
            'payment_methods.*.payment_method_id' => 'required_with:payment_methods.*.amount|exists:payment_methods,id',
            'payment_methods.*.amount'            => 'required_with:payment_methods.*.payment_method_id|numeric|min:0.01',
        ];

        // Define custom error messages
        $messages = [
            // Purchase Return Information
            'purchase_id.required'                => 'Please select a purchase to return.',
            'purchase_id.exists'                  => 'The selected purchase does not exist.',
            'return_no.required'                  => 'Return number is required.',
            'return_no.unique'                    => 'This return number has already been used. Please refresh to get a new one.',
            'return_date.required'                => 'Please select a return date.',
            'return_date.date'                    => 'Return date must be a valid date.',
            'discount_amount.required'            => 'Discount amount is required.',
            'discount_amount.numeric'             => 'Discount amount must be a number.',
            'discount_amount.min'                 => 'Discount amount cannot be negative.',
            'notes.string'                        => 'Notes must be a valid text.',
            'notes.max'                           => 'Notes cannot exceed 1000 characters.',

            // Return Items
            'return_items.required'               => 'Please add at least one product to the return.',
            'return_items.array'                  => 'Return items must be an array.',
            'return_items.min'                    => 'Please add at least one product to the return.',
            'return_items.*.purchase_item_id.required' => 'Please select the original purchase item.',
            'return_items.*.purchase_item_id.exists'   => 'Selected purchase item does not exist.',
            'return_items.*.product_id.required'       => 'Please select a product.',
            'return_items.*.product_id.exists'         => 'Selected product does not exist.',
            'return_items.*.batch_no.required'         => 'Please enter the batch number.',
            'return_items.*.batch_no.string'           => 'Batch number must be a valid string.',
            'return_items.*.batch_no.max'              => 'Batch number cannot exceed 255 characters.',
            'return_items.*.quantity.required'         => 'Please enter the quantity to return.',
            'return_items.*.quantity.integer'          => 'Quantity must be an integer.',
            'return_items.*.quantity.min'              => 'Quantity must be at least 1.',
            'return_items.*.unit_price.required'       => 'Please enter the unit price.',
            'return_items.*.unit_price.numeric'        => 'Unit price must be a number.',
            'return_items.*.unit_price.min'            => 'Unit price cannot be negative.',

            // Calculated Fields
            'total_amount.required'     => 'Total amount is required.',
            'total_amount.numeric'      => 'Total amount must be a number.',
            'total_amount.min'          => 'Total amount cannot be negative.',
            'net_amount.required'       => 'Net amount is required.',
            'net_amount.numeric'        => 'Net amount must be a number.',
            'net_amount.min'            => 'Net amount cannot be negative.',

            // Payment Methods - Updated Messages
            'payment_methods.array'                         => 'Payment methods must be an array.',
            'payment_methods.*.payment_method_id.required_with' => 'Please select a payment method.',
            'payment_methods.*.payment_method_id.exists'   => 'Selected payment method does not exist.',
            'payment_methods.*.amount.required_with'            => 'Please enter the payment amount.',
            'payment_methods.*.amount.numeric'             => 'Payment amount must be a number.',
            'payment_methods.*.amount.min'                 => 'Payment amount must be at least 0.01.',
        ];

        // Create validator instance
        $validator = Validator::make($request->all(), $rules, $messages);

        // After validation to check:
        // 1. Sum of return items matches total_amount and net_amount
        $validator->after(function ($validator) use ($request) {
            if (is_array($request->return_items)) {
                $calculatedTotal = 0;
                foreach ($request->return_items as $item) {
                    $calculatedTotal += ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0);
                }

                $submittedTotal = round($request->total_amount, 2);
                $expectedTotal  = round($calculatedTotal, 2);

                if ($submittedTotal !== $expectedTotal) {
                    $validator->errors()->add('total_amount', 'The total amount does not match the sum of all return items.');
                }

                $calculatedNet      = $calculatedTotal - ($request->discount_amount ?? 0);
                $submittedNet       = round($request->net_amount, 2);
                $expectedNet        = round($calculatedNet, 2);

                if ($submittedNet !== $expectedNet) {
                    $validator->errors()->add('net_amount', 'The net amount does not match the total amount minus the discount.');
                }
            }
        });

        // Check validation
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'There were some errors with your submission.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Proceed to update the purchase return and related data
        try {
            DB::beginTransaction();

            // Recalculate totals on the server side
            $calculatedTotal = 0;
            foreach ($request->return_items as $item) {
                $calculatedTotal += $item['quantity'] * $item['unit_price'];
            }

            $calculatedDiscount = $request->discount_amount ?? 0;
            $calculatedNet = $calculatedTotal - $calculatedDiscount;

            // Update the purchase return record
            $purchaseReturn->update([
                'purchase_id'     => $request->purchase_id,
                'vendor_id'       => $request->vendor_id,
                'return_no'       => $request->return_no,
                'return_date'     => $request->return_date,
                'discount_amount' => $calculatedDiscount,
                'notes'           => $request->notes,
                'total_amount'    => $calculatedTotal,
                'net_amount'      => $calculatedNet,
            ]);

            $totalAmount = 0;

            // Handle existing return items
            $existingItemIds = $purchaseReturn->returnItems()->pluck('id')->toArray();
            $newItemIds = [];

            foreach ($request->return_items as $item) {
                if (isset($item['id'])) {
                    // Update existing return item
                    $returnItem = $purchaseReturn->returnItems()->find($item['id']);
                    if ($returnItem) {
                        // Adjust batch total_qty_received if quantity has changed
                        if ($returnItem->quantity != $item['quantity']) {
                            $difference = $item['quantity'] - $returnItem->quantity;
                            $returnItem->batch->decrement('total_qty_received', $difference);
                        }

                        // Update batch details if necessary
                        $batch = Batch::where('product_id', $item['product_id'])
                                      ->where('batch_no', $item['batch_no'])
                                      ->first();

                        if ($batch) {
                            // Update batch total_qty_received
                            $batch->decrement('total_qty_received', $returnItem->quantity);
                            $batch->increment('total_qty_received', $item['quantity']);
                        } else {
                            // Handle the case where the batch does not exist
                            DB::rollBack();
                            return response()->json([
                                'success' => false,
                                'message' => 'Batch number ' . $item['batch_no'] . ' for product ' . $item['product']->name . ' does not exist.',
                            ], 422);
                        }

                        // Update purchase return item
                        $returnItem->update([
                            'purchase_item_id' => $item['purchase_item_id'],
                            'product_id'       => $item['product_id'],
                            'batch_no'         => $item['batch_no'],
                            'quantity'         => $item['quantity'],
                            'unit_price'       => $item['unit_price'],
                            'total_amount'     => $item['quantity'] * $item['unit_price'],
                        ]);

                        // Log the inventory transaction
                        InventoryTransaction::create([
                            'product_id'               => $item['product_id'],
                            'batch_id'                 => $batch->id,
                            'qty_change'               => -$item['quantity'],
                            'transaction_type'         => 'purchase_return_update',
                            'transaction_reference_id' => $purchaseReturn->id,
                            'user_id'                  => auth()->id(),
                        ]);

                        $totalAmount += $returnItem->total_amount;

                        $newItemIds[] = $returnItem->id;
                    }
                } else {
                    // Create new return item
                    $itemTotalAmount = $item['quantity'] * $item['unit_price'];

                    $totalAmount += $itemTotalAmount;

                    // Find existing batch
                    $batch = Batch::where('product_id', $item['product_id'])
                                  ->where('batch_no', $item['batch_no'])
                                  ->first();

                    if (!$batch) {
                        // Handle the case where the batch does not exist
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Batch number ' . $item['batch_no'] . ' for product ' . $item['product']->name . ' does not exist.',
                        ], 422);
                    }

                    // Update the total_qty_received in the batch
                    $batch->decrement('total_qty_received', $item['quantity']);

                    // Create the purchase return item record
                    $returnItem = $purchaseReturn->returnItems()->create([
                        'purchase_item_id' => $item['purchase_item_id'],
                        'product_id'       => $item['product_id'],
                        'batch_no'         => $item['batch_no'],
                        'quantity'         => $item['quantity'],
                        'unit_price'       => $item['unit_price'],
                        'total_amount'     => $itemTotalAmount,
                    ]);

                    // Log the inventory transaction
                    InventoryTransaction::create([
                        'product_id'               => $item['product_id'],
                        'batch_id'                 => $batch->id,
                        'qty_change'               => -$item['quantity'],
                        'transaction_type'         => 'purchase_return',
                        'transaction_reference_id' => $purchaseReturn->id,
                        'user_id'                  => auth()->id(),
                    ]);

                    $newItemIds[] = $returnItem->id;
                }
            }

            // Delete removed return items
            $itemsToDelete = array_diff($existingItemIds, $newItemIds);
            if (!empty($itemsToDelete)) {
                foreach ($itemsToDelete as $itemId) {
                    $returnItem = $purchaseReturn->returnItems()->find($itemId);
                    if ($returnItem) {
                        // Decrement batch total_qty_received
                        $returnItem->batch->increment('total_qty_received', $returnItem->quantity);

                        // Log the inventory transaction as a positive change
                        InventoryTransaction::create([
                            'product_id'               => $returnItem->product_id,
                            'batch_id'                 => $returnItem->batch_id,
                            'qty_change'               => $returnItem->quantity,
                            'transaction_type'         => 'purchase_return_delete',
                            'transaction_reference_id' => $purchaseReturn->id,
                            'user_id'                  => auth()->id(),
                        ]);

                        // Delete the purchase return item
                        $returnItem->delete();
                    }
                }
            }

            // Update total_amount in the purchase return record
            $purchaseReturn->update([
                'total_amount'   => $totalAmount,
                'net_amount'     => $totalAmount - $purchaseReturn->discount_amount,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase return updated successfully.',
                'redirect' => route('purchase_returns.index'),
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            // Log the error for debugging
            \Log::error('Purchase Return Update Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the purchase return.',
                'errors'  => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified purchase return from storage.
     */
    public function destroy(PurchaseReturn $purchaseReturn)
    {
        // Wrap operations in a transaction to ensure data integrity
        DB::beginTransaction();

        try {
            // Reverse inventory changes
            foreach ($purchaseReturn->returnItems as $item) {
                $batch = Batch::where('product_id', $item->product_id)
                              ->where('batch_no', $item->batch_no)
                              ->first();

                if ($batch) {
                    // Increment the stock back
                    $batch->increment('total_qty_received', $item->quantity);
                }

                // Log the inventory transaction as a reverse of the return
                InventoryTransaction::create([
                    'product_id'               => $item->product_id,
                    'batch_id'                 => $batch->id,
                    'qty_change'               => $item->quantity,
                    'transaction_type'         => 'purchase_return_delete',
                    'transaction_reference_id' => $purchaseReturn->id,
                    'user_id'                  => auth()->id(),
                ]);
            }

            // Optionally, delete related payments and ledger entries
            // This depends on your application's logic

            // Delete the purchase return record
            $purchaseReturn->delete();

            DB::commit();

            return redirect()->route('purchase_returns.index')->with('success', 'Purchase return deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            // Log the error or handle it as needed
            \Log::error('Purchase Return Delete Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'An error occurred while deleting the purchase return.']);
        }
    }
}
