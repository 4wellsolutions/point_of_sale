@extends('layouts.app')

@section('title', 'Transactions')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Transactions</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>All Transactions</h5>
            <div class="d-flex gap-2 export-buttons">
                <a href="{{ route('transactions.export.pdf', request()->query()) }}"
                    class="btn btn-sm btn-outline-secondary"><i class="fas fa-file-pdf me-1"></i>PDF</a>
                <a href="{{ route('transactions.export.csv', request()->query()) }}"
                    class="btn btn-sm btn-outline-secondary"><i class="fas fa-file-csv me-1"></i>CSV</a>
                <a href="{{ route('transactions.create') }}" class="btn btn-sm btn-primary"><i
                        class="fas fa-plus me-1"></i>New Transaction</a>
            </div>
        </div>

        <div class="filter-bar mx-3 mt-3">
            <form action="{{ route('transactions.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select form-select-sm">
                            <option value="">All Types</option>
                            <option value="payment" {{ request('type') == 'payment' ? 'selected' : '' }}>Payment</option>
                            <option value="receipt" {{ request('type') == 'receipt' ? 'selected' : '' }}>Receipt</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method_id" class="form-select form-select-sm">
                            <option value="">All Methods</option>
                            @foreach($paymentMethods ?? [] as $method)
                                <option value="{{ $method->id }}" {{ request('payment_method_id') == $method->id ? 'selected' : '' }}>{{ $method->name }}</option>
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
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary"><i
                                class="fas fa-search me-1"></i>Filter</button>
                        <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-secondary"><i
                                class="fas fa-times"></i></a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            @if($transactions->count() > 0)
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Party</th>
                                <th>Payment Method</th>
                                <th class="text-end">Amount</th>
                                <th>Reference</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{ $loop->iteration + ($transactions->currentPage() - 1) * $transactions->perPage() }}</td>
                                    <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d M Y') }}</td>
                                    <td>
                                        @if($transaction->type == 'payment')
                                            <span class="badge bg-danger">Payment</span>
                                        @else
                                            <span class="badge bg-success">Receipt</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $transaction->vendor->name ?? $transaction->customer->name ?? '—' }}
                                    </td>
                                    <td>{{ $transaction->paymentMethod->name ?? '—' }}</td>
                                    <td class="text-end fw-bold">${{ number_format($transaction->amount, 2) }}</td>
                                    <td><small class="text-muted">{{ $transaction->reference ?? '—' }}</small></td>
                                    <td class="text-center action-btns">
                                        <a href="{{ route('transactions.edit', $transaction->id) }}" class="btn btn-sm btn-warning"
                                            title="Edit"><i class="fas fa-edit"></i></a>
                                        <button class="btn btn-sm btn-danger" onclick="confirmDelete({{ $transaction->id }})"
                                            title="Delete"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center p-3">{{ $transactions->withQueryString()->links() }}</div>
            @else
                <div class="empty-state">
                    <i class="fas fa-exchange-alt"></i>
                    <p>No transactions found</p>
                    <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-sm"><i
                            class="fas fa-plus me-1"></i>New Transaction</a>
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
                <div class="modal-body">Are you sure you want to delete this transaction?</div>
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
            document.getElementById('deleteForm').action = '/transactions/' + id;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
@endpush