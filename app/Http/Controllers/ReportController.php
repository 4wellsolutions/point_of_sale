<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Sales Report
     */
    public function sales(Request $request)
    {
        $query = Sale::with(['customer', 'user']);

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }

        $sales = $query->latest('id')->paginate(50)->appends($request->query());

        $totalAmount = (clone $query)->getQuery()->sum('net_amount');
        $totalDiscount = (clone $query)->getQuery()->sum('discount_amount');
        $totalCount = (clone $query)->getQuery()->count();

        $customers = Customer::orderBy('name')->get();

        return view('reports.sales', compact('sales', 'customers', 'totalAmount', 'totalDiscount', 'totalCount'));
    }

    /**
     * Purchase Report
     */
    public function purchases(Request $request)
    {
        $query = Purchase::with(['vendor', 'user']);

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('purchase_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('purchase_date', '<=', $request->date_to);
        }

        $purchases = $query->latest('id')->paginate(50)->appends($request->query());

        $totalAmount = (clone $query)->getQuery()->sum('net_amount');
        $totalDiscount = (clone $query)->getQuery()->sum('discount_amount');
        $totalCount = (clone $query)->getQuery()->count();

        $vendors = Vendor::orderBy('name')->get();

        return view('reports.purchases', compact('purchases', 'vendors', 'totalAmount', 'totalDiscount', 'totalCount'));
    }

    /**
     * Profit & Loss Report
     */
    public function profitLoss(Request $request)
    {
        $dateFrom = $request->date_from ?? Carbon::now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? Carbon::now()->toDateString();

        // Sales revenue
        $salesRevenue = Sale::whereDate('sale_date', '>=', $dateFrom)
            ->whereDate('sale_date', '<=', $dateTo)
            ->sum('net_amount');

        // Cost of goods sold (from sale items' purchase prices)
        $cogs = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereNull('sales.deleted_at')
            ->whereNull('sale_items.deleted_at')
            ->whereDate('sales.sale_date', '>=', $dateFrom)
            ->whereDate('sales.sale_date', '<=', $dateTo)
            ->sum(DB::raw('sale_items.quantity * sale_items.purchase_price'));

        // Total purchases
        $totalPurchases = Purchase::whereDate('purchase_date', '>=', $dateFrom)
            ->whereDate('purchase_date', '<=', $dateTo)
            ->sum('net_amount');

        // Total expenses
        $totalExpenses = Expense::whereDate('date', '>=', $dateFrom)
            ->whereDate('date', '<=', $dateTo)
            ->sum('amount');

        // Expense breakdown by type
        $expensesByType = Expense::select('expense_type_id', DB::raw('SUM(amount) as total'))
            ->with('expenseType')
            ->whereDate('date', '>=', $dateFrom)
            ->whereDate('date', '<=', $dateTo)
            ->groupBy('expense_type_id')
            ->get();

        $grossProfit = $salesRevenue - $cogs;
        $netProfit = $grossProfit - $totalExpenses;

        return view('reports.profit-loss', compact(
            'dateFrom',
            'dateTo',
            'salesRevenue',
            'cogs',
            'totalPurchases',
            'totalExpenses',
            'expensesByType',
            'grossProfit',
            'netProfit'
        ));
    }

    /**
     * Expense Report
     */
    public function expenses(Request $request)
    {
        $query = Expense::with(['expenseType', 'user']);

        if ($request->filled('expense_type_id')) {
            $query->where('expense_type_id', $request->expense_type_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $expenses = $query->latest('id')->paginate(50)->appends($request->query());
        $totalAmount = (clone $query)->getQuery()->sum('amount');
        $totalCount = (clone $query)->getQuery()->count();

        $expenseTypes = \App\Models\ExpenseType::orderBy('name')->get();

        return view('reports.expenses', compact('expenses', 'expenseTypes', 'totalAmount', 'totalCount'));
    }

    /**
     * Stock / Inventory Report
     */
    public function stock(Request $request)
    {
        $query = Product::with(['category', 'batches.batchStocks'])
            ->withSum('batches as total_stock', 'quantity');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->havingRaw('total_stock <= alert_quantity AND total_stock > 0');
            } elseif ($request->stock_status === 'out') {
                $query->havingRaw('total_stock <= 0 OR total_stock IS NULL');
            } elseif ($request->stock_status === 'in') {
                $query->havingRaw('total_stock > alert_quantity');
            }
        }

        $products = $query->orderBy('name')->paginate(50)->appends($request->query());
        $categories = Category::orderBy('name')->get();

        $totalProducts = Product::count();
        $lowStockCount = Product::withSum('batches as total_stock', 'quantity')
            ->havingRaw('total_stock <= alert_quantity AND total_stock > 0')
            ->count();
        $outOfStockCount = Product::withSum('batches as total_stock', 'quantity')
            ->havingRaw('total_stock <= 0 OR total_stock IS NULL')
            ->count();

        return view('reports.stock', compact(
            'products',
            'categories',
            'totalProducts',
            'lowStockCount',
            'outOfStockCount'
        ));
    }
}
