<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\PaymentMethod;
use App\Models\Vendor;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use DB;
use Auth;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['paymentMethod', 'transactionable'])
            ->where('transaction_type', 'credit')
            ->whereHasMorph('transactionable', [Vendor::class]);

        if ($request->filled('vendor_id'))
            $query->where('transactionable_id', $request->vendor_id)
                ->where('transactionable_type', Vendor::class);
        if ($request->filled('payment_method_id'))
            $query->where('payment_method_id', $request->payment_method_id);
        if ($request->filled('from_date'))
            $query->whereDate('transaction_date', '>=', $request->from_date);
        if ($request->filled('to_date'))
            $query->whereDate('transaction_date', '<=', $request->to_date);

        $payments = $query->latest('transaction_date')->paginate(20);
        $paymentMethods = PaymentMethod::orderBy('name')->get();
        $vendors = Vendor::orderBy('name')->get();

        return view('payments.index', compact('payments', 'paymentMethods', 'vendors'));
    }

    public function create()
    {
        $paymentMethods = PaymentMethod::orderBy('name')->get();
        $vendors = Vendor::orderBy('name')->get();
        return view('payments.create', compact('paymentMethods', 'vendors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'payment_method_id' => $request->payment_method_id,
                'vendor_id' => $request->vendor_id,
                'customer_id' => null,
                'amount' => $request->amount,
                'transaction_type' => 'credit',
                'transaction_date' => $request->transaction_date,
                'transactionable_id' => $request->vendor_id,
                'transactionable_type' => Vendor::class,
                'user_id' => Auth::id(),
            ]);

            $lastLedger = LedgerEntry::where('ledgerable_id', $request->vendor_id)
                ->where('ledgerable_type', Vendor::class)
                ->latest('id')->first();
            $lastBalance = $lastLedger ? $lastLedger->balance : 0;

            LedgerEntry::create([
                'ledgerable_id' => $request->vendor_id,
                'ledgerable_type' => Vendor::class,
                'transaction_id' => $transaction->id,
                'date' => $request->transaction_date,
                'description' => 'Payment to vendor',
                'debit' => 0,
                'credit' => $request->amount,
                'balance' => $lastBalance - $request->amount,
                'user_id' => Auth::id(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error saving payment: ' . $e->getMessage()])->withInput();
        }

        return redirect()->route('payments.index')->with('success', 'Payment recorded successfully.');
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
                'description' => 'Reversal: Payment #' . $transaction->id . ' deleted',
                'debit' => $transaction->amount,
                'credit' => 0,
                'balance' => $lastBalance + $transaction->amount,
                'user_id' => Auth::id(),
            ]);

            $transaction->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error deleting payment: ' . $e->getMessage()]);
        }

        return redirect()->route('payments.index')->with('success', 'Payment deleted and ledger reversed.');
    }

    public function exportPdf(Request $request)
    {
        $query = Transaction::with(['paymentMethod', 'transactionable'])
            ->where('transaction_type', 'credit')
            ->whereHasMorph('transactionable', [Vendor::class]);

        if ($request->filled('payment_method_id'))
            $query->where('payment_method_id', $request->payment_method_id);
        if ($request->filled('from_date'))
            $query->whereDate('transaction_date', '>=', $request->from_date);
        if ($request->filled('to_date'))
            $query->whereDate('transaction_date', '<=', $request->to_date);

        $payments = $query->latest('transaction_date')->get();

        $pdf = Pdf::loadView('payments.pdf', compact('payments'))->setPaper('a4', 'landscape');
        return $pdf->stream('payments-report.pdf');
    }

    public function exportCsv(Request $request)
    {
        $query = Transaction::with(['paymentMethod', 'transactionable'])
            ->where('transaction_type', 'credit')
            ->whereHasMorph('transactionable', [Vendor::class]);

        if ($request->filled('payment_method_id'))
            $query->where('payment_method_id', $request->payment_method_id);
        if ($request->filled('from_date'))
            $query->whereDate('transaction_date', '>=', $request->from_date);
        if ($request->filled('to_date'))
            $query->whereDate('transaction_date', '<=', $request->to_date);

        $payments = $query->latest('transaction_date')->get();

        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="payments.csv"'];
        $callback = function () use ($payments) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['#', 'Date', 'Vendor', 'Payment Method', 'Amount']);
            foreach ($payments as $i => $p) {
                fputcsv($file, [
                    $i + 1,
                    $p->transaction_date,
                    $p->transactionable->name ?? 'N/A',
                    $p->paymentMethod->name ?? '',
                    $p->amount,
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
