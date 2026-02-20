<?php

namespace App\Http\Controllers;

use App\Models\Packing;
use Illuminate\Http\Request;

class PackingController extends Controller
{
    // Display a listing of the packings
    public function index()
    {
        $packings = Packing::paginate(20);
        return view('packings.index', compact('packings'));
    }

    // Show the form for creating a new packing
    public function create()
    {
        return view('packings.create');
    }

    // Store a newly created packing in storage
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'unit_size' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Packing::create($request->all());

        return redirect()->route('packings.index')->with('success', 'Packing created successfully');
    }

    // Display the specified packing
    public function show(Packing $packing)
    {
        return view('packings.show', compact('packing'));
    }

    // Show the form for editing the specified packing
    public function edit(Packing $packing)
    {
        return view('packings.edit', compact('packing'));
    }

    // Update the specified packing in storage
    public function update(Request $request, Packing $packing)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'unit_size' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $packing->update($request->all());

        return redirect()->route('packings.index')->with('success', 'Packing updated successfully');
    }

    // Remove the specified packing from storage
    public function destroy(Packing $packing)
    {
        $packing->delete();

        return redirect()->route('packings.index')->with('success', 'Packing deleted successfully');
    }
}
