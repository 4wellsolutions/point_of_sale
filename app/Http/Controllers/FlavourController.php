<?php

namespace App\Http\Controllers;

use App\Models\Flavour;
use Illuminate\Http\Request;

class FlavourController extends Controller
{
    /**
     * Display a listing of the flavours.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Retrieve all flavours, paginated
        $flavours = Flavour::orderBy('created_at', 'desc')->paginate(10);

        // Return the index view with flavours data
        return view('flavours.index', compact('flavours'));
    }

    /**
     * Show the form for creating a new flavour.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Return the create view
        return view('flavours.create');
    }

    /**
     * Store a newly created flavour in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255|unique:flavours,name',
            'description' => 'nullable|string',
        ]);

        // Create a new flavour with validated data
        Flavour::create($request->only(['name', 'description']));

        // Redirect to the flavours index with a success message
        return redirect()->route('flavours.index')
                         ->with('success', 'Flavour created successfully.');
    }

    /**
     * Display the specified flavour.
     *
     * @param  \App\Models\Flavour  $flavour
     * @return \Illuminate\View\View
     */
    public function show(Flavour $flavour)
    {
        // Return the show view with the flavour data
        return view('flavours.show', compact('flavour'));
    }

    /**
     * Show the form for editing the specified flavour.
     *
     * @param  \App\Models\Flavour  $flavour
     * @return \Illuminate\View\View
     */
    public function edit(Flavour $flavour)
    {
        // Return the edit view with the flavour data
        return view('flavours.edit', compact('flavour'));
    }

    /**
     * Update the specified flavour in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Flavour  $flavour
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Flavour $flavour)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255|unique:flavours,name,' . $flavour->id,
            'description' => 'nullable|string',
        ]);

        // Update the flavour with validated data
        $flavour->update($request->only(['name', 'description']));

        // Redirect to the flavours index with a success message
        return redirect()->route('flavours.index')
                         ->with('success', 'Flavour updated successfully.');
    }

    /**
     * Remove the specified flavour from storage.
     *
     * @param  \App\Models\Flavour  $flavour
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Flavour $flavour)
    {
        // Delete the flavour
        $flavour->delete();

        // Redirect to the flavours index with a success message
        return redirect()->route('flavours.index')
                         ->with('success', 'Flavour deleted successfully.');
    }
}
