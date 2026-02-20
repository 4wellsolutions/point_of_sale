<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class InventoryTransactionController extends Controller
{
    /**
     * Display inventory transactions with filters.
     */
    public function index(Request $request)
    {
        $query = InventoryTransaction::with(['product', 'user']);

        // Apply filters if present in the request
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
        }

        $transactions = $query->latest()->paginate(20);

        // Fetch products and users for dropdowns
        $products = Product::select('id', 'name')->get();
        $users = User::select('id', 'name')->get();

        return view('reports.inventory.index', compact('transactions', 'products', 'users'));
    }

    /**
     * Generate PDF View for Inventory Transactions.
     */
    public function viewPdf(Request $request)
    {
        $query = InventoryTransaction::with(['product', 'user']);

        // Apply filters (same as index method)
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
        }

        $transactions = $query->get();

        // Generate PDF for View
        $pdf = Pdf::loadView('reports.inventory.pdf', compact('transactions'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('reports.inventory_transactions.pdf'); // Display in browser instead of downloading
    }
}
