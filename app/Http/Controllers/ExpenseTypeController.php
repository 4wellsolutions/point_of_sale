<?php

namespace App\Http\Controllers;

use App\Models\ExpenseType;
use Illuminate\Http\Request;

class ExpenseTypeController extends Controller
{
    public function index()
    {
        $expenseTypes = ExpenseType::all();
        return view('expense_types.index', compact('expenseTypes'));
    }

    public function create()
    {
        return view('expense_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        ExpenseType::create($request->only('name', 'description'));

        return redirect()->route('expense-types.index')->with('success', 'Expense Type created successfully.');
    }

    public function show(ExpenseType $expenseType)
    {
        return view('expense_types.show', compact('expenseType'));
    }

    public function edit(ExpenseType $expenseType)
    {
        return view('expense_types.edit', compact('expenseType'));
    }

    public function update(Request $request, ExpenseType $expenseType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $expenseType->update($request->only('name', 'description'));

        return redirect()->route('expense-types.index')->with('success', 'Expense Type updated successfully.');
    }

    public function destroy(ExpenseType $expenseType)
    {
        $expenseType->delete();
        return redirect()->route('expense-types.index')->with('success', 'Expense Type deleted successfully.');
    }
}
