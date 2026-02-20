<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::allCached();
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:100',
            'currency_symbol' => 'required|string|max:10',
            'currency_code' => 'nullable|string|max:10',
            'business_name' => 'nullable|string|max:200',
            'business_address' => 'nullable|string|max:500',
            'business_phone' => 'nullable|string|max:30',
            'business_email' => 'nullable|email|max:100',
            'tax_number' => 'nullable|string|max:50',
        ]);

        $keys = [
            'app_name',
            'currency_symbol',
            'currency_code',
            'business_name',
            'business_address',
            'business_phone',
            'business_email',
            'tax_number',
        ];

        foreach ($keys as $key) {
            Setting::set($key, $request->input($key, ''));
        }

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully.');
    }
}
