@extends('layouts.app')
@section('title', 'Purchase Report')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Purchase Report</li>
@endsection

@section('content')
    <!-- Summary Cards -->
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="kpi-card kpi-purchases">
                <div class="kpi-icon"><i class="fas fa-file-invoice"></i></div>
                <div class="kpi-value">{{ number_format($totalCount) }}</div>
                <div class="kpi-label">Total Purchases</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card kpi-revenue">
                <div class="kpi-icon"><i class="fas fa-coins"></i></div>
                <div class="kpi-value">{{ setting('currency_symbol') }}{{ number_format($totalAmount, 2) }}</div>
                <div class="kpi-label">Total Purchase Amount</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card kpi-expenses">
                <div class="kpi-icon"><i class="fas fa-percentage"></i></div>
                <div class="kpi-value">{{ setting('currency_symbol') }}{{ number_format($totalDiscount, 2) }}</div>
                <div class="kpi-label">Total Discount Received</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.purchases') }}">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Vendor</label>
                        <select name="vendor_id" class="form-control">
                            <option value="">All Vendors</option>
                            @foreach($vendors as $v)
                                <option value="{{ $v->id }}" {{ request('vendor_id') == $v->id ? 'selected' : '' }}>{{ $v->name }}
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
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filter</button>
                        <a href="{{ route('reports.purchases') }}" class="btn btn-outline-secondary"><i
                                class="fas fa-times me-1"></i>Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-table me-2"></i>Purchase Data</h5>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Invoice</th>
                        <th>Vendor</th>
                        <th>Date</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Discount</th>
                        <th class="text-end">Net Amount</th>
                        <th>Created By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                        <tr>
                            <td>{{ $loop->iteration + ($purchases->currentPage() - 1) * $purchases->perPage() }}</td>
                            <td><a
                                    href="{{ route('purchases.show', $purchase->id) }}"><strong>{{ $purchase->invoice_no }}</strong></a>
                            </td>
                            <td>{{ $purchase->vendor->name ?? '—' }}</td>
                            <td><small class="text-muted">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</small>
                            </td>
                            <td class="text-end">
                                {{ setting('currency_symbol') }}{{ number_format($purchase->total_amount ?? 0, 2) }}</td>
                            <td class="text-end">
                                {{ setting('currency_symbol') }}{{ number_format($purchase->discount_amount ?? 0, 2) }}</td>
                            <td class="text-end">
                                <strong>{{ setting('currency_symbol') }}{{ number_format($purchase->net_amount ?? 0, 2) }}</strong>
                            </td>
                            <td><small>{{ $purchase->user->name ?? '—' }}</small></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No purchases found for the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($purchases->hasPages())
            <div class="card-footer">{{ $purchases->links() }}</div>
        @endif
    </div>
@endsection