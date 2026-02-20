@extends('layouts.app')
@section('title', 'Profit & Loss Report')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Profit & Loss</li>
@endsection

@section('content')
    <!-- Date Filter -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.profit-loss') }}">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">From Date</label>
                        <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">To Date</label>
                        <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Generate</button>
                        <a href="{{ route('reports.profit-loss') }}" class="btn btn-outline-secondary"><i
                                class="fas fa-times me-1"></i>Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- P&L Summary Cards -->
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="kpi-card kpi-sales">
                <div class="kpi-icon"><i class="fas fa-arrow-up"></i></div>
                <div class="kpi-value">{{ setting('currency_symbol') }}{{ number_format($salesRevenue, 2) }}</div>
                <div class="kpi-label">Sales Revenue</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card kpi-purchases">
                <div class="kpi-icon"><i class="fas fa-arrow-down"></i></div>
                <div class="kpi-value">{{ setting('currency_symbol') }}{{ number_format($cogs, 2) }}</div>
                <div class="kpi-label">Cost of Goods Sold</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card"
                style="background: linear-gradient(135deg, {{ $grossProfit >= 0 ? '#10b981, #059669' : '#ef4444, #dc2626' }})">
                <div class="kpi-icon"><i class="fas fa-balance-scale"></i></div>
                <div class="kpi-value">{{ setting('currency_symbol') }}{{ number_format(abs($grossProfit), 2) }}</div>
                <div class="kpi-label">Gross {{ $grossProfit >= 0 ? 'Profit' : 'Loss' }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card"
                style="background: linear-gradient(135deg, {{ $netProfit >= 0 ? '#10b981, #059669' : '#ef4444, #dc2626' }})">
                <div class="kpi-icon"><i class="fas fa-chart-line"></i></div>
                <div class="kpi-value">{{ setting('currency_symbol') }}{{ number_format(abs($netProfit), 2) }}</div>
                <div class="kpi-label">Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</div>
            </div>
        </div>
    </div>

    <!-- P&L Statement -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Profit & Loss Statement
                <small class="text-muted ms-2">{{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} —
                    {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</small>
            </h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <tbody>
                    <tr class="table-light">
                        <td colspan="2"><strong><i class="fas fa-plus-circle text-success me-2"></i>Revenue</strong></td>
                    </tr>
                    <tr>
                        <td class="ps-4">Sales Revenue</td>
                        <td class="text-end">
                            <strong>{{ setting('currency_symbol') }}{{ number_format($salesRevenue, 2) }}</strong></td>
                    </tr>

                    <tr class="table-light">
                        <td colspan="2"><strong><i class="fas fa-minus-circle text-danger me-2"></i>Cost of Goods
                                Sold</strong></td>
                    </tr>
                    <tr>
                        <td class="ps-4">Purchase Cost of Sold Items</td>
                        <td class="text-end text-danger">{{ setting('currency_symbol') }}{{ number_format($cogs, 2) }}</td>
                    </tr>

                    <tr class="{{ $grossProfit >= 0 ? 'table-success' : 'table-danger' }}">
                        <td><strong>Gross {{ $grossProfit >= 0 ? 'Profit' : 'Loss' }}</strong></td>
                        <td class="text-end">
                            <strong>{{ setting('currency_symbol') }}{{ number_format(abs($grossProfit), 2) }}</strong></td>
                    </tr>

                    <tr class="table-light">
                        <td colspan="2"><strong><i class="fas fa-money-bill-wave text-warning me-2"></i>Operating
                                Expenses</strong></td>
                    </tr>
                    @forelse($expensesByType as $exp)
                        <tr>
                            <td class="ps-4">{{ $exp->expenseType->name ?? 'Unknown' }}</td>
                            <td class="text-end text-danger">{{ setting('currency_symbol') }}{{ number_format($exp->total, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="ps-4 text-muted" colspan="2">No expenses recorded in this period</td>
                        </tr>
                    @endforelse
                    <tr>
                        <td class="ps-4"><strong>Total Expenses</strong></td>
                        <td class="text-end text-danger">
                            <strong>{{ setting('currency_symbol') }}{{ number_format($totalExpenses, 2) }}</strong></td>
                    </tr>

                    <tr class="{{ $netProfit >= 0 ? 'table-success' : 'table-danger' }}" style="font-size: 1.1rem;">
                        <td><strong><i class="fas fa-chart-line me-2"></i>Net
                                {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</strong></td>
                        <td class="text-end">
                            <strong>{{ setting('currency_symbol') }}{{ number_format(abs($netProfit), 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Purchases Reference -->
    <div class="card mt-3">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Reference</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Total Purchases (this period):</strong>
                        {{ setting('currency_symbol') }}{{ number_format($totalPurchases, 2) }}</p>
                    <small class="text-muted">Note: Total purchases is shown for reference — COGS (cost of goods actually
                        sold) is used for profit calculation.</small>
                </div>
            </div>
        </div>
    </div>
@endsection