@extends('layouts.app')

@section('title', 'Expenses')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Expenses</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>All Expenses</h5>
            <div class="d-flex gap-2 export-buttons">
                <a href="{{ route('expenses.export.pdf', request()->query()) }}" class="btn btn-sm btn-outline-secondary"
                    target="_blank"><i class="fas fa-file-pdf me-1"></i>PDF</a>
                <a href="{{ route('expenses.export.csv', request()->query()) }}" class="btn btn-sm btn-outline-secondary"><i
                        class="fas fa-file-csv me-1"></i>CSV</a>
                <a href="{{ route('expenses.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>Add
                    Expense</a>
            </div>
        </div>

        <div class="filter-bar mx-3 mt-3">
            <form action="{{ route('expenses.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Expense Type</label>
                        <select name="expense_type_id" class="form-select form-select-sm">
                            <option value="">All Types</option>
                            @foreach($expenseTypes ?? [] as $type)
                                <option value="{{ $type->id }}" {{ request('expense_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">From Date</label>
                        <input type="date" name="from_date" class="form-control form-control-sm"
                            value="{{ request('from_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">To Date</label>
                        <input type="date" name="to_date" class="form-control form-control-sm"
                            value="{{ request('to_date') }}">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary"><i
                                class="fas fa-search me-1"></i>Filter</button>
                        <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-secondary"><i
                                class="fas fa-times"></i></a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            @if($expenses->count() > 0)
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th class="text-end">Amount</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expenses as $expense)
                                <tr>
                                    <td>{{ $loop->iteration + ($expenses->currentPage() - 1) * $expenses->perPage() }}</td>
                                    <td>{{ \Carbon\Carbon::parse($expense->date)->format('d M Y') }}</td>
                                    <td><span class="badge bg-warning">{{ $expense->expenseType->name ?? '—' }}</span></td>
                                    <td>{{ Str::limit($expense->description, 50) }}</td>
                                    <td class="text-end fw-bold">
                                        {{ setting('currency_symbol', '$') }}{{ number_format($expense->amount, 2) }}</td>
                                    <td class="text-center action-btns">
                                        <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-sm btn-warning"
                                            title="Edit"><i class="fas fa-edit"></i></a>
                                        <button class="btn btn-sm btn-danger" onclick="confirmDelete({{ $expense->id }})"
                                            title="Delete"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center p-3">{{ $expenses->withQueryString()->links() }}</div>
            @else
                <div class="empty-state">
                    <i class="fas fa-money-bill-wave"></i>
                    <p>No expenses found</p>
                    <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Add
                        First Expense</a>
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">Are you sure you want to delete this expense?</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" class="d-inline">@csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash me-1"></i>Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete(id) {
            document.getElementById('deleteForm').action = '/expenses/' + id;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
@endpush