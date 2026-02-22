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
use App\Models\LedgerEntry;
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

		// Total Receivable: sum of latest ledger balance per customer (where positive = they owe us)
		// + opening balances of type 'debit' for customers with no ledger entries yet
		$customerClass = Customer::class;
		$totalReceivable = LedgerEntry::where('ledgerable_type', $customerClass)
			->whereIn('id', function ($sub) use ($customerClass) {
				$sub->selectRaw('MAX(id)')
					->from('ledger_entries')
					->where('ledgerable_type', $customerClass)
					->groupBy('ledgerable_id');
			})
			->where('balance', '>', 0)
			->sum('balance');

		// Add customer opening balances (debit = receivable) for customers with NO ledger entries
		$custWithLedger = LedgerEntry::where('ledgerable_type', $customerClass)
			->distinct()->pluck('ledgerable_id');
		$totalReceivable += Customer::where('opening_balance_type', 'debit')
			->where('opening_balance', '>', 0)
			->whereNotIn('id', $custWithLedger)
			->sum('opening_balance');

		// Total Payable: sum of latest ledger balance per vendor (where positive = we owe them)
		// + opening balances of type 'credit' for vendors with no ledger entries yet
		$vendorClass = Vendor::class;
		$totalPayable = LedgerEntry::where('ledgerable_type', $vendorClass)
			->whereIn('id', function ($sub) use ($vendorClass) {
				$sub->selectRaw('MAX(id)')
					->from('ledger_entries')
					->where('ledgerable_type', $vendorClass)
					->groupBy('ledgerable_id');
			})
			->where('balance', '>', 0)
			->sum('balance');

		// Add vendor opening balances (credit = payable) for vendors with NO ledger entries
		$vendWithLedger = LedgerEntry::where('ledgerable_type', $vendorClass)
			->distinct()->pluck('ledgerable_id');
		$totalPayable += Vendor::where('opening_balance_type', 'credit')
			->where('opening_balance', '>', 0)
			->whereNotIn('id', $vendWithLedger)
			->sum('opening_balance');

		// Low Stock Products
		$lowStockProducts = Product::where('status', 'active')
			->where('reorder_level', '>', 0)
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
			'totalReceivable',
			'totalPayable',
			'lowStockProducts',
			'recentSales',
			'recentPurchases',
			'topProducts',
			'chartData'
		));
	}
}
