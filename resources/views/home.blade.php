@extends('layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
    <!-- KPI Cards Row -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="kpi-card kpi-sales">
                <i class="fas fa-chart-line kpi-icon"></i>
                <div class="kpi-value">{{ setting('currency_symbol', '$') }}{{ number_format($todaySales, 0) }}</div>
                <div class="kpi-label">Today's Sales</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="kpi-card kpi-purchases">
                <i class="fas fa-shopping-cart kpi-icon"></i>
                <div class="kpi-value">{{ setting('currency_symbol', '$') }}{{ number_format($todayPurchases, 0) }}</div>
                <div class="kpi-label">Today's Purchases</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="kpi-card kpi-revenue">
                <i class="fas fa-dollar-sign kpi-icon"></i>
                <div class="kpi-value">{{ setting('currency_symbol', '$') }}{{ number_format($monthSales, 0) }}</div>
                <div class="kpi-label">Monthly Revenue</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="kpi-card kpi-expenses">
                <i class="fas fa-money-bill-wave kpi-icon"></i>
                <div class="kpi-value">{{ setting('currency_symbol', '$') }}{{ number_format($monthExpenses, 0) }}</div>
                <div class="kpi-label">Monthly Expenses</div>
            </div>
        </div>
    </div>

    <!-- Secondary Stats -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                        <i class="fas fa-shopping-bag text-primary fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ setting('currency_symbol', '$') }}{{ number_format($monthPurchases, 0) }}</h5>
                        <small class="text-muted">Monthly Purchases</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                        <i class="fas fa-users text-success fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $totalCustomers }}</h5>
                        <small class="text-muted">Total Customers</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                        <i class="fas fa-truck text-info fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $totalVendors }}</h5>
                        <small class="text-muted">Total Vendors</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                        <i class="fas fa-box-open text-warning fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $totalProducts }}</h5>
                        <small class="text-muted">Active Products</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Monthly Overview</h5>
                </div>
                <div class="card-body chart-container">
                    <canvas id="monthlyChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-trophy me-2 text-warning"></i>Top Products</h5>
                </div>
                <div class="card-body p-0">
                    @if($topProducts->count() > 0)
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <tbody>
                                    @foreach($topProducts as $item)
                                        <tr>
                                            <td>
                                                <strong>{{ $item->product->name ?? 'N/A' }}</strong>
                                                <br><small class="text-muted">{{ $item->total_qty }} units sold</small>
                                            </td>
                                            <td class="text-end">
                                                <span
                                                    class="fw-bold text-success">{{ setting('currency_symbol', '$') }}{{ number_format($item->total_revenue, 0) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state py-4">
                            <i class="fas fa-chart-pie text-muted"></i>
                            <p class="mb-0">No sales this month</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Row -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-receipt me-2 text-success"></i>Recent Sales</h5>
                    <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
                </div>
                <div class="card-body p-0">
                    @if($recentSales->count() > 0)
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Date</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentSales as $sale)
                                        <tr>
                                            <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                                            <td><small>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</small></td>
                                            <td class="text-end fw-bold">{{ setting('currency_symbol', '$') }}{{ number_format($sale->net_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state py-4">
                            <i class="fas fa-receipt text-muted"></i>
                            <p class="mb-0">No recent sales</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart me-2 text-primary"></i>Recent Purchases</h5>
                    <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
                </div>
                <div class="card-body p-0">
                    @if($recentPurchases->count() > 0)
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>Vendor</th>
                                        <th>Date</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentPurchases as $purchase)
                                        <tr>
                                            <td>{{ $purchase->vendor->name ?? 'N/A' }}</td>
                                            <td><small>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</small>
                                            </td>
                                            <td class="text-end fw-bold">{{ setting('currency_symbol', '$') }}{{ number_format($purchase->net_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state py-4">
                            <i class="fas fa-shopping-cart text-muted"></i>
                            <p class="mb-0">No recent purchases</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Alerts -->
    @if($lowStockProducts->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-start border-danger border-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2 text-danger"></i>Low Stock Alerts</h5>
                        <span class="badge bg-danger">{{ $lowStockProducts->count() }} items</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-center">Current Stock</th>
                                        <th class="text-center">Reorder Level</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStockProducts as $product)
                                        <tr class="low-stock-row">
                                            <td><strong>{{ $product->name }}</strong></td>
                                            <td class="text-center">
                                                <span class="badge bg-danger">{{ $product->current_stock_qty }}</span>
                                            </td>
                                            <td class="text-center">{{ $product->reorder_level }}</td>
                                            <td class="text-center">
                                                @if($product->current_stock_qty <= 0)
                                                    <span class="badge bg-danger">Out of Stock</span>
                                                @else
                                                    <span class="badge bg-warning">Low Stock</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartData = @json($chartData);
            const ctx = document.getElementById('monthlyChart').getContext('2d');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.map(d => d.label),
                    datasets: [
                        {
                            label: 'Sales',
                            data: chartData.map(d => d.sales),
                            backgroundColor: 'rgba(16, 185, 129, 0.8)',
                            borderRadius: 6,
                            barPercentage: 0.6,
                        },
                        {
                            label: 'Purchases',
                            data: chartData.map(d => d.purchases),
                            backgroundColor: 'rgba(59, 130, 246, 0.8)',
                            borderRadius: 6,
                            barPercentage: 0.6,
                        },
                        {
                            label: 'Expenses',
                            data: chartData.map(d => d.expenses),
                            backgroundColor: 'rgba(245, 158, 11, 0.8)',
                            borderRadius: 6,
                            barPercentage: 0.6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: { family: 'Inter', size: 12 }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: {
                                font: { family: 'Inter', size: 11 },
                                callback: v => '$' + v.toLocaleString()
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'Inter', size: 11 } }
                        }
                    }
                }
            });
        });
    </script>
@endpush