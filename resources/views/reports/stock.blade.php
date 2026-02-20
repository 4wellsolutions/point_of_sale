@extends('layouts.app')
@section('title', 'Stock Report')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Stock Report</li>
@endsection

@section('content')
    <!-- Summary Cards -->
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="kpi-card kpi-products">
                <div class="kpi-icon"><i class="fas fa-boxes"></i></div>
                <div class="kpi-value">{{ number_format($totalProducts) }}</div>
                <div class="kpi-label">Total Products</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card kpi-expenses">
                <div class="kpi-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="kpi-value">{{ number_format($lowStockCount) }}</div>
                <div class="kpi-label">Low Stock Items</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card" style="background: linear-gradient(135deg, #ef4444, #dc2626)">
                <div class="kpi-icon"><i class="fas fa-times-circle"></i></div>
                <div class="kpi-value">{{ number_format($outOfStockCount) }}</div>
                <div class="kpi-label">Out of Stock</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.stock') }}">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-control">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Stock Status</label>
                        <select name="stock_status" class="form-control">
                            <option value="">All</option>
                            <option value="in" {{ request('stock_status') == 'in' ? 'selected' : '' }}>In Stock</option>
                            <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Low Stock</option>
                            <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Out of Stock</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filter</button>
                        <a href="{{ route('reports.stock') }}" class="btn btn-outline-secondary"><i
                                class="fas fa-times me-1"></i>Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-table me-2"></i>Stock Levels</h5>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th class="text-center">Current Stock</th>
                        <th class="text-center">Alert Qty</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        @php
                            $stock = $product->total_stock ?? 0;
                            $alertQty = $product->alert_quantity ?? 0;
                            if ($stock <= 0) {
                                $statusClass = 'bg-danger';
                                $statusLabel = 'Out of Stock';
                            } elseif ($stock <= $alertQty) {
                                $statusClass = 'bg-warning';
                                $statusLabel = 'Low Stock';
                            } else {
                                $statusClass = 'bg-success';
                                $statusLabel = 'In Stock';
                            }
                        @endphp
                        <tr class="{{ $stock <= 0 ? 'low-stock-row' : '' }}">
                            <td>{{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}</td>
                            <td><strong>{{ $product->name }}</strong></td>
                            <td><code>{{ $product->sku }}</code></td>
                            <td>{{ $product->category->name ?? '—' }}</td>
                            <td class="text-center"><strong>{{ number_format($stock) }}</strong></td>
                            <td class="text-center">{{ number_format($alertQty) }}</td>
                            <td class="text-center"><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No products found for the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
            <div class="card-footer">{{ $products->links() }}</div>
        @endif
    </div>
@endsection