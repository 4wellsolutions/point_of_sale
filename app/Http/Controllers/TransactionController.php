<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\PaymentMethod;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;
use DB;
use Auth;

class TransactionController extends Controller
{
    /**
     * Display a listing of the transactions.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Eager load paymentMethod and transactionable relationships for efficiency
        $transactions = Transaction::with(['paymentMethod', 'transactionable'])
                            ->latest()
                            ->get();

        return view('transactions.index', compact('transactions'));
    }

    /**
     * Show the form for creating a new transaction.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Retrieve all payment methods, customers, and vendors to populate the form
        $paymentMethods = PaymentMethod::all();
        $customers = Customer::all();
        $vendors = Vendor::all();

        return view('transactions.create', compact('paymentMethods', 'customers', 'vendors'));
    }

    /**
     * Store a newly created transaction in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
	{
	    // Validate the incoming request data
	    $validatedData = $request->validate([
	        'payment_method_id' => 'required|exists:payment_methods,id',
	        'amount' => 'required|numeric|min:0.01',
	        'transaction_type' => 'required|in:credit,debit',
	        'transaction_date' => 'required|date',
	        'transactionable_type' => 'required|in:customer,vendor',
	        'transactionable_id' => [
	            'required',
	            function ($attribute, $value, $fail) use ($request) {
	                if ($request->transactionable_type === 'customer') {
	                    if (!Customer::where('id', $value)->exists()) {
	                        $fail('The selected customer is invalid.');
	                    }
	                } elseif ($request->transactionable_type === 'vendor') {
	                    if (!Vendor::where('id', $value)->exists()) {
	                        $fail('The selected vendor is invalid.');
	                    }
	                }
	            },
	        ],
	    ]);

	    // Determine the fully qualified class name based on the transactionable_type
	    $transactionableType = $request->transactionable_type === 'customer'
	        ? Customer::class
	        : Vendor::class;

	    DB::beginTransaction(); // Start the transaction

	    try {
	        // Create the transaction using the polymorphic relationship
	        $transaction = Transaction::create([
	            'payment_method_id'    => $validatedData['payment_method_id'],
	            'amount'               => $validatedData['amount'],
	            'transaction_type'     => $validatedData['transaction_type'],
	            'transaction_date'     => $validatedData['transaction_date'],
	            'transactionable_id'   => $validatedData['transactionable_id'],
	            'transactionable_type' => $transactionableType,
                'user_id'              => Auth::User()->id,

	        ]);

	        // Update the ledger
	        $debit     = $transaction->transaction_type === 'debit' ? $transaction->amount : 0;
	        $credit    = $transaction->transaction_type === 'credit' ? $transaction->amount : 0;

	        // Fetch the last ledger entry to calculate the new balance
	        $lastLedgerEntry = LedgerEntry::where('ledgerable_id', $validatedData['transactionable_id'])
	            ->where('ledgerable_type', $transactionableType)
	            ->latest('date')
	            ->first();

	        $lastBalance = $lastLedgerEntry ? $lastLedgerEntry->balance : 0;
	        $newBalance = $lastBalance + $credit - $debit;

	        // Create a new ledger entry
	        LedgerEntry::create([
	            'ledgerable_id'    => $validatedData['transactionable_id'],
	            'ledgerable_type'  => $transactionableType,
	            'transaction_id'   => $transaction->id,
	            'date'             => $transaction->transaction_date,
	            'description'      => 'Transaction recorded',
	            'debit'            => $debit,
	            'credit'           => $credit,
	            'balance'          => $newBalance,
                'user_id'          => Auth::User()->id,
	        ]);

	        DB::commit(); // Commit the transaction
	    } catch (\Exception $e) {
	        DB::rollBack(); // Rollback the transaction on failure
	        return redirect()->back()->withErrors(['error' => 'An error occurred while processing the transaction: ' . $e->getMessage()]);
	    }

	    // Redirect to the transactions index with a success message
	    return redirect()->route('transactions.index')
	                     ->with('success', 'Transaction added successfully.');
	}

    /**
     * Show the form for editing the specified transaction.
     *
     * @param  int  $id  The ID of the transaction to edit
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        // Find the transaction or fail with 404
        $transaction = Transaction::findOrFail($id);

        // Retrieve all payment methods, customers, and vendors to populate the form
        $paymentMethods = PaymentMethod::all();
        $customers = Customer::all();
        $vendors = Vendor::all();

        return view('transactions.edit', compact('transaction', 'paymentMethods', 'customers', 'vendors'));
    }

    /**
     * Update the specified transaction in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  The ID of the transaction to update
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Find the transaction or fail with 404
        $transaction = Transaction::findOrFail($id);

        // Validate the incoming request data
        $validatedData = $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0.01',
            'transaction_type' => 'required|in:credit,debit',
            'transaction_date' => 'required|date',
            'transactionable_type' => 'required|in:customer,vendor',
            'transactionable_id' => [
                'required',
                // Custom validation rule to ensure transactionable_id exists in the selected type
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->transactionable_type === 'customer') {
                        if (!Customer::where('id', $value)->exists()) {
                            $fail('The selected customer is invalid.');
                        }
                    } elseif ($request->transactionable_type === 'vendor') {
                        if (!Vendor::where('id', $value)->exists()) {
                            $fail('The selected vendor is invalid.');
                        }
                    }
                },
            ],
        ]);

        // Determine the fully qualified class name based on the transactionable_type
        $transactionableType = $request->transactionable_type === 'customer'
            ? Customer::class
            : Vendor::class;

        // Update the transaction with the validated data
        $transaction->update([
            'payment_method_id' => $validatedData['payment_method_id'],
            'amount' => $validatedData['amount'],
            'transaction_type' => $validatedData['transaction_type'],
            'transaction_date' => $validatedData['transaction_date'],
            'transactionable_id' => $validatedData['transactionable_id'],
            'transactionable_type' => $transactionableType,
        ]);

        // Redirect to the transactions index with a success message
        return redirect()->route('transactions.index')
                         ->with('success', 'Transaction updated successfully.');
    }

    /**
     * Remove the specified transaction from storage.
     *
     * @param  int  $id  The ID of the transaction to delete
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // Find the transaction or fail with 404
        $transaction = Transaction::findOrFail($id);

        // Delete the transaction
        $transaction->delete();

        // Redirect to the transactions index with a success message
        return redirect()->route('transactions.index')
                         ->with('success', 'Transaction deleted successfully.');
    }
}
