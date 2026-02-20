<?php

namespace App\Http\Controllers;

use App\Models\SalesReturn;
use App\Models\Sale;
use Illuminate\Http\Request;

class SalesReturnController extends Controller
{
    /**
     * Display a listing of the sales returns.
     */
    public function index()
    {
        $salesReturns = SalesReturn::with(['sale.product', 'sale.customer'])->paginate(10);
        return view('sales_returns.index', compact('salesReturns'));
    }

    /**
     * Show the form for creating a new sales return.
     */
    public function create()
    {
        $sales = Sale::with('product', 'customer')->get();
        return view('sales_returns.create', compact('sales'));
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

        SalesReturn::create($request->all());

        return redirect()->route('sales-returns.index')->with('success', 'Sales return created successfully.');
    }

    /**
     * Display the specified sales return.
     */
    public function show(SalesReturn $salesReturn)
    {
        $salesReturn->load(['sale.product', 'sale.customer']);
        return view('sales_returns.show', compact('salesReturn'));
    }

    /**
     * Show the form for editing the specified sales return.
     */
    public function edit(SalesReturn $salesReturn)
    {
        $sales = Sale::with('product', 'customer')->get();
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
     * Remove the specified sales return from storage.
     */
    public function destroy(SalesReturn $salesReturn)
    {
        $salesReturn->delete();

        return redirect()->route('sales-returns.index')->with('success', 'Sales return deleted successfully.');
    }
}
