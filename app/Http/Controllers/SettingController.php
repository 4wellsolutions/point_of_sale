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

    public function activityLog(\Illuminate\Http\Request $request)
    {
        $query = \OwenIt\Auditing\Models\Audit::with('user')
            ->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }
        if ($request->filled('model')) {
            $query->where('auditable_type', 'like', '%' . $request->model . '%');
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50)->withQueryString();
        $users = \App\Models\User::orderBy('name')->get();

        return view('settings.activity-log', compact('logs', 'users'));
    }
}

