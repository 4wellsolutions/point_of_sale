@extends('layouts.app')
@section('title', 'Income Statement — ' . $sale->invoice_no)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.sales-income-statement') }}">Sales Profit Report</a></li>
    <li class="breadcrumb-item active">{{ $sale->invoice_no }}</li>
@endsection

@section('content')

    {{-- Header Banner --}}
    <div class="detail-header mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="detail-icon-wrapper">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <h2 class="mb-1" style="font-weight:700; font-size:1.5rem;">Income Statement — {{ $sale->invoice_no }}
                    </h2>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="detail-id-badge">{{ $sale->customer->name ?? '—' }}</span>
                        <span class="badge bg-info">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</span>
                        <span class="badge {{ $totals->gross_profit >= 0 ? 'bg-success' : 'bg-danger' }}">
                            Profit: {{ setting('currency_symbol') }}{{ format_number($totals->gross_profit) }}
                            ({{ format_number($totals->margin) }}%)
                        </span>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('reports.sales-income-statement.pdf', $sale) }}" target="_blank"
                    class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i> PDF
                </a>
                <a href="{{ route('sales.show', $sale) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-eye me-1"></i> Sale Detail
                </a>
                <a href="{{ route('reports.sales-income-statement') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    {{-- Financial Summary --}}
    <div class="card mb-4">
        <div class="card-body p-0">
            <div class="row g-0">
                <div class="col-sm-2">
                    <div class="stat-tile">
                        <div class="stat-tile-icon" style="background:rgba(59,130,246,.1); color:#3b82f6;">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div>
                            <div class="stat-tile-value">
                                {{ setting('currency_symbol') }}{{ format_number($totals->revenue) }}</div>
                            <div class="stat-tile-label">Revenue</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="stat-tile">
                        <div class="stat-tile-icon" style="background:rgba(239,68,68,.1); color:#ef4444;">
                            <i class="fas fa-box"></i>
                        </div>
                        <div>
                            <div class="stat-tile-value">{{ setting('currency_symbol') }}{{ format_number($totals->cogs) }}
                            </div>
                            <div class="stat-tile-label">Cost of Goods</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="stat-tile">
                        <div class="stat-tile-icon" style="background:rgba(245,158,11,.1); color:#f59e0b;">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div>
                            <div class="stat-tile-value">
                                {{ setting('currency_symbol') }}{{ format_number($totals->item_discount + $totals->invoice_disc) }}
                            </div>
                            <div class="stat-tile-label">Total Discount</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="stat-tile">
                        <div class="stat-tile-icon" style="background:rgba(99,102,241,.1); color:#6366f1;">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div>
                            <div class="stat-tile-value">
                                {{ setting('currency_symbol') }}{{ format_number($totals->net_revenue) }}</div>
                            <div class="stat-tile-label">Net Revenue</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="stat-tile">
                        <div class="stat-tile-icon" style="background:rgba(16,185,129,.1); color:#10b981;">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <div class="stat-tile-value"
                                style="color:{{ $totals->gross_profit >= 0 ? '#10b981' : '#ef4444' }}">
                                {{ setting('currency_symbol') }}{{ format_number($totals->gross_profit) }}
                            </div>
                            <div class="stat-tile-label">Gross Profit</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="stat-tile">
                        <div class="stat-tile-icon" style="background:rgba(16,185,129,.1); color:#10b981;">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div>
                            <div class="stat-tile-value" style="color:{{ $totals->margin >= 0 ? '#10b981' : '#ef4444' }}">
                                {{ format_number($totals->margin) }}%
                            </div>
                            <div class="stat-tile-label">Profit Margin</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sale Info --}}
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="fas fa-info-circle" style="color:var(--primary);"></i>
            <h3 class="card-title mb-0">Invoice Information</h3>
        </div>
        <div class="card-body p-0">
            <div class="row g-0">
                <div class="col-sm-4">
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-user"></i> Customer</span>
                        <span class="detail-value">{{ $sale->customer->name ?? '—' }}</span>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-file-invoice"></i> Invoice No</span>
                        <span class="detail-value font-monospace">{{ $sale->invoice_no }}</span>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-calendar"></i> Sale Date</span>
                        <span class="detail-value">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Items Profit Breakdown --}}
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="fas fa-list" style="color:var(--primary);"></i>
            <h3 class="card-title mb-0">Item-wise Profit Breakdown</h3>
            <span class="badge bg-info ms-auto">{{ $items->count() }} items</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:0.85rem;">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Sale Price</th>
                            <th class="text-end">Cost Price</th>
                            <th class="text-end">Revenue</th>
                            <th class="text-end">Cost</th>
                            <th class="text-end">Discount</th>
                            <th class="text-end">Net</th>
                            <th class="text-end">Profit</th>
                            <th class="text-end">Margin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-medium">{{ $item->product->name ?? '—' }}</td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end">{{ format_number($item->sale_price) }}</td>
                                <td class="text-end">{{ format_number($item->purchase_price) }}</td>
                                <td class="text-end">{{ setting('currency_symbol') }}{{ format_number($item->_revenue) }}</td>
                                <td class="text-end">{{ setting('currency_symbol') }}{{ format_number($item->_cost) }}</td>
                                <td class="text-end">{{ setting('currency_symbol') }}{{ format_number($item->_discount) }}</td>
                                <td class="text-end">
                                    <strong>{{ setting('currency_symbol') }}{{ format_number($item->_net) }}</strong></td>
                                <td class="text-end">
                                    <span class="{{ $item->_profit >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                        {{ setting('currency_symbol') }}{{ format_number($item->_profit) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <span
                                        class="badge {{ $item->_margin >= 20 ? 'bg-success' : ($item->_margin >= 0 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ format_number($item->_margin) }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <th colspan="5" class="text-end">Subtotals:</th>
                            <th class="text-end">{{ setting('currency_symbol') }}{{ format_number($totals->revenue) }}</th>
                            <th class="text-end">{{ setting('currency_symbol') }}{{ format_number($totals->cogs) }}</th>
                            <th class="text-end">{{ setting('currency_symbol') }}{{ format_number($totals->item_discount) }}
                            </th>
                            <th class="text-end">
                                {{ setting('currency_symbol') }}{{ format_number($totals->net_revenue + $totals->invoice_disc) }}
                            </th>
                            <th class="text-end">
                                {{ setting('currency_symbol') }}{{ format_number($totals->gross_profit + $totals->invoice_disc) }}
                            </th>
                            <th></th>
                        </tr>
                        @if($totals->invoice_disc > 0)
                            <tr>
                                <th colspan="8" class="text-end">Invoice Discount:</th>
                                <th class="text-end" colspan="2">-
                                    {{ setting('currency_symbol') }}{{ format_number($totals->invoice_disc) }}</th>
                                <th></th>
                            </tr>
                        @endif
                        <tr style="font-size:1rem;">
                            <th colspan="8" class="text-end">Net Profit:</th>
                            <th class="text-end">{{ setting('currency_symbol') }}{{ format_number($totals->net_revenue) }}
                            </th>
                            <th class="text-end" style="color:{{ $totals->gross_profit >= 0 ? '#10b981' : '#ef4444' }}">
                                {{ setting('currency_symbol') }}{{ format_number($totals->gross_profit) }}
                            </th>
                            <th class="text-end">{{ format_number($totals->margin) }}%</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

@endsection