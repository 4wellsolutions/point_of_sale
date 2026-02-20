<?php
namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseType;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with('expenseType')->get();
        return view('expenses.index', compact('expenses'));
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
}
