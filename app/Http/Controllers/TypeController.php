<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    /**
     * Display a listing of the Types.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $types = Type::orderBy('created_at', 'desc')->paginate(10); // Pagination
        return view('types.index', compact('types'));
    }

    /**
     * Show the form for creating a new Type.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('types.create');
    }

    /**
     * Store a newly created Type in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'name' => 'required|string|max:255|unique:types,name',
        ]);

        // Create Type
        Type::create([
            'name' => $request->name,
        ]);

        // Redirect with success message
        return redirect()->route('types.index')->with('success', 'Type created successfully.');
    }

    /**
     * Show the form for editing the specified Type.
     *
     * @param  \App\Models\Type  $type
     * @return \Illuminate\View\View
     */
    public function edit(Type $type)
    {
        return view('types.edit', compact('type'));
    }

    /**
     * Update the specified Type in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Type  $type
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Type $type)
    {
        // Validation
        $request->validate([
            'name' => 'required|string|max:255|unique:types,name,' . $type->id,
        ]);

        // Update Type
        $type->update([
            'name' => $request->name,
        ]);

        // Redirect with success message
        return redirect()->route('types.index')->with('success', 'Type updated successfully.');
    }

    /**
     * Remove the specified Type from storage.
     *
     * @param  \App\Models\Type  $type
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Type $type)
    {
        // Check if Type is associated with any Customers or Vendors
        $hasCustomers = $type->customers()->exists();
        $hasVendors = $type->vendors()->exists();

        if ($hasCustomers || $hasVendors) {
            return redirect()->route('types.index')->with('error', 'Cannot delete Type as it is associated with Customers or Vendors.');
        }

        // Delete Type
        $type->delete();

        // Redirect with success message
        return redirect()->route('types.index')->with('success', 'Type deleted successfully.');
    }
}
