<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\BatchStock;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function locations($batch, $product)
    {
        // Validate batch_no format before querying
        if (!is_numeric($batch) && strlen($batch) > 50) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid batch number format.'
            ], 422);
        }

        // Fetch the batch using batch_no
        $batch = Batch::where('batch_no', $batch)->first();

        // If batch does not exist, return an error response
        if (!$batch) {
            return response()->json([
                'success' => false,
                'error' => 'Batch not found.'
            ], 404);
        }

        // Load batchstocks along with location details
        $batchstocks = BatchStock::where("product_id", $product)->with('location')->get();
        // If there are no batch stocks, return an empty response with a message
        if ($batchstocks->isEmpty()) {
            return response()->json([
                'success' => false,
                'error' => 'No stock available for this batch.'
            ], 404);
        }

        // Extract locations and ensure proper validation
        $locations = $batchstocks->map(function ($batchStock) {
            // Ensure location exists
            if (!$batchStock->location) {
                return null;
            }

            return [
                'id' => $batchStock->location->id,
                'name' => $batchStock->location->name,
                'product_id' => $batchStock->product_id,
                'quantity' => is_numeric($batchStock->quantity) ? (int) $batchStock->quantity : 0,
                'purchase_price' => $batchStock->purchase_price,
                'sale_price' => 0,
            ];
        })->filter()->unique('id')->values();

        // If no valid locations are found, return an error message
        if ($locations->isEmpty()) {
            return response()->json([
                'success' => false,
                'error' => 'No valid locations found for this batch.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'locations' => $locations
        ]);
    }

}
