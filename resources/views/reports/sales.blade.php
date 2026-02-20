@extends('layouts.app')
@section('title', 'Sales Report')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Sales Report</li>
@endsection

@section('content')
    <!-- Summary Cards -->
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="kpi-card kpi-sales">
                <div class="kpi-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                <div class="kpi-value">{{ number_format($totalCount) }}</div>
                <div class="kpi-label">Total Invoices</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card kpi-revenue">
                <div class="kpi-icon"><i class="fas fa-coins"></i></div>
                <div class="kpi-value">{{ setting('currency_symbol') }}{{ number_format($totalAmount, 2) }}</div>
                <div class="kpi-label">Total Sales Amount</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card kpi-expenses">
                <div class="kpi-icon"><i class="fas fa-percentage"></i></div>
                <div class="kpi-value">{{ setting('currency_symbol') }}{{ number_format($totalDiscount, 2) }}</div>
                <div class="kpi-label">Total Discount Given</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.sales') }}">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Customer</label>
                        <select name="customer_id" class="form-control">
                            <option value="">All Customers</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}</option>
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
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filter</button>
                        <a href="{{ route('reports.sales') }}" class="btn btn-outline-secondary"><i
                                class="fas fa-times me-1"></i>Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-table me-2"></i>Sales Data</h5>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Discount</th>
                        <th class="text-end">Net Amount</th>
                        <th>Created By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td>{{ $loop->iteration + ($sales->currentPage() - 1) * $sales->perPage() }}</td>
                            <td><a href="{{ route('sales.show', $sale->id) }}"><strong>{{ $sale->invoice_no }}</strong></a></td>
                            <td>{{ $sale->customer->name ?? '—' }}</td>
                            <td><small class="text-muted">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</small>
                            </td>
                            <td class="text-end">
                                {{ setting('currency_symbol') }}{{ number_format($sale->total_amount ?? 0, 2) }}</td>
                            <td class="text-end">
                                {{ setting('currency_symbol') }}{{ number_format($sale->discount_amount ?? 0, 2) }}</td>
                            <td class="text-end">
                                <strong>{{ setting('currency_symbol') }}{{ number_format($sale->net_amount ?? 0, 2) }}</strong>
                            </td>
                            <td><small>{{ $sale->user->name ?? '—' }}</small></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No sales found for the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sales->hasPages())
            <div class="card-footer">{{ $sales->links() }}</div>
        @endif
    </div>
@endsection