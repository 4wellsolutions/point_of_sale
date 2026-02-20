<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::all();
        return view('payment_methods.index', compact('paymentMethods'));
    }

    public function create()
    {
        return view('payment_methods.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'method_name' => 'required|unique:payment_methods|max:255',
        ]);

        PaymentMethod::create([
            'method_name' => $request->method_name,
        ]);

        return redirect()->route('payment_methods.index')
                         ->with('success', 'Payment Method added successfully.');
    }

    public function edit($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        return view('payment_methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'method_name' => 'required|unique:payment_methods,method_name,' . $id . '|max:255',
        ]);

        $paymentMethod = PaymentMethod::findOrFail($id);
        $paymentMethod->update([
            'method_name' => $request->method_name,
        ]);

        return redirect()->route('payment_methods.index')
                         ->with('success', 'Payment Method updated successfully.');
    }

    public function destroy($id)
    {
        PaymentMethod::destroy($id);
        return redirect()->route('payment_methods.index')
                         ->with('success', 'Payment Method deleted successfully.');
    }
}
