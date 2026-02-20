@extends('layouts.app')

@section('title', 'Products')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Products</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-box-open me-2"></i>All Products</h5>
            <div class="d-flex gap-2 export-buttons">
                <a href="{{ route('products.export.pdf') }}" class="btn btn-sm btn-outline-secondary" title="Export PDF" target="_blank">
                    <i class="fas fa-file-pdf me-1"></i>PDF
                </a>
                <a href="{{ route('products.export.csv') }}" class="btn btn-sm btn-outline-secondary" title="Export CSV">
                    <i class="fas fa-file-csv me-1"></i>CSV
                </a>
                <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i>Add Product
                </a>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar mx-3 mt-3">
            <form action="{{ route('products.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ request('name') }}"
                            placeholder="Search by name...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Flavour</label>
                        <select name="flavour_id" class="form-select">
                            <option value="">All Flavours</option>
                            @foreach($flavours ?? [] as $flavour)
                                <option value="{{ $flavour->id }}" {{ request('flavour_id') == $flavour->id ? 'selected' : '' }}>
                                    {{ $flavour->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary"><i
                                class="fas fa-search me-1"></i>Filter</button>
                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary"><i
                                class="fas fa-times"></i></a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            @if($products->count() > 0)
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Flavour</th>
                                <th>Packing</th>
                                <th>GST %</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td>{{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}</td>
                                    <td><strong>{{ $product->name }}</strong></td>
                                    <td>{{ $product->category->name ?? '—' }}</td>
                                    <td>{{ $product->flavour->name ?? '—' }}</td>
                                    <td>{{ $product->packing->name ?? '—' }}</td>
                                    <td>{{ $product->gst ?? 0 }}%</td>
                                    <td>
                                        @if($product->status == 'active')
                                            <span class="badge" style="background:#22c55e;color:#fff;padding:5px 12px;font-size:0.8rem;border-radius:4px;">Active</span>
                                        @elseif($product->status == 'discontinued')
                                            <span class="badge" style="background:#f59e0b;color:#fff;padding:5px 12px;font-size:0.8rem;border-radius:4px;">Discontinued</span>
                                        @else
                                            <span class="badge" style="background:#ef4444;color:#fff;padding:5px 12px;font-size:0.8rem;border-radius:4px;">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-center action-btns">
                                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-info"
                                            title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-warning"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger" onclick="confirmDelete({{ $product->id }})"
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
                    {{ $products->withQueryString()->links() }}
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <p>No products found</p>
                    <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Add First Product
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this product? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
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
            document.getElementById('deleteForm').action = '/products/' + id;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
@endpush