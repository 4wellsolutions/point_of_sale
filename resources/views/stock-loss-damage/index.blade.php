@extends('layouts.app')

@section('title', 'Stock Adjustments')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Stock Adjustments</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Stock Adjustments (Loss / Damage / Correction)</h5>
            <a href="{{ route('stock-loss-damage.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i>New Adjustment
            </a>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar mx-3 mt-3">
            <form action="{{ route('stock-loss-damage.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="adjustment" {{ request('category') == 'adjustment' ? 'selected' : '' }}>Adjustment
                            </option>
                            <option value="damage" {{ request('category') == 'damage' ? 'selected' : '' }}>Damage</option>
                            <option value="loss" {{ request('category') == 'loss' ? 'selected' : '' }}>Loss</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="increase" {{ request('type') == 'increase' ? 'selected' : '' }}>Increase</option>
                            <option value="decrease" {{ request('type') == 'decrease' ? 'selected' : '' }}>Decrease</option>
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
                        <a href="{{ route('stock-loss-damage.index') }}" class="btn btn-sm btn-outline-secondary"><i
                                class="fas fa-times"></i></a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            @if($adjustments->count() > 0)
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Type</th>
                                <th class="text-end">Quantity</th>
                                <th>Reason</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($adjustments as $adj)
                                <tr>
                                    <td>{{ $loop->iteration + ($adjustments->currentPage() - 1) * $adjustments->perPage() }}</td>
                                    <td>{{ \Carbon\Carbon::parse($adj->date)->format('d M Y') }}</td>
                                    <td><strong>{{ $adj->product->name ?? '—' }}</strong></td>
                                    <td>
                                        @if($adj->category === 'damage')
                                            <span class="badge bg-danger">Damage</span>
                                        @elseif($adj->category === 'loss')
                                            <span class="badge bg-warning text-dark">Loss</span>
                                        @else
                                            <span class="badge bg-info">Adjustment</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($adj->type === 'decrease')
                                            <span class="badge bg-danger"><i class="fas fa-arrow-down me-1"></i>Decrease</span>
                                        @else
                                            <span class="badge bg-success"><i class="fas fa-arrow-up me-1"></i>Increase</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold">{{ $adj->quantity }}</td>
                                    <td>{{ Str::limit($adj->reason, 40) }}</td>
                                    <td class="text-center action-btns">
                                        <a href="{{ route('stock-loss-damage.show', $adj->id) }}" class="btn btn-sm btn-info"
                                            title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('stock-loss-damage.edit', $adj->id) }}" class="btn btn-sm btn-warning"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger" onclick="confirmDelete({{ $adj->id }})"
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
                    {{ $adjustments->withQueryString()->links() }}
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-boxes"></i>
                    <p>No stock adjustments found</p>
                    <a href="{{ route('stock-loss-damage.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>New Adjustment
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
                <div class="modal-body">Are you sure you want to delete this adjustment? Stock effects will be reversed.
                </div>
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
            document.getElementById('deleteForm').action = '/stock-loss-damage/' + id;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
@endpush