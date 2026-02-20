<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the locations.
     */
    public function index()
    {
        $locations = Location::all();
        return view('locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new location.
     */
    public function create()
    {
        return view('locations.create');
    }

    /**
     * Store a newly created location in storage.
     */
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'name' => 'required|unique:locations,name|max:255',
            'description' => 'nullable|string',
        ]);

        // Create location
        Location::create($request->all());

        return redirect()->route('locations.index')
                         ->with('success', 'Location created successfully.');
    }

    /**
     * Display the specified location.
     */
    public function show(Location $location)
    {
        return view('locations.show', compact('location'));
    }

    /**
     * Show the form for editing the specified location.
     */
    public function edit(Location $location)
    {
        return view('locations.edit', compact('location'));
    }

    /**
     * Update the specified location in storage.
     */
    public function update(Request $request, Location $location)
    {
        // Validate input
        $request->validate([
            'name' => 'required|unique:locations,name,' . $location->id . '|max:255',
            'description' => 'nullable|string',
        ]);

        // Update location
        $location->update($request->all());

        return redirect()->route('locations.index')
                         ->with('success', 'Location updated successfully.');
    }

    /**
     * Remove the specified location from storage.
     */
    public function destroy(Location $location)
    {
        $location->delete();

        return redirect()->route('locations.index')
                         ->with('success', 'Location deleted successfully.');
    }
}
