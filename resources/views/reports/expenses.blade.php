@extends('layouts.app')
@section('title', 'Expense Report')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Expense Report</li>
@endsection

@section('content')
    <!-- Summary Cards -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="kpi-card kpi-expenses">
                <div class="kpi-icon"><i class="fas fa-receipt"></i></div>
                <div class="kpi-value">{{ number_format($totalCount) }}</div>
                <div class="kpi-label">Total Expense Entries</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="kpi-card" style="background: linear-gradient(135deg, #ef4444, #dc2626)">
                <div class="kpi-icon"><i class="fas fa-money-bill-wave"></i></div>
                <div class="kpi-value">{{ setting('currency_symbol') }}{{ number_format($totalAmount, 2) }}</div>
                <div class="kpi-label">Total Expense Amount</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.expenses') }}">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Expense Type</label>
                        <select name="expense_type_id" class="form-control">
                            <option value="">All Types</option>
                            @foreach($expenseTypes as $et)
                                <option value="{{ $et->id }}" {{ request('expense_type_id') == $et->id ? 'selected' : '' }}>
                                    {{ $et->name }}</option>
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
                        <a href="{{ route('reports.expenses') }}" class="btn btn-outline-secondary"><i
                                class="fas fa-times me-1"></i>Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-table me-2"></i>Expense Data</h5>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th class="text-end">Amount</th>
                        <th>Created By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr>
                            <td>{{ $loop->iteration + ($expenses->currentPage() - 1) * $expenses->perPage() }}</td>
                            <td><small class="text-muted">{{ \Carbon\Carbon::parse($expense->date)->format('d M Y') }}</small>
                            </td>
                            <td><span class="badge bg-info">{{ $expense->expenseType->name ?? '—' }}</span></td>
                            <td>{{ Str::limit($expense->description ?? '—', 60) }}</td>
                            <td class="text-end">
                                <strong>{{ setting('currency_symbol') }}{{ number_format($expense->amount ?? 0, 2) }}</strong>
                            </td>
                            <td><small>{{ $expense->user->name ?? '—' }}</small></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No expenses found for the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($expenses->hasPages())
            <div class="card-footer">{{ $expenses->links() }}</div>
        @endif
    </div>
@endsection