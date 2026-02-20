<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;
use App\Models\User;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Audit::with('user')->latest();

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by event/action
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        // Filter by model type
        if ($request->filled('auditable_type')) {
            $query->where('auditable_type', $request->auditable_type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $audits = $query->paginate(50)->appends($request->query());
        $users = User::orderBy('name')->get();

        // Get distinct model types for filter dropdown
        $modelTypes = Audit::select('auditable_type')
            ->distinct()
            ->orderBy('auditable_type')
            ->pluck('auditable_type')
            ->map(function ($type) {
                return [
                    'value' => $type,
                    'label' => class_basename($type),
                ];
            });

        return view('activity-log.index', compact('audits', 'users', 'modelTypes'));
    }
}
