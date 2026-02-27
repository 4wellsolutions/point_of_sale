@extends('layouts.app')
@section('title', 'Sales Profit Report')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Sales Profit Report</li>
@endsection

@section('content')
    <!-- Summary Cards -->
    <div class="row mb-3">
        <div class="col-md-2 col-6">
            <div class="kpi-card kpi-sales">
                <div class="kpi-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                <div class="kpi-value">{{ format_number($totalCount) }}</div>
                <div class="kpi-label">Invoices</div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="kpi-card kpi-revenue">
                <div class="kpi-icon"><i class="fas fa-coins"></i></div>
                <div class="kpi-value">{{ setting('currency_symbol') }}{{ format_number($totalRevenue) }}</div>
                <div class="kpi-label">Revenue</div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="kpi-card kpi-expenses">
                <div class="kpi-icon"><i class="fas fa-box"></i></div>
                <div class="kpi-value">{{ setting('currency_symbol') }}{{ format_number($totalCogs) }}</div>
                <div class="kpi-label">Cost of Goods</div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="kpi-card" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;">
                <div class="kpi-icon"><i class="fas fa-percentage"></i></div>
                <div class="kpi-value">{{ setting('currency_symbol') }}{{ format_number($totalDiscount) }}</div>
                <div class="kpi-label">Discounts</div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="kpi-card" style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;">
                <div class="kpi-icon"><i class="fas fa-chart-line"></i></div>
                <div class="kpi-value">{{ setting('currency_symbol') }}{{ format_number($totalProfit) }}</div>
                <div class="kpi-label">Gross Profit</div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="kpi-card"
                style="background:linear-gradient(135deg,{{ $totalProfitPct >= 0 ? '#10b981,#059669' : '#ef4444,#dc2626' }});color:#fff;">
                <div class="kpi-icon"><i class="fas fa-chart-pie"></i></div>
                <div class="kpi-value">{{ format_number($totalProfitPct) }}%</div>
                <div class="kpi-label">Profit Margin</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.sales-income-statement') }}">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Customer</label>
                        <select name="customer_id" class="form-control">
                            <option value="">All Customers</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">From Date</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">To Date</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3 d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filter</button>
                        <a href="{{ route('reports.sales-income-statement') }}" class="btn btn-outline-secondary"><i
                                class="fas fa-times me-1"></i>Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Invoice-wise Profit Report</h5>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped align-middle mb-0" style="font-size:0.85rem;">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th class="text-end">Revenue</th>
                        <th class="text-end">COGS</th>
                        <th class="text-end">Item Disc</th>
                        <th class="text-end">Inv. Disc</th>
                        <th class="text-end">Net Revenue</th>
                        <th class="text-end">Gross Profit</th>
                        <th class="text-end">Margin %</th>
                        <th style="width:100px" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td>{{ $loop->iteration + ($sales->currentPage() - 1) * $sales->perPage() }}</td>
                            <td><a
                                    href="{{ route('reports.sales-income-statement.show', $sale->id) }}"><strong>{{ $sale->invoice_no }}</strong></a>
                            </td>
                            <td>{{ $sale->customer->name ?? '—' }}</td>
                            <td><small class="text-muted">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</small>
                            </td>
                            <td class="text-end">{{ setting('currency_symbol') }}{{ format_number($sale->_revenue) }}</td>
                            <td class="text-end">{{ setting('currency_symbol') }}{{ format_number($sale->_cogs) }}</td>
                            <td class="text-end">{{ setting('currency_symbol') }}{{ format_number($sale->_item_discount) }}</td>
                            <td class="text-end">{{ setting('currency_symbol') }}{{ format_number($sale->_invoice_disc) }}</td>
                            <td class="text-end">
                                <strong>{{ setting('currency_symbol') }}{{ format_number($sale->_net_revenue) }}</strong></td>
                            <td class="text-end">
                                <span class="{{ $sale->_gross_profit >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                    {{ setting('currency_symbol') }}{{ format_number($sale->_gross_profit) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <span
                                    class="badge {{ $sale->_profit_pct >= 20 ? 'bg-success' : ($sale->_profit_pct >= 0 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                    {{ format_number($sale->_profit_pct) }}%
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('reports.sales-income-statement.show', $sale->id) }}"
                                    class="btn btn-sm btn-outline-primary" title="View"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('reports.sales-income-statement.pdf', $sale->id) }}" target="_blank"
                                    class="btn btn-sm btn-outline-danger" title="PDF"><i class="fas fa-file-pdf"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="text-center py-4 text-muted">No sales found for the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($sales->count())
                    <tfoot class="table-dark">
                        <tr>
                            <th colspan="4" class="text-end">Page Totals:</th>
                            <th class="text-end">{{ setting('currency_symbol') }}{{ format_number($sales->sum('_revenue')) }}
                            </th>
                            <th class="text-end">{{ setting('currency_symbol') }}{{ format_number($sales->sum('_cogs')) }}</th>
                            <th class="text-end">
                                {{ setting('currency_symbol') }}{{ format_number($sales->sum('_item_discount')) }}</th>
                            <th class="text-end">
                                {{ setting('currency_symbol') }}{{ format_number($sales->sum('_invoice_disc')) }}</th>
                            <th class="text-end">
                                {{ setting('currency_symbol') }}{{ format_number($sales->sum('_net_revenue')) }}</th>
                            <th class="text-end">
                                {{ setting('currency_symbol') }}{{ format_number($sales->sum('_gross_profit')) }}</th>
                            <th class="text-end">
                                @php $pageNetRev = $sales->sum('_net_revenue'); @endphp
                                {{ $pageNetRev > 0 ? format_number(($sales->sum('_gross_profit') / $pageNetRev) * 100) : '0' }}%
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
        @if($sales->hasPages())
            <div class="card-footer">{{ $sales->links() }}</div>
        @endif
    </div>
@endsection