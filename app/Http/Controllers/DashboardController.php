<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Purchase;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Expense;
use App\Models\Transaction;
use App\Models\BatchStock;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function index()
	{
		$today = Carbon::today();
		$startOfMonth = Carbon::now()->startOfMonth();
		$endOfMonth = Carbon::now()->endOfMonth();

		// KPI Cards
		$todaySales = Sale::whereDate('sale_date', $today)->sum('net_amount');
		$todayPurchases = Purchase::whereDate('purchase_date', $today)->sum('net_amount');
		$monthSales = Sale::whereBetween('sale_date', [$startOfMonth, $endOfMonth])->sum('net_amount');
		$monthPurchases = Purchase::whereBetween('purchase_date', [$startOfMonth, $endOfMonth])->sum('net_amount');
		$monthExpenses = Expense::whereBetween('date', [$startOfMonth, $endOfMonth])->sum('amount');
		$totalCustomers = Customer::count();
		$totalVendors = Vendor::count();
		$totalProducts = Product::where('status', 'active')->count();

		// Low Stock Products
		$lowStockProducts = Product::where('status', 'active')
			->whereColumn('reorder_level', '>', DB::raw('0'))
			->get()
			->filter(function ($product) {
				$currentStock = BatchStock::where('product_id', $product->id)->sum('quantity');
				$product->current_stock_qty = $currentStock;
				return $currentStock <= $product->reorder_level;
			})
			->take(10);

		// Recent Sales
		$recentSales = Sale::with('customer')
			->orderBy('sale_date', 'desc')
			->take(5)
			->get();

		// Recent Purchases
		$recentPurchases = Purchase::with('vendor')
			->orderBy('purchase_date', 'desc')
			->take(5)
			->get();

		// Top Selling Products (this month)
		$topProducts = SaleItem::select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(total_amount) as total_revenue'))
			->whereHas('sale', function ($q) use ($startOfMonth, $endOfMonth) {
				$q->whereBetween('sale_date', [$startOfMonth, $endOfMonth]);
			})
			->groupBy('product_id')
			->orderByDesc('total_qty')
			->take(5)
			->with('product')
			->get();

		// Monthly Sales & Purchases for Chart (last 6 months)
		$chartData = [];
		for ($i = 5; $i >= 0; $i--) {
			$month = Carbon::now()->subMonths($i);
			$label = $month->format('M Y');
			$salesTotal = Sale::whereYear('sale_date', $month->year)
				->whereMonth('sale_date', $month->month)
				->sum('net_amount');
			$purchasesTotal = Purchase::whereYear('purchase_date', $month->year)
				->whereMonth('purchase_date', $month->month)
				->sum('net_amount');
			$expensesTotal = Expense::whereYear('date', $month->year)
				->whereMonth('date', $month->month)
				->sum('amount');

			$chartData[] = [
				'label' => $label,
				'sales' => (float) $salesTotal,
				'purchases' => (float) $purchasesTotal,
				'expenses' => (float) $expensesTotal,
			];
		}

		return view('home', compact(
			'todaySales',
			'todayPurchases',
			'monthSales',
			'monthPurchases',
			'monthExpenses',
			'totalCustomers',
			'totalVendors',
			'totalProducts',
			'lowStockProducts',
			'recentSales',
			'recentPurchases',
			'topProducts',
			'chartData'
		));
	}
}
