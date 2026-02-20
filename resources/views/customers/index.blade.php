@extends('layouts.app')

@section('title', 'Customers')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Customers</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-users me-2"></i>All Customers</h5>
            <div class="d-flex gap-2 export-buttons">
                <a href="{{ route('customers.export.pdf', request()->query()) }}" class="btn btn-sm btn-outline-secondary"
                    title="Export PDF" target="_blank">
                    <i class="fas fa-file-pdf me-1"></i>PDF
                </a>
                <a href="{{ route('customers.export.csv', request()->query()) }}" class="btn btn-sm btn-outline-secondary"
                    title="Export CSV">
                    <i class="fas fa-file-csv me-1"></i>CSV
                </a>
                <a href="{{ route('customers.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i>Add Customer
                </a>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar mx-3 mt-3">
            <form action="{{ route('customers.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control form-control-sm" value="{{ request('name') }}"
                            placeholder="Search by name...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control form-control-sm" value="{{ request('phone') }}"
                            placeholder="Phone...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Type</label>
                        <select name="type_id" class="form-select form-select-sm">
                            <option value="">All Types</option>
                            @foreach($types ?? [] as $type)
                                <option value="{{ $type->id }}" {{ request('type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Email</label>
                        <input type="text" name="email" class="form-control form-control-sm" value="{{ request('email') }}"
                            placeholder="Email...">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary"><i
                                class="fas fa-search me-1"></i>Filter</button>
                        <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-secondary"><i
                                class="fas fa-times"></i></a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            @if($customers->count() > 0)
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>WhatsApp</th>
                                <th>Type</th>
                                <th>Balance</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $customer)
                                <tr>
                                    <td>{{ $loop->iteration + ($customers->currentPage() - 1) * $customers->perPage() }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($customer->image)
                                                <img src="{{ asset('storage/' . $customer->image) }}" alt="" class="rounded-circle me-2"
                                                    width="32" height="32" style="object-fit:cover">
                                            @else
                                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-2"
                                                    style="width:32px;height:32px;font-size:.75rem">
                                                    {{ strtoupper(substr($customer->name, 0, 2)) }}
                                                </div>
                                            @endif
                                            <strong>{{ $customer->name }}</strong>
                                        </div>
                                    </td>
                                    <td>{{ $customer->email ?? '—' }}</td>
                                    <td>{{ $customer->phone ?? '—' }}</td>
                                    <td>{{ $customer->whatsapp ?? '—' }}</td>
                                    <td><span class="badge bg-info">{{ $customer->type->name ?? '—' }}</span></td>
                                    <td class="fw-bold">
                                        @if(($customer->balance ?? 0) > 0)
                                            <span
                                                class="text-success">{{ setting('currency_symbol', '$') }}{{ number_format($customer->balance, 2) }}</span>
                                        @elseif(($customer->balance ?? 0) < 0)
                                            <span
                                                class="text-danger">{{ setting('currency_symbol', '$') }}{{ number_format($customer->balance, 2) }}</span>
                                        @else
                                            {{ setting('currency_symbol', '$') }}0.00
                                        @endif
                                    </td>
                                    <td class="text-center action-btns">
                                        <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-warning"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger" onclick="confirmDelete({{ $customer->id }})"
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
                    {{ $customers->withQueryString()->links() }}
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <p>No customers found</p>
                    <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Add First Customer
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
                <div class="modal-body">Are you sure you want to delete this customer?</div>
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
            document.getElementById('deleteForm').action = '/customers/' + id;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
@endpush