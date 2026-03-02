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
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Sales Report
     */
    public function sales(Request $request)
    {
        $query = Sale::with(['customer']);

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
        $query = Purchase::with(['vendor']);

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
        $query = Expense::with(['expenseType']);

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
        $query = \App\Models\BatchStock::with(['product.category', 'batch', 'location'])
            ->join('products', 'batch_stocks.product_id', '=', 'products.id')
            ->join('batches', 'batch_stocks.batch_id', '=', 'batches.id')
            ->join('locations', 'batch_stocks.location_id', '=', 'locations.id')
            ->where('products.status', 'active')
            ->whereNull('batches.deleted_at')
            ->select(
                'batch_stocks.*',
                'products.name as product_name',
                'products.sku',
                'products.category_id',
                'products.reorder_level'
            );

        if ($request->filled('category_id')) {
            $query->where('products.category_id', $request->category_id);
        }
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->whereRaw('batch_stocks.quantity <= products.reorder_level AND batch_stocks.quantity > 0');
            } elseif ($request->stock_status === 'out') {
                $query->whereRaw('batch_stocks.quantity <= 0');
            } elseif ($request->stock_status === 'in') {
                $query->whereRaw('batch_stocks.quantity > products.reorder_level');
            }
        }

        $stockEntries = $query->orderBy('products.name')
            ->orderBy('batches.batch_no')
            ->paginate(50)
            ->appends($request->query());

        $categories = Category::orderBy('name')->get();

        // Calculate KPI totals
        $totalProducts = Product::where('status', 'active')->count();

        $stockSql = '(SELECT COALESCE(SUM(bs.quantity),0)
            FROM batch_stocks bs
            INNER JOIN batches b ON b.id = bs.batch_id
            WHERE b.product_id = products.id AND b.deleted_at IS NULL)';

        $lowStockCount = Product::where('status', 'active')
            ->whereRaw("$stockSql <= products.reorder_level")
            ->whereRaw("$stockSql > 0")
            ->count();

        $outOfStockCount = Product::where('status', 'active')
            ->whereRaw("$stockSql <= 0")
            ->count();

        return view('reports.stock', compact(
            'stockEntries',
            'categories',
            'totalProducts',
            'lowStockCount',
            'outOfStockCount'
        ));
    }

    /* ─────────────────────────────────────────────
       EXPORT HELPERS — shared query builder
       ───────────────────────────────────────────── */

    private function buildSalesQuery(Request $request)
    {
        $q = Sale::with(['customer']);
        if ($request->filled('customer_id'))
            $q->where('customer_id', $request->customer_id);
        if ($request->filled('date_from'))
            $q->whereDate('sale_date', '>=', $request->date_from);
        if ($request->filled('date_to'))
            $q->whereDate('sale_date', '<=', $request->date_to);
        return $q;
    }

    private function buildPurchasesQuery(Request $request)
    {
        $q = Purchase::with(['vendor']);
        if ($request->filled('vendor_id'))
            $q->where('vendor_id', $request->vendor_id);
        if ($request->filled('date_from'))
            $q->whereDate('purchase_date', '>=', $request->date_from);
        if ($request->filled('date_to'))
            $q->whereDate('purchase_date', '<=', $request->date_to);
        return $q;
    }

    private function buildExpensesQuery(Request $request)
    {
        $q = Expense::with(['expenseType']);
        if ($request->filled('expense_type_id'))
            $q->where('expense_type_id', $request->expense_type_id);
        if ($request->filled('date_from'))
            $q->whereDate('date', '>=', $request->date_from);
        if ($request->filled('date_to'))
            $q->whereDate('date', '<=', $request->date_to);
        return $q;
    }

    private function buildBookingsQuery(Request $request)
    {
        $q = Booking::with(['customer', 'user', 'items.product']);
        if ($request->filled('customer_id'))
            $q->where('customer_id', $request->customer_id);
        if ($request->filled('status'))
            $q->where('status', $request->status);
        if ($request->filled('date_from'))
            $q->whereDate('booking_date', '>=', $request->date_from);
        if ($request->filled('date_to'))
            $q->whereDate('booking_date', '<=', $request->date_to);
        return $q;
    }

    /* ─────────────────────────────────────────────
       SALES EXPORTS
       ───────────────────────────────────────────── */
    public function salesPdf(Request $request)
    {
        $records = $this->buildSalesQuery($request)->latest('id')->get();
        $filters = array_filter(['Customer' => Customer::find($request->customer_id)?->name, 'From' => $request->date_from, 'To' => $request->date_to]);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.exports.sales-pdf', compact('records', 'filters'))
            ->setPaper('a4', 'landscape');
        return $pdf->stream('sales-report.pdf');
    }

    public function salesCsv(Request $request)
    {
        $records = $this->buildSalesQuery($request)->latest('id')->get();
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="sales-report.csv"'];
        return response()->stream(function () use ($records) {
            $f = fopen('php://output', 'w');
            fputcsv($f, ['#', 'Invoice No', 'Customer', 'Date', 'Total Amount', 'Discount', 'Net Amount']);
            foreach ($records as $i => $r) {
                fputcsv($f, [$i + 1, $r->invoice_no, $r->customer->name ?? '—', $r->sale_date, $r->total_amount, $r->discount_amount, $r->net_amount]);
            }
            fclose($f);
        }, 200, $headers);
    }

    /* ─────────────────────────────────────────────
       PURCHASES EXPORTS
       ───────────────────────────────────────────── */
    public function purchasesPdf(Request $request)
    {
        $records = $this->buildPurchasesQuery($request)->latest('id')->get();
        $filters = array_filter(['Vendor' => Vendor::find($request->vendor_id)?->name, 'From' => $request->date_from, 'To' => $request->date_to]);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.exports.purchases-pdf', compact('records', 'filters'))
            ->setPaper('a4', 'landscape');
        return $pdf->stream('purchases-report.pdf');
    }

    public function purchasesCsv(Request $request)
    {
        $records = $this->buildPurchasesQuery($request)->latest('id')->get();
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="purchases-report.csv"'];
        return response()->stream(function () use ($records) {
            $f = fopen('php://output', 'w');
            fputcsv($f, ['#', 'Invoice No', 'Vendor', 'Date', 'Total Amount', 'Discount', 'Net Amount']);
            foreach ($records as $i => $r) {
                fputcsv($f, [$i + 1, $r->invoice_no, $r->vendor->name ?? '—', $r->purchase_date, $r->total_amount, $r->discount_amount, $r->net_amount]);
            }
            fclose($f);
        }, 200, $headers);
    }

    /* ─────────────────────────────────────────────
       EXPENSES EXPORTS
       ───────────────────────────────────────────── */
    public function expensesPdf(Request $request)
    {
        $records = $this->buildExpensesQuery($request)->latest('id')->get();
        $filters = array_filter(['Type' => \App\Models\ExpenseType::find($request->expense_type_id)?->name, 'From' => $request->date_from, 'To' => $request->date_to]);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.exports.expenses-pdf', compact('records', 'filters'))
            ->setPaper('a4', 'landscape');
        return $pdf->stream('expenses-report.pdf');
    }

    public function expensesCsv(Request $request)
    {
        $records = $this->buildExpensesQuery($request)->latest('id')->get();
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="expenses-report.csv"'];
        return response()->stream(function () use ($records) {
            $f = fopen('php://output', 'w');
            fputcsv($f, ['#', 'Date', 'Type', 'Amount', 'Description']);
            foreach ($records as $i => $r) {
                fputcsv($f, [$i + 1, $r->date, $r->expenseType->name ?? '—', $r->amount, $r->description ?? '']);
            }
            fclose($f);
        }, 200, $headers);
    }

    /* ─────────────────────────────────────────────
       STOCK EXPORTS
       ───────────────────────────────────────────── */
    public function stockPdf(Request $request)
    {
        $records = \App\Models\BatchStock::with(['product.category', 'batch', 'location'])
            ->join('products', 'batch_stocks.product_id', '=', 'products.id')
            ->join('batches', 'batch_stocks.batch_id', '=', 'batches.id')
            ->join('locations', 'batch_stocks.location_id', '=', 'locations.id')
            ->where('products.status', 'active')
            ->whereNull('batches.deleted_at')
            ->select(
                'batch_stocks.*',
                'products.name as product_name',
                'products.sku',
                'products.category_id',
                'products.reorder_level'
            );

        if ($request->filled('category_id')) {
            $records->where('products.category_id', $request->category_id);
        }
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $records->whereRaw('batch_stocks.quantity <= products.reorder_level AND batch_stocks.quantity > 0');
            } elseif ($request->stock_status === 'out') {
                $records->whereRaw('batch_stocks.quantity <= 0');
            } elseif ($request->stock_status === 'in') {
                $records->whereRaw('batch_stocks.quantity > products.reorder_level');
            }
        }

        $records = $records->orderBy('products.name')->orderBy('batches.batch_no')->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.exports.stock-pdf', compact('records'))
            ->setPaper('a4', 'landscape');
        return $pdf->stream('stock-report.pdf');
    }

    public function stockCsv(Request $request)
    {
        $records = \App\Models\BatchStock::with(['product.category', 'batch', 'location'])
            ->join('products', 'batch_stocks.product_id', '=', 'products.id')
            ->join('batches', 'batch_stocks.batch_id', '=', 'batches.id')
            ->join('locations', 'batch_stocks.location_id', '=', 'locations.id')
            ->where('products.status', 'active')
            ->whereNull('batches.deleted_at')
            ->select(
                'batch_stocks.*',
                'products.name as product_name',
                'products.sku',
                'products.category_id',
                'products.reorder_level'
            );

        if ($request->filled('category_id')) {
            $records->where('products.category_id', $request->category_id);
        }
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $records->whereRaw('batch_stocks.quantity <= products.reorder_level AND batch_stocks.quantity > 0');
            } elseif ($request->stock_status === 'out') {
                $records->whereRaw('batch_stocks.quantity <= 0');
            } elseif ($request->stock_status === 'in') {
                $records->whereRaw('batch_stocks.quantity > products.reorder_level');
            }
        }

        $records = $records->orderBy('products.name')->orderBy('batches.batch_no')->get();

        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="stock-report.csv"'];
        return response()->stream(function () use ($records) {
            $f = fopen('php://output', 'w');
            fputcsv($f, ['#', 'Product', 'SKU', 'Category', 'Batch No', 'Location', 'Purchase Price', 'Sale Price', 'Stock Qty', 'Reorder Level', 'Status']);
            foreach ($records as $i => $r) {
                $qty = $r->quantity ?? 0;
                $status = $qty <= 0 ? 'Out of Stock' : ($qty <= $r->reorder_level ? 'Low Stock' : 'In Stock');
                fputcsv($f, [
                    $i + 1,
                    $r->product_name,
                    $r->sku,
                    $r->product->category->name ?? '—',
                    $r->batch->batch_no ?? '—',
                    $r->location->name ?? '—',
                    $r->purchase_price,
                    $r->product->sale_price,
                    $qty,
                    $r->reorder_level,
                    $status
                ]);
            }
            fclose($f);
        }, 200, $headers);
    }

    /**
     * Payment Methods Balance Report
     * Shows total balance per payment method and overall business balance.
     */
    public function paymentMethodsBalance(Request $request)
    {
        // Total received per payment method (sale transactions)
        $received = DB::table('transactions')
            ->join('payment_methods', 'payment_methods.id', '=', 'transactions.payment_method_id')
            ->whereNull('transactions.deleted_at')
            ->whereIn('transactions.transaction_type', ['receipt', 'sale_payment', 'debit'])
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('transactions.transaction_date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('transactions.transaction_date', '<=', $request->date_to))
            ->groupBy('payment_methods.id', 'payment_methods.method_name')
            ->select('payment_methods.id', 'payment_methods.method_name', DB::raw('SUM(transactions.amount) as total'))
            ->pluck('total', 'payment_methods.id');

        // Total paid per payment method (purchase/expense transactions)
        $paid = DB::table('transactions')
            ->join('payment_methods', 'payment_methods.id', '=', 'transactions.payment_method_id')
            ->whereNull('transactions.deleted_at')
            ->whereIn('transactions.transaction_type', ['payment', 'purchase_payment', 'credit'])
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('transactions.transaction_date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('transactions.transaction_date', '<=', $request->date_to))
            ->groupBy('payment_methods.id', 'payment_methods.method_name')
            ->select('payment_methods.id', 'payment_methods.method_name', DB::raw('SUM(transactions.amount) as total'))
            ->pluck('total', 'payment_methods.id');

        // All payment methods
        $methods = DB::table('payment_methods')->whereNull('deleted_at')->orderBy('method_name')->get();

        // Build rows
        $rows = $methods->map(function ($m) use ($received, $paid) {
            $in = $received[$m->id] ?? 0;
            $out = $paid[$m->id] ?? 0;
            return (object) [
                'id' => $m->id,
                'name' => $m->method_name,
                'received' => $in,
                'paid' => $out,
                'balance' => $in - $out,
            ];
        });

        // Overall totals
        $totalReceived = $rows->sum('received');
        $totalPaid = $rows->sum('paid');
        $netBalance = $totalReceived - $totalPaid;

        // Customer opening balances
        $custDebit = \App\Models\Customer::where('opening_balance_type', 'debit')->sum('opening_balance');
        $custCredit = \App\Models\Customer::where('opening_balance_type', 'credit')->sum('opening_balance');

        // Vendor opening balances
        $vendCredit = \App\Models\Vendor::where('opening_balance_type', 'credit')->sum('opening_balance');
        $vendDebit = \App\Models\Vendor::where('opening_balance_type', 'debit')->sum('opening_balance');

        return view('reports.payment-methods-balance', compact(
            'rows',
            'totalReceived',
            'totalPaid',
            'netBalance',
            'custDebit',
            'custCredit',
            'vendCredit',
            'vendDebit'
        ));
    }

    /**
     * Sales Invoice Income Statement — profit per invoice
     */
    public function salesIncomeStatement(Request $request)
    {
        $query = Sale::with(['customer', 'saleItems']);

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

        // Calculate per-invoice profit data
        $salesData = $sales->getCollection()->map(function ($sale) {
            $revenue = $sale->saleItems->sum(fn($i) => $i->quantity * $i->sale_price);
            $cogs = $sale->saleItems->sum(fn($i) => $i->quantity * $i->purchase_price);
            $itemDiscount = $sale->saleItems->sum('discount');
            $invoiceDisc = $sale->discount_amount ?? 0;
            $totalDiscount = $itemDiscount + $invoiceDisc;
            $netRevenue = $revenue - $totalDiscount;
            $grossProfit = $netRevenue - $cogs;
            $profitPct = $netRevenue > 0 ? ($grossProfit / $netRevenue) * 100 : 0;

            $sale->_revenue = $revenue;
            $sale->_cogs = $cogs;
            $sale->_item_discount = $itemDiscount;
            $sale->_invoice_disc = $invoiceDisc;
            $sale->_total_discount = $totalDiscount;
            $sale->_net_revenue = $netRevenue;
            $sale->_gross_profit = $grossProfit;
            $sale->_profit_pct = $profitPct;

            return $sale;
        });

        // Aggregated KPI totals
        $totalRevenue = $salesData->sum('_revenue');
        $totalCogs = $salesData->sum('_cogs');
        $totalDiscount = $salesData->sum('_total_discount');
        $totalNetRev = $salesData->sum('_net_revenue');
        $totalProfit = $salesData->sum('_gross_profit');
        $totalProfitPct = $totalNetRev > 0 ? ($totalProfit / $totalNetRev) * 100 : 0;
        $totalCount = (clone $query)->getQuery()->count();

        $customers = Customer::orderBy('name')->get();

        return view('reports.sales-income-statement', compact(
            'sales',
            'customers',
            'totalRevenue',
            'totalCogs',
            'totalDiscount',
            'totalNetRev',
            'totalProfit',
            'totalProfitPct',
            'totalCount'
        ));
    }

    /**
     * Sales Income Statement — Single invoice show (detailed)
     */
    public function salesIncomeStatementShow(Sale $sale)
    {
        $sale->load(['customer', 'saleItems.product', 'saleItems.location']);

        // Calculate per-item profit
        $items = $sale->saleItems->map(function ($item) {
            $item->_revenue = $item->quantity * $item->sale_price;
            $item->_cost = $item->quantity * $item->purchase_price;
            $item->_discount = $item->discount ?? 0;
            $item->_net = $item->_revenue - $item->_discount;
            $item->_profit = $item->_net - $item->_cost;
            $item->_margin = $item->_net > 0 ? ($item->_profit / $item->_net) * 100 : 0;
            return $item;
        });

        // Invoice totals
        $totals = (object) [
            'revenue' => $items->sum('_revenue'),
            'cogs' => $items->sum('_cost'),
            'item_discount' => $items->sum('_discount'),
            'invoice_disc' => $sale->discount_amount ?? 0,
            'net_revenue' => $items->sum('_net') - ($sale->discount_amount ?? 0),
            'gross_profit' => $items->sum('_profit') - ($sale->discount_amount ?? 0),
        ];
        $totals->margin = $totals->net_revenue > 0 ? ($totals->gross_profit / $totals->net_revenue) * 100 : 0;

        return view('reports.sales-income-statement-show', compact('sale', 'items', 'totals'));
    }

    /**
     * Sales Income Statement — PDF download
     */
    public function salesIncomeStatementPdf(Sale $sale)
    {
        $sale->load(['customer', 'saleItems.product', 'saleItems.location']);

        $items = $sale->saleItems->map(function ($item) {
            $item->_revenue = $item->quantity * $item->sale_price;
            $item->_cost = $item->quantity * $item->purchase_price;
            $item->_discount = $item->discount ?? 0;
            $item->_net = $item->_revenue - $item->_discount;
            $item->_profit = $item->_net - $item->_cost;
            $item->_margin = $item->_net > 0 ? ($item->_profit / $item->_net) * 100 : 0;
            return $item;
        });

        $totals = (object) [
            'revenue' => $items->sum('_revenue'),
            'cogs' => $items->sum('_cost'),
            'item_discount' => $items->sum('_discount'),
            'invoice_disc' => $sale->discount_amount ?? 0,
            'net_revenue' => $items->sum('_net') - ($sale->discount_amount ?? 0),
            'gross_profit' => $items->sum('_profit') - ($sale->discount_amount ?? 0),
        ];
        $totals->margin = $totals->net_revenue > 0 ? ($totals->gross_profit / $totals->net_revenue) * 100 : 0;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.sales-income-statement-pdf', compact('sale', 'items', 'totals'))
            ->setPaper('a4', 'landscape');
        return $pdf->stream("income-statement-{$sale->invoice_no}.pdf");
    }

    /**
     * Bookings Report
     */
    public function bookings(Request $request)
    {
        $query = Booking::with(['customer', 'user', 'items.product']);

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }

        $bookings = $query->latest('id')->paginate(50)->appends($request->query());

        $totalAmount = (clone $query)->getQuery()->sum('net_amount');
        $totalDiscount = (clone $query)->getQuery()->sum('discount_amount');
        $totalCount = (clone $query)->getQuery()->count();
        $pendingCount = (clone $query)->getQuery()->where('status', 'pending')->count();
        $convertedCount = (clone $query)->getQuery()->where('status', 'converted')->count();

        $customers = Customer::orderBy('name')->get();

        return view('reports.bookings', compact(
            'bookings',
            'customers',
            'totalAmount',
            'totalDiscount',
            'totalCount',
            'pendingCount',
            'convertedCount'
        ));
    }

    /* ─────────────────────────────────────────────
       BOOKINGS EXPORTS
       ───────────────────────────────────────────── */
    public function bookingsPdf(Request $request)
    {
        $records = $this->buildBookingsQuery($request)->latest('id')->get();
        $filters = array_filter([
            'Customer' => Customer::find($request->customer_id)?->name,
            'Status' => $request->status ? ucfirst($request->status) : null,
            'From' => $request->date_from,
            'To' => $request->date_to,
        ]);
        $title = 'Bookings Report';
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.exports.bookings-pdf', compact('records', 'filters', 'title'))
            ->setPaper('a4', 'landscape');
        return $pdf->stream('bookings-report.pdf');
    }

    public function bookingsCsv(Request $request)
    {
        $records = $this->buildBookingsQuery($request)->latest('id')->get();
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="bookings-report.csv"'];
        return response()->stream(function () use ($records) {
            $f = fopen('php://output', 'w');
            fputcsv($f, ['#', 'Invoice No', 'Customer', 'Date', 'Status', 'Items', 'Total Amount', 'Discount', 'Net Amount', 'Created By']);
            foreach ($records as $i => $r) {
                fputcsv($f, [
                    $i + 1,
                    $r->invoice_no,
                    $r->customer->name ?? '—',
                    $r->booking_date->format('Y-m-d'),
                    ucfirst($r->status),
                    $r->items->count(),
                    $r->total_amount,
                    $r->discount_amount,
                    $r->net_amount,
                    $r->user->name ?? '—',
                ]);
            }
            fclose($f);
        }, 200, $headers);
    }
}
