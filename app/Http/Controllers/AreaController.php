<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index()
    {
        $areas = Area::withCount('customers')->latest()->paginate(20);
        return view('areas.index', compact('areas'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100|unique:areas,name']);
        Area::create(['name' => $request->name]);
        return back()->with('success', 'Area added successfully.');
    }

    public function update(Request $request, Area $area)
    {
        $request->validate(['name' => 'required|string|max:100|unique:areas,name,' . $area->id]);
        $area->update(['name' => $request->name]);
        return back()->with('success', 'Area updated successfully.');
    }

    public function destroy(Area $area)
    {
        $area->delete();
        return back()->with('success', 'Area deleted.');
    }
}
