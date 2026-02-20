<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StockLossDamageController extends Controller
{
    public function index()
    {
        return view('stock-loss-damage.index');
    }

    public function create()
    {
        return view('stock-loss-damage.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('stock-loss-damage.index')
            ->with('info', 'Stock loss/damage feature coming soon.');
    }

    public function show($id)
    {
        abort(404);
    }

    public function edit($id)
    {
        abort(404);
    }

    public function update(Request $request, $id)
    {
        abort(404);
    }

    public function destroy($id)
    {
        abort(404);
    }
}
