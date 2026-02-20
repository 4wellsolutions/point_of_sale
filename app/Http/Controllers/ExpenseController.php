<?php
namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('expenseType');
        if ($request->filled('expense_type_id'))
            $query->where('expense_type_id', $request->expense_type_id);
        if ($request->filled('from_date'))
            $query->whereDate('date', '>=', $request->from_date);
        if ($request->filled('to_date'))
            $query->whereDate('date', '<=', $request->to_date);
        $expenses = $query->latest('date')->paginate(20);
        $expenseTypes = ExpenseType::orderBy('name')->get();
        return view('expenses.index', compact('expenses', 'expenseTypes'));
    }

    public function create()
    {
        $expenseTypes = ExpenseType::all();
        return view('expenses.create', compact('expenseTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_type_id' => 'required|exists:expense_types,id',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        Expense::create($request->only('expense_type_id', 'amount', 'description', 'date'));

        return redirect()->route('expenses.index')->with('success', 'Expense created successfully.');
    }

    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $expenseTypes = ExpenseType::all();
        return view('expenses.edit', compact('expense', 'expenseTypes'));
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'expense_type_id' => 'required|exists:expense_types,id',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $expense->update($request->only('expense_type_id', 'amount', 'description', 'date'));

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }

    public function exportPdf(Request $request)
    {
        $query = Expense::with('expenseType');
        if ($request->filled('expense_type_id'))
            $query->where('expense_type_id', $request->expense_type_id);
        if ($request->filled('from_date'))
            $query->whereDate('date', '>=', $request->from_date);
        if ($request->filled('to_date'))
            $query->whereDate('date', '<=', $request->to_date);
        $expenses = $query->latest('date')->get();

        $pdf = Pdf::loadView('exports.expenses', [
            'expenses' => $expenses,
            'title' => 'Expenses Report',
            'filters' => array_filter([
                $request->from_date ? 'From: ' . $request->from_date : null,
                $request->to_date ? 'To: ' . $request->to_date : null,
            ]),
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('expenses-report.pdf');
    }

    public function exportCsv(Request $request)
    {
        $query = Expense::with('expenseType');
        if ($request->filled('expense_type_id'))
            $query->where('expense_type_id', $request->expense_type_id);
        if ($request->filled('from_date'))
            $query->whereDate('date', '>=', $request->from_date);
        if ($request->filled('to_date'))
            $query->whereDate('date', '<=', $request->to_date);
        $expenses = $query->latest('date')->get();

        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="expenses-report.csv"'];
        $callback = function () use ($expenses) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['#', 'Date', 'Expense Type', 'Amount', 'Description']);
            foreach ($expenses as $i => $e) {
                fputcsv($file, [$i + 1, $e->date, $e->expenseType->name ?? '', $e->amount, $e->description ?? '']);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
