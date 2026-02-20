<?php

namespace App\Http\Controllers;

use App\Models\SalesReturn;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\Batch;
use App\Models\BatchStock;
use App\Models\InventoryTransaction;
use App\Models\Transaction;
use App\Models\LedgerEntry;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalesReturnController extends Controller
{
    /**
     * Display a listing of the sales returns.
     */
    public function index()
    {
        $salesReturns = SalesReturn::with(['sale.customer'])->latest()->paginate(15);
        return view('sales_returns.index', compact('salesReturns'));
    }

    /**
     * Show the form for creating a new sales return.
     */
    public function create()
    {
        $sales = Sale::with('customer', 'saleItems.product')->get();
        $paymentMethods = PaymentMethod::all();
        return view('sales_returns.create', compact('sales', 'paymentMethods'));
    }

    /**
     * Store a newly created sales return in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'qty_returned' => 'required|integer|min:1',
            'return_reason' => 'nullable|string',
            'refund_amount' => 'required|numeric|min:0',
        ]);

        $sale = Sale::with('saleItems')->findOrFail($request->sale_id);

        DB::beginTransaction();
        try {
            // 1. Create the sales return record
            $salesReturn = SalesReturn::create([
                'sale_id' => $sale->id,
                'qty_returned' => $request->qty_returned,
                'return_reason' => $request->return_reason,
                'refund_amount' => $request->refund_amount,
            ]);

            // 2. Restore stock — distribute returned qty across sale items (FIFO by id)
            $remainingQty = $request->qty_returned;

            foreach ($sale->saleItems as $item) {
                if ($remainingQty <= 0)
                    break;

                $qtyToReturn = min($item->quantity, $remainingQty);

                // Find batch and restock
                $batch = Batch::where('product_id', $item->product_id)
                    ->where('batch_no', $item->batch_no)
                    ->first();

                if ($batch) {
                    $batchStock = BatchStock::where('batch_id', $batch->id)
                        ->where('location_id', $item->location_id)
                        ->first();

                    if ($batchStock) {
                        $batchStock->increment('quantity', $qtyToReturn);
                    } else {
                        BatchStock::create([
                            'batch_id' => $batch->id,
                            'product_id' => $item->product_id,
                            'location_id' => $item->location_id,
                            'quantity' => $qtyToReturn,
                            'purchase_price' => $item->purchase_price,
                            'sale_price' => $item->sale_price,
                        ]);
                    }

                    // Create inventory transaction for the return
                    InventoryTransaction::create([
                        'product_id' => $item->product_id,
                        'location_id' => $item->location_id,
                        'batch_id' => $batch->id,
                        'quantity' => $qtyToReturn,
                        'user_id' => auth()->id(),
                        'transactionable_id' => $salesReturn->id,
                        'transactionable_type' => SalesReturn::class,
                    ]);
                }

                $remainingQty -= $qtyToReturn;
            }

            // 3. Create ledger entry (credit to customer — refund reduces their receivable)
            $customer = Customer::find($sale->customer_id);
            if ($customer) {
                $newBalance = $this->calculateNewBalance($sale->customer_id, $request->refund_amount, 'credit');

                $ledger = new LedgerEntry([
                    'transaction_id' => null,
                    'date' => now(),
                    'description' => 'Sales Return - Invoice #' . $sale->invoice_no,
                    'debit' => 0,
                    'credit' => $request->refund_amount,
                    'balance' => $newBalance,
                    'user_id' => auth()->id(),
                ]);
                $ledger->ledgerable()->associate($customer);
                $ledger->save();
            }

            DB::commit();
            return redirect()->route('sales-returns.index')->with('success', 'Sales return created with stock and ledger effects.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Sales Return Store Error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error creating sales return: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified sales return.
     */
    public function show(SalesReturn $salesReturn)
    {
        $salesReturn->load(['sale.customer', 'sale.saleItems.product']);
        return view('sales_returns.show', compact('salesReturn'));
    }

    /**
     * Show the form for editing the specified sales return.
     */
    public function edit(SalesReturn $salesReturn)
    {
        $sales = Sale::with('customer', 'saleItems.product')->get();
        return view('sales_returns.edit', compact('salesReturn', 'sales'));
    }

    /**
     * Update the specified sales return in storage.
     */
    public function update(Request $request, SalesReturn $salesReturn)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'qty_returned' => 'required|integer|min:1',
            'return_reason' => 'nullable|string',
            'refund_amount' => 'required|numeric|min:0',
        ]);

        $salesReturn->update($request->all());

        return redirect()->route('sales-returns.index')->with('success', 'Sales return updated successfully.');
    }

    /**
     * Remove the specified sales return from storage (with reversal).
     */
    public function destroy(SalesReturn $salesReturn)
    {
        $sale = Sale::with('saleItems')->find($salesReturn->sale_id);

        DB::beginTransaction();
        try {
            // Reverse stock restoration done by this return
            if ($sale) {
                $remainingQty = $salesReturn->qty_returned;

                foreach ($sale->saleItems as $item) {
                    if ($remainingQty <= 0)
                        break;

                    $qtyToReverse = min($item->quantity, $remainingQty);

                    $batch = Batch::where('product_id', $item->product_id)
                        ->where('batch_no', $item->batch_no)
                        ->first();

                    if ($batch) {
                        $batchStock = BatchStock::where('batch_id', $batch->id)
                            ->where('location_id', $item->location_id)
                            ->first();

                        if ($batchStock) {
                            $batchStock->decrement('quantity', $qtyToReverse);
                            if ($batchStock->quantity <= 0) {
                                $batchStock->delete();
                            }
                        }
                    }

                    $remainingQty -= $qtyToReverse;
                }

                // Reverse ledger entry
                $customer = Customer::find($sale->customer_id);
                if ($customer) {
                    $newBalance = $this->calculateNewBalance($sale->customer_id, $salesReturn->refund_amount, 'debit');

                    $ledger = new LedgerEntry([
                        'transaction_id' => null,
                        'date' => now(),
                        'description' => 'Reversal: Sales Return - Invoice #' . $sale->invoice_no,
                        'debit' => $salesReturn->refund_amount,
                        'credit' => 0,
                        'balance' => $newBalance,
                        'user_id' => auth()->id(),
                    ]);
                    $ledger->ledgerable()->associate($customer);
                    $ledger->save();
                }
            }

            $salesReturn->delete();

            DB::commit();
            return redirect()->route('sales-returns.index')->with('success', 'Sales return deleted and effects reversed.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Sales Return Delete Error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error deleting sales return: ' . $e->getMessage()]);
        }
    }

    /**
     * Calculate the new ledger balance for a customer.
     */
    protected function calculateNewBalance($customerId, $amount, $type)
    {
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
}
