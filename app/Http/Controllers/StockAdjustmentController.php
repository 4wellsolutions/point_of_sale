<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use App\Models\Product;
use App\Models\Batch;
use App\Models\BatchStock;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function index()
    {
        $stockAdjustments = StockAdjustment::with('product')->latest()->paginate(20);
        return view('stock_adjustments.index', compact('stockAdjustments'));
    }

    public function create()
    {
        return view('stock_adjustments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'stock_adjustments' => 'required|array|min:1',
            'stock_adjustments.*.product_id' => 'required|exists:products,id',
            'stock_adjustments.*.batch_no' => 'required|string',
            'stock_adjustments.*.location_id' => 'required|exists:locations,id',
            'stock_adjustments.*.adjustment_type' => 'required|in:increase,decrease',
            'stock_adjustments.*.adjust_qty' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->stock_adjustments as $item) {
                $batch = Batch::where('product_id', $item['product_id'])
                    ->where('batch_no', $item['batch_no'])
                    ->first();

                if (!$batch) {
                    throw new \Exception("Batch '{$item['batch_no']}' not found for this product.");
                }

                $batchStock = BatchStock::where('batch_id', $batch->id)
                    ->where('location_id', $item['location_id'])
                    ->first();

                if ($item['adjustment_type'] === 'decrease') {
                    if (!$batchStock || $batchStock->quantity < $item['adjust_qty']) {
                        throw new \Exception('Not enough stock to decrease.');
                    }
                    $batchStock->decrement('quantity', $item['adjust_qty']);
                } else {
                    if ($batchStock) {
                        $batchStock->increment('quantity', $item['adjust_qty']);
                    } else {
                        BatchStock::create([
                            'batch_id' => $batch->id,
                            'product_id' => $item['product_id'],
                            'location_id' => $item['location_id'],
                            'quantity' => $item['adjust_qty'],
                            'purchase_price' => 0,
                            'sale_price' => 0,
                        ]);
                    }
                }

                $adjustment = StockAdjustment::create([
                    'product_id' => $item['product_id'],
                    'batch_id' => $batch->id,
                    'location_id' => $item['location_id'],
                    'type' => $item['adjustment_type'],
                    'quantity' => $item['adjust_qty'],
                    'reason' => $item['reason'] ?? null,
                    'date' => $request->date,
                ]);

                InventoryTransaction::create([
                    'product_id' => $item['product_id'],
                    'location_id' => $item['location_id'],
                    'batch_id' => $batch->id,
                    'quantity' => $item['adjustment_type'] === 'increase' ? $item['adjust_qty'] : -$item['adjust_qty'],
                    'user_id' => auth()->id(),
                    'transactionable_id' => $adjustment->id,
                    'transactionable_type' => StockAdjustment::class,
                ]);
            }

            DB::commit();
            return redirect()->route('stock_adjustments.index')->with('success', 'Stock adjustment saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        $adjustment = StockAdjustment::with('product')->findOrFail($id);
        return view('stock_adjustments.show', compact('adjustment'));
    }

    public function edit($id)
    {
        $adjustment = StockAdjustment::with('product')->findOrFail($id);
        return view('stock_adjustments.edit', compact('adjustment'));
    }

    public function update(Request $request, $id)
    {
        $adjustment = StockAdjustment::findOrFail($id);

        $request->validate([
            'adjustment_type' => 'required|in:increase,decrease',
            'adjust_qty' => 'required|integer|min:1',
            'reason' => 'nullable|string',
            'date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $batch = Batch::find($adjustment->batch_id);
            $batchStock = $batch ? BatchStock::where('batch_id', $batch->id)
                ->where('location_id', $adjustment->location_id)->first() : null;

            // Reverse old effect
            if ($batchStock) {
                if ($adjustment->type === 'increase') {
                    $batchStock->decrement('quantity', $adjustment->quantity);
                } else {
                    $batchStock->increment('quantity', $adjustment->quantity);
                }
            }

            // Apply new effect
            if ($request->adjustment_type === 'decrease') {
                if (!$batchStock || $batchStock->quantity < $request->adjust_qty) {
                    throw new \Exception('Not enough stock to decrease.');
                }
                $batchStock->decrement('quantity', $request->adjust_qty);
            } else {
                if ($batchStock) {
                    $batchStock->increment('quantity', $request->adjust_qty);
                }
            }

            $adjustment->update([
                'type' => $request->adjustment_type,
                'quantity' => $request->adjust_qty,
                'reason' => $request->reason,
                'date' => $request->date,
            ]);

            DB::commit();
            return redirect()->route('stock_adjustments.index')->with('success', 'Stock adjustment updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        $adjustment = StockAdjustment::findOrFail($id);

        DB::beginTransaction();
        try {
            $batch = Batch::find($adjustment->batch_id);
            $batchStock = $batch ? BatchStock::where('batch_id', $batch->id)
                ->where('location_id', $adjustment->location_id)->first() : null;

            if ($batchStock) {
                // Reverse the effect
                if ($adjustment->type === 'increase') {
                    $newQty = $batchStock->quantity - $adjustment->quantity;
                    $newQty <= 0 ? $batchStock->delete() : $batchStock->update(['quantity' => $newQty]);
                } else {
                    $batchStock->increment('quantity', $adjustment->quantity);
                }
            }

            $adjustment->delete();
            DB::commit();
            return redirect()->route('stock_adjustments.index')->with('success', 'Adjustment deleted and stock reversed.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
