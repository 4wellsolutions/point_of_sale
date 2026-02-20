@extends('layouts.app')

@section('title', 'Purchases')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Purchases</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>All Purchases</h5>
            <div class="d-flex gap-2 export-buttons">
                <a href="{{ route('purchases.export.pdf', request()->query()) }}" class="btn btn-sm btn-outline-secondary"
                    title="Export PDF" target="_blank">
                    <i class="fas fa-file-pdf me-1"></i>PDF
                </a>
                <a href="{{ route('purchases.export.csv', request()->query()) }}" class="btn btn-sm btn-outline-secondary"
                    title="Export CSV">
                    <i class="fas fa-file-csv me-1"></i>CSV
                </a>
                <a href="{{ route('purchases.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i>New Purchase
                </a>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar mx-3 mt-3">
            <form action="{{ route('purchases.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Vendor</label>
                        <select name="vendor_id" class="form-select form-select-sm">
                            <option value="">All Vendors</option>
                            @foreach($vendors ?? [] as $vendor)
                                <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Invoice No.</label>
                        <input type="text" name="invoice_no" class="form-control form-control-sm"
                            value="{{ request('invoice_no') }}" placeholder="Invoice #">
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
                        <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-outline-secondary"><i
                                class="fas fa-times"></i></a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            @if($purchases->count() > 0)
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Invoice No</th>
                                <th>Vendor</th>
                                <th>Date</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Discount</th>
                                <th class="text-end">Net Amount</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchases as $purchase)
                                <tr>
                                    <td>{{ $loop->iteration + ($purchases->currentPage() - 1) * $purchases->perPage() }}</td>
                                    <td><span class="badge bg-secondary">{{ $purchase->invoice_no }}</span></td>
                                    <td><strong>{{ $purchase->vendor->name ?? '—' }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</td>
                                    <td class="text-end">
                                        {{ setting('currency_symbol', '$') }}{{ number_format($purchase->total_amount, 2) }}</td>
                                    <td class="text-end">
                                        {{ setting('currency_symbol', '$') }}{{ number_format($purchase->discount_amount, 2) }}</td>
                                    <td class="text-end fw-bold">
                                        {{ setting('currency_symbol', '$') }}{{ number_format($purchase->net_amount, 2) }}</td>
                                    <td class="text-center action-btns">
                                        <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-sm btn-info"
                                            title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('purchases.pdf', $purchase->id) }}" class="btn btn-sm btn-secondary"
                                            title="PDF" target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        <a href="{{ route('purchases.edit', $purchase->id) }}" class="btn btn-sm btn-warning"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger" onclick="confirmDelete({{ $purchase->id }})"
                                            title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center p-3">
                    {{ $purchases->withQueryString()->links() }}
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-shopping-cart"></i>
                    <p>No purchases found</p>
                    <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>New Purchase
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">Are you sure you want to delete this purchase?</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" class="d-inline">
                        @csrf @method('DELETE')
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
            document.getElementById('deleteForm').action = '/purchases/' + id;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
@endpush