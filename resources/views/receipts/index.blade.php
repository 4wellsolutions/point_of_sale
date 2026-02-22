@extends('layouts.app')

@section('title', 'Receipts')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Receipts</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-hand-holding-usd me-2"></i>Receipts (from Customers)</h5>
            <div class="d-flex gap-2 export-buttons">
                <a href="{{ route('receipts.export.pdf', request()->query()) }}" class="btn btn-sm btn-outline-secondary"
                    target="_blank"><i class="fas fa-file-pdf me-1"></i>PDF</a>
                <a href="{{ route('receipts.export.csv', request()->query()) }}" class="btn btn-sm btn-outline-secondary"><i
                        class="fas fa-file-csv me-1"></i>CSV</a>
                <a href="{{ route('receipts.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>New
                    Receipt</a>
            </div>
        </div>

        <div class="filter-bar mx-3 mt-3">
            <form action="{{ route('receipts.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Customer</label>
                        <select name="customer_id" class="form-select form-select-sm">
                            <option value="">All Customers</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method_id" class="form-select form-select-sm">
                            <option value="">All Methods</option>
                            @foreach($paymentMethods as $method)
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
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary"><i
                                class="fas fa-search me-1"></i>Filter</button>
                        <a href="{{ route('receipts.index') }}" class="btn btn-sm btn-outline-secondary"><i
                                class="fas fa-times"></i></a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            @if($receipts->count() > 0)
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Payment Method</th>
                                <th class="text-end">Amount</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($receipts as $receipt)
                                <tr>
                                    <td>{{ $loop->iteration + ($receipts->currentPage() - 1) * $receipts->perPage() }}</td>
                                    <td>{{ \Carbon\Carbon::parse($receipt->transaction_date)->format('d M Y') }}</td>
                                    <td>{{ $receipt->transactionable->name ?? '—' }}</td>
                                    <td>{{ $receipt->paymentMethod->name ?? '—' }}</td>
                                    <td class="text-end fw-bold text-success">
                                        {{ setting('currency_symbol', 'Rs.') }} {{ number_format($receipt->amount, 2) }}
                                    </td>
                                    <td class="text-center action-btns">
                                        <button class="btn btn-sm btn-danger" onclick="confirmDelete({{ $receipt->id }})"
                                            title="Delete"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td colspan="4" class="text-end">Total:</td>
                                <td class="text-end text-success">
                                    {{ setting('currency_symbol', 'Rs.') }} {{ number_format($receipts->sum('amount'), 2) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="d-flex justify-content-center p-3">{{ $receipts->withQueryString()->links() }}</div>
            @else
                <div class="empty-state">
                    <i class="fas fa-hand-holding-usd"></i>
                    <p>No receipts found</p>
                    <a href="{{ route('receipts.create') }}" class="btn btn-primary btn-sm"><i
                            class="fas fa-plus me-1"></i>Record Receipt</a>
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
                <div class="modal-body">Are you sure you want to delete this receipt? The ledger will be reversed.</div>
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
            document.getElementById('deleteForm').action = '/receipts/' + id;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
@endpush