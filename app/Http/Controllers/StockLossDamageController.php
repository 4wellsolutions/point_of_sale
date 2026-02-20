<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use App\Models\Product;
use App\Models\Batch;
use App\Models\BatchStock;
use App\Models\Location;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockLossDamageController extends Controller
{
    /**
     * Display a listing of stock adjustments.
     */
    public function index(Request $request)
    {
        $query = StockAdjustment::with(['product']);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        $adjustments = $query->latest('date')->paginate(20);
        return view('stock-loss-damage.index', compact('adjustments'));
    }

    /**
     * Show the form for creating a new stock adjustment.
     */
    public function create()
    {
        $products = Product::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        return view('stock-loss-damage.create', compact('products', 'locations'));
    }

    /**
     * Store a newly created stock adjustment in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'batch_id' => 'required|exists:batches,id',
            'location_id' => 'required|exists:locations,id',
            'type' => 'required|in:increase,decrease',
            'category' => 'required|in:adjustment,damage,loss',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:500',
            'date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            // Create the stock adjustment record
            $adjustment = StockAdjustment::create($validated);

            // Update batch stock
            $batchStock = BatchStock::where('batch_id', $validated['batch_id'])
                ->where('location_id', $validated['location_id'])
                ->first();

            if ($validated['type'] === 'decrease') {
                if (!$batchStock || $batchStock->quantity < $validated['quantity']) {
                    throw new \Exception('Insufficient stock in this batch/location. Available: ' . ($batchStock->quantity ?? 0));
                }
                $batchStock->decrement('quantity', $validated['quantity']);

                if ($batchStock->quantity <= 0) {
                    $batchStock->delete();
                }
            } else {
                // Increase
                if ($batchStock) {
                    $batchStock->increment('quantity', $validated['quantity']);
                } else {
                    // Get the batch to find purchase/sale price
                    $existingBatchStock = BatchStock::where('batch_id', $validated['batch_id'])->first();
                    BatchStock::create([
                        'batch_id' => $validated['batch_id'],
                        'product_id' => $validated['product_id'],
                        'location_id' => $validated['location_id'],
                        'quantity' => $validated['quantity'],
                        'purchase_price' => $existingBatchStock->purchase_price ?? 0,
                        'sale_price' => $existingBatchStock->sale_price ?? 0,
                    ]);
                }
            }

            // Create inventory transaction
            InventoryTransaction::create([
                'product_id' => $validated['product_id'],
                'location_id' => $validated['location_id'],
                'batch_id' => $validated['batch_id'],
                'quantity' => $validated['type'] === 'decrease' ? -$validated['quantity'] : $validated['quantity'],
                'user_id' => auth()->id(),
                'transactionable_id' => $adjustment->id,
                'transactionable_type' => StockAdjustment::class,
            ]);

            DB::commit();
            return redirect()->route('stock-loss-damage.index')
                ->with('success', 'Stock adjustment recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified stock adjustment.
     */
    public function show($id)
    {
        $adjustment = StockAdjustment::with(['product'])->findOrFail($id);
        return view('stock-loss-damage.show', compact('adjustment'));
    }

    /**
     * Show the form for editing the specified stock adjustment.
     */
    public function edit($id)
    {
        $adjustment = StockAdjustment::findOrFail($id);
        $products = Product::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        return view('stock-loss-damage.edit', compact('adjustment', 'products', 'locations'));
    }

    /**
     * Update the specified stock adjustment in storage.
     * Note: Only the reason and date can be updated. Quantity changes require a new adjustment.
     */
    public function update(Request $request, $id)
    {
        $adjustment = StockAdjustment::findOrFail($id);

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
            'date' => 'required|date',
        ]);

        $adjustment->update($validated);

        return redirect()->route('stock-loss-damage.index')
            ->with('success', 'Stock adjustment updated successfully.');
    }

    /**
     * Remove the specified stock adjustment from storage (with reversal).
     */
    public function destroy($id)
    {
        $adjustment = StockAdjustment::findOrFail($id);

        DB::beginTransaction();
        try {
            // Reverse the stock effect
            $batchStock = BatchStock::where('batch_id', $adjustment->batch_id)
                ->where('location_id', $adjustment->location_id)
                ->first();

            if ($adjustment->type === 'decrease') {
                // Was a decrease, so increase stock back
                if ($batchStock) {
                    $batchStock->increment('quantity', $adjustment->quantity);
                } else {
                    $existingBatchStock = BatchStock::where('batch_id', $adjustment->batch_id)->first();
                    BatchStock::create([
                        'batch_id' => $adjustment->batch_id,
                        'product_id' => $adjustment->product_id,
                        'location_id' => $adjustment->location_id,
                        'quantity' => $adjustment->quantity,
                        'purchase_price' => $existingBatchStock->purchase_price ?? 0,
                        'sale_price' => $existingBatchStock->sale_price ?? 0,
                    ]);
                }
            } else {
                // Was an increase, so decrease stock back
                if ($batchStock) {
                    $batchStock->decrement('quantity', $adjustment->quantity);
                    if ($batchStock->quantity <= 0) {
                        $batchStock->delete();
                    }
                }
            }

            // Create reversal inventory transaction
            InventoryTransaction::create([
                'product_id' => $adjustment->product_id,
                'location_id' => $adjustment->location_id,
                'batch_id' => $adjustment->batch_id,
                'quantity' => $adjustment->type === 'decrease' ? $adjustment->quantity : -$adjustment->quantity,
                'user_id' => auth()->id(),
                'transactionable_id' => $adjustment->id,
                'transactionable_type' => StockAdjustment::class,
            ]);

            $adjustment->delete();

            DB::commit();
            return redirect()->route('stock-loss-damage.index')
                ->with('success', 'Stock adjustment deleted and effects reversed.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Error deleting adjustment: ' . $e->getMessage()]);
        }
    }

    /**
     * Get batches with stock for a given product (AJAX endpoint).
     */
    public function getBatches(Request $request)
    {
        $batches = Batch::where('product_id', $request->product_id)
            ->with([
                'batchstocks' => function ($q) use ($request) {
                    if ($request->filled('location_id')) {
                        $q->where('location_id', $request->location_id);
                    }
                }
            ])
            ->get()
            ->map(function ($batch) {
                $totalStock = $batch->batchstocks->sum('quantity');
                return [
                    'id' => $batch->id,
                    'batch_no' => $batch->batch_no,
                    'stock' => $totalStock,
                ];
            });

        return response()->json($batches);
    }
}
