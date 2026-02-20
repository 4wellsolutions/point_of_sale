<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\FlavourController;
use App\Http\Controllers\PackingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\StockLossDamageController;
use App\Http\Controllers\SalesReturnController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\InventoryTransactionController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\ExpenseTypeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StockAlertController;

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/home', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Stock Alerts
    Route::get('/stock-alerts', [StockAlertController::class, 'index'])->name('stock-alerts.index');

    // Cache clear (protected)
    Route::get('/clear-cache', function () {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        return response()->json(['message' => 'All caches cleared successfully!']);
    })->name('clear.cache');

    Route::get('/user-login', [ProfileController::class, "loginUserById"]);

    Route::resource('types', TypeController::class);
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/password/change', [ProfileController::class, 'password'])->name('change.password');
    Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
    Route::get('/customers/export/pdf', [CustomerController::class, 'exportPdf'])->name('customers.export.pdf');
    Route::get('/customers/export/csv', [CustomerController::class, 'exportCsv'])->name('customers.export.csv');
    Route::resource('customers', CustomerController::class);
    Route::get('/vendors/search', [VendorController::class, 'search'])->name('vendors.search');
    Route::get('/vendors/export/pdf', [VendorController::class, 'exportPdf'])->name('vendors.export.pdf');
    Route::get('/vendors/export/csv', [VendorController::class, 'exportCsv'])->name('vendors.export.csv');
    Route::resource('vendors', VendorController::class);
    Route::get('/generate-invoice', [PurchaseController::class, 'generateInvoiceNo'])->name('purchases.generate.invoice');

    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
    Route::get('/products/export/pdf', [ProductController::class, 'exportPdf'])->name('products.export.pdf');
    Route::get('/products/export/csv', [ProductController::class, 'exportCsv'])->name('products.export.csv');
    Route::resource('products', ProductController::class);
    Route::get('purchases/search', [PurchaseController::class, 'searchPurchases'])->name('purchases.search');
    Route::get('/purchases/export/pdf', [PurchaseController::class, 'exportPdf'])->name('purchases.export.pdf');
    Route::get('/purchases/export/csv', [PurchaseController::class, 'exportCsv'])->name('purchases.export.csv');
    Route::resource('purchases', PurchaseController::class);
    Route::get('/purchases/{purchase}/pdf', [PurchaseController::class, 'generatePdf'])->name('purchases.pdf');
    Route::get('/batches/{batch}/products/{product}/locations', [BatchController::class, 'locations'])
        ->name('batches.products.locations');
    Route::get('/products/{id}/batches', [ProductController::class, 'getBatches'])
        ->name('products.batches');

    Route::resource('purchase-returns', PurchaseReturnController::class);

    // Custom route for generating return number
    Route::get('/generate-return-no', [PurchaseReturnController::class, 'generateReturnNo'])->name('purchase-returns.generateReturnNo');
    Route::resource('stock-loss-damage', StockLossDamageController::class);
    Route::resource('sales-returns', SalesReturnController::class);
    Route::get('/sales/export/pdf', [SalesController::class, 'exportPdf'])->name('sales.export.pdf');
    Route::get('/sales/export/csv', [SalesController::class, 'exportCsv'])->name('sales.export.csv');
    Route::resource('sales', SalesController::class);
    Route::get('/sales/{sale}/pdf', [SalesController::class, 'generatePdf'])->name('sales.pdf');
    Route::resource('flavours', FlavourController::class);
    Route::resource('packings', PackingController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('payment_methods', PaymentMethodController::class);
    Route::get('/transactions/export/pdf', [TransactionController::class, 'exportPdf'])->name('transactions.export.pdf');
    Route::get('/transactions/export/csv', [TransactionController::class, 'exportCsv'])->name('transactions.export.csv');
    Route::resource('transactions', TransactionController::class);
    Route::get('/ledgers', [LedgerController::class, 'index'])->name('ledgers.index');
    Route::get('/ledgers/pdf', [LedgerController::class, 'generatePDF'])->name('ledgers.pdf');
    Route::resource('locations', LocationController::class);
    Route::get('/inventory', [InventoryTransactionController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/view-pdf', [InventoryTransactionController::class, 'viewPdf'])->name('inventory.viewPdf');
    Route::get('/inventory/export/csv', [InventoryTransactionController::class, 'exportCsv'])->name('inventory.export.csv');
    Route::resource('expense-types', ExpenseTypeController::class);
    Route::get('/expenses/export/pdf', [ExpenseController::class, 'exportPdf'])->name('expenses.export.pdf');
    Route::get('/expenses/export/csv', [ExpenseController::class, 'exportCsv'])->name('expenses.export.csv');
    Route::resource('expenses', ExpenseController::class);
});

Route::get('/', function () {
    return redirect('/login');
});
Auth::routes();

