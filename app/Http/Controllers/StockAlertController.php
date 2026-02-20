<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Batch;
use App\Models\BatchStock;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StockAlertController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'low-stock');

        // Low Stock Products: current stock <= reorder_level
        $lowStockProducts = Product::where('status', 'active')
            ->where('reorder_level', '>', 0)
            ->get()
            ->map(function ($product) {
                $product->current_stock = BatchStock::where('product_id', $product->id)->sum('quantity');
                return $product;
            })
            ->filter(function ($product) {
                return $product->current_stock <= $product->reorder_level;
            })
            ->sortBy('current_stock')
            ->values();

        // Expiring Soon: batches expiring within next 30 days (with stock > 0)
        $expiringDays = $request->get('days', 30);
        $expiringBatches = Batch::with(['product', 'batchstocks.location'])
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', Carbon::now()->addDays($expiringDays))
            ->where('expiry_date', '>=', Carbon::today())
            ->whereHas('batchstocks', function ($q) {
                $q->where('quantity', '>', 0);
            })
            ->orderBy('expiry_date')
            ->get()
            ->map(function ($batch) {
                $batch->remaining_stock = $batch->batchstocks->sum('quantity');
                $batch->days_until_expiry = Carbon::today()->diffInDays(Carbon::parse($batch->expiry_date));
                return $batch;
            });

        // Already expired batches with stock > 0
        $expiredBatches = Batch::with(['product', 'batchstocks.location'])
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', Carbon::today())
            ->whereHas('batchstocks', function ($q) {
                $q->where('quantity', '>', 0);
            })
            ->orderBy('expiry_date', 'desc')
            ->get()
            ->map(function ($batch) {
                $batch->remaining_stock = $batch->batchstocks->sum('quantity');
                $batch->days_expired = Carbon::parse($batch->expiry_date)->diffInDays(Carbon::today());
                return $batch;
            });

        return view('stock-alerts.index', compact(
            'lowStockProducts',
            'expiringBatches',
            'expiredBatches',
            'tab',
            'expiringDays'
        ));
    }
}
