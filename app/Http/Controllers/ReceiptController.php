<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\PaymentMethod;
use App\Models\Customer;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use DB;
use Auth;

class ReceiptController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['paymentMethod', 'transactionable'])
            ->where('transaction_type', 'credit')
            ->whereHasMorph('transactionable', [Customer::class]);

        if ($request->filled('customer_id'))
            $query->where('transactionable_id', $request->customer_id)
                ->where('transactionable_type', Customer::class);
        if ($request->filled('payment_method_id'))
            $query->where('payment_method_id', $request->payment_method_id);
        if ($request->filled('from_date'))
            $query->whereDate('transaction_date', '>=', $request->from_date);
        if ($request->filled('to_date'))
            $query->whereDate('transaction_date', '<=', $request->to_date);

        $receipts = $query->latest('transaction_date')->paginate(20);
        $paymentMethods = PaymentMethod::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();

        return view('receipts.index', compact('receipts', 'paymentMethods', 'customers'));
    }

    public function create()
    {
        $paymentMethods = PaymentMethod::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        return view('receipts.create', compact('paymentMethods', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'payment_method_id' => $request->payment_method_id,
                'customer_id' => $request->customer_id,
                'vendor_id' => null,
                'amount' => $request->amount,
                'transaction_type' => 'credit',
                'transaction_date' => $request->transaction_date,
                'transactionable_id' => $request->customer_id,
                'transactionable_type' => Customer::class,
                'user_id' => Auth::id(),
            ]);

            $lastLedger = LedgerEntry::where('ledgerable_id', $request->customer_id)
                ->where('ledgerable_type', Customer::class)
                ->latest('id')->first();

            $lastBalance = $lastLedger ? $lastLedger->balance : 0;

            LedgerEntry::create([
                'ledgerable_id' => $request->customer_id,
                'ledgerable_type' => Customer::class,
                'transaction_id' => $transaction->id,
                'date' => $request->transaction_date,
                'description' => 'Receipt from customer',
                'debit' => 0,
                'credit' => $request->amount,
                'balance' => $lastBalance - $request->amount,
                'user_id' => Auth::id(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error saving receipt: ' . $e->getMessage()])->withInput();
        }

        return redirect()->route('receipts.index')->with('success', 'Receipt recorded successfully.');
    }

    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);

        DB::beginTransaction();
        try {
            $lastLedger = LedgerEntry::where('ledgerable_id', $transaction->transactionable_id)
                ->where('ledgerable_type', $transaction->transactionable_type)
                ->latest('id')->first();
            $lastBalance = $lastLedger ? $lastLedger->balance : 0;

            LedgerEntry::create([
                'ledgerable_id' => $transaction->transactionable_id,
                'ledgerable_type' => $transaction->transactionable_type,
                'transaction_id' => $transaction->id,
                'date' => now(),
                'description' => 'Reversal: Receipt #' . $transaction->id . ' deleted',
                'debit' => $transaction->amount,
                'credit' => 0,
                'balance' => $lastBalance + $transaction->amount,
                'user_id' => Auth::id(),
            ]);

            $transaction->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error deleting receipt: ' . $e->getMessage()]);
        }

        return redirect()->route('receipts.index')->with('success', 'Receipt deleted and ledger reversed.');
    }

    public function exportPdf(Request $request)
    {
        $query = Transaction::with(['paymentMethod', 'transactionable'])
            ->where('transaction_type', 'credit')
            ->whereHasMorph('transactionable', [Customer::class]);

        if ($request->filled('payment_method_id'))
            $query->where('payment_method_id', $request->payment_method_id);
        if ($request->filled('from_date'))
            $query->whereDate('transaction_date', '>=', $request->from_date);
        if ($request->filled('to_date'))
            $query->whereDate('transaction_date', '<=', $request->to_date);

        $receipts = $query->latest('transaction_date')->get();

        $pdf = Pdf::loadView('receipts.pdf', compact('receipts'))->setPaper('a4', 'landscape');
        return $pdf->stream('receipts-report.pdf');
    }

    public function exportCsv(Request $request)
    {
        $query = Transaction::with(['paymentMethod', 'transactionable'])
            ->where('transaction_type', 'credit')
            ->whereHasMorph('transactionable', [Customer::class]);

        if ($request->filled('payment_method_id'))
            $query->where('payment_method_id', $request->payment_method_id);
        if ($request->filled('from_date'))
            $query->whereDate('transaction_date', '>=', $request->from_date);
        if ($request->filled('to_date'))
            $query->whereDate('transaction_date', '<=', $request->to_date);

        $receipts = $query->latest('transaction_date')->get();

        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="receipts.csv"'];
        $callback = function () use ($receipts) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['#', 'Date', 'Customer', 'Payment Method', 'Amount']);
            foreach ($receipts as $i => $r) {
                fputcsv($file, [
                    $i + 1,
                    $r->transaction_date,
                    $r->transactionable->name ?? 'N/A',
                    $r->paymentMethod->name ?? '',
                    $r->amount,
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
