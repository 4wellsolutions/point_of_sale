@extends('layouts.app')

@section('title', 'Product Details')

@section('page_title', 'Product Details')

@section('content')

    {{-- ===== Header Banner ===== --}}
    <div class="detail-header mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="detail-icon-wrapper">
                    <i class="fas fa-box-open"></i>
                </div>
                <div>
                    <h2 class="mb-1" style="font-weight:700; font-size:1.5rem;">{{ $product->name }}</h2>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="detail-id-badge">{{ $product->sku }}</span>
                        @if($product->status == 'active')
                            <span class="badge bg-success"><i class="fas fa-circle" style="font-size:.45rem;vertical-align:middle;margin-right:4px;"></i>Active</span>
                        @elseif($product->status == 'inactive')
                            <span class="badge bg-secondary"><i class="fas fa-circle" style="font-size:.45rem;vertical-align:middle;margin-right:4px;"></i>Inactive</span>
                        @else
                            <span class="badge bg-danger"><i class="fas fa-circle" style="font-size:.45rem;vertical-align:middle;margin-right:4px;"></i>Discontinued</span>
                        @endif
                        @if($product->category)
                            <span class="badge bg-info">{{ $product->category->name }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-pen me-1"></i> Edit
                </a>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- ===== Left Column — Product Info ===== --}}
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-info-circle" style="color:var(--primary);"></i>
                    <h3 class="card-title mb-0">Product Information</h3>
                </div>
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-sm-6">
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-tag"></i> Description</span>
                                <span class="detail-value">{{ $product->description ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-palette"></i> Flavour</span>
                                <span class="detail-value">{{ $product->flavour->name }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-box"></i> Packing</span>
                                <span class="detail-value">{{ $product->packing->type }} <span class="text-muted">({{ $product->packing->unit_size }})</span></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-barcode"></i> Barcode</span>
                                <span class="detail-value font-monospace">{{ $product->barcode ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-weight-hanging"></i> Weight</span>
                                <span class="detail-value">{{ $product->weight ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-flask"></i> Volume</span>
                                <span class="detail-value">{{ $product->volume ?? '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stock & Tax --}}
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-cubes" style="color:var(--primary);"></i>
                    <h3 class="card-title mb-0">Stock & Tax</h3>
                </div>
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-sm-4">
                            <div class="stat-tile">
                                <div class="stat-tile-icon" style="background:rgba(16,185,129,.1); color:#10b981;">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                                <div>
                                    <div class="stat-tile-value">{{ $product->reorder_level }}</div>
                                    <div class="stat-tile-label">Reorder Level</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="stat-tile">
                                <div class="stat-tile-icon" style="background:rgba(59,130,246,.1); color:#3b82f6;">
                                    <i class="fas fa-warehouse"></i>
                                </div>
                                <div>
                                    <div class="stat-tile-value">{{ $product->max_stock_level }}</div>
                                    <div class="stat-tile-label">Max Stock Level</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="stat-tile">
                                <div class="stat-tile-icon" style="background:rgba(245,158,11,.1); color:#f59e0b;">
                                    <i class="fas fa-percent"></i>
                                </div>
                                <div>
                                    <div class="stat-tile-value">{{ $product->gst ?? '0' }}%</div>
                                    <div class="stat-tile-label">GST Rate</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Purchase Batches --}}
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-boxes-stacked" style="color:var(--primary);"></i>
                    <h3 class="card-title mb-0">Purchase Batches</h3>
                    <span class="badge bg-info ms-auto">{{ $product->purchases->count() }}</span>
                </div>
                <div class="card-body{{ $product->purchases->count() ? ' p-0' : '' }}">
                    @if($product->purchases->count())
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Batch No</th>
                                        <th>Purchase Date</th>
                                        <th class="text-end">Qty</th>
                                        <th class="text-end">Unit Price</th>
                                        <th class="text-end">Total</th>
                                        <th>Expiry</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->purchases as $item)
                                        <tr>
                                            <td><span class="font-monospace fw-medium">{{ $item->batch_no ?? '—' }}</span></td>
                                            <td>{{ $item->purchase ? \Carbon\Carbon::parse($item->purchase->purchase_date)->format('d M Y') : '—' }}</td>
                                            <td class="text-end fw-medium">{{ number_format($item->quantity) }}</td>
                                            <td class="text-end">{{ number_format($item->purchase_price, 2) }}</td>
                                            <td class="text-end fw-medium">{{ number_format($item->total_amount, 2) }}</td>
                                            <td>{{ $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('d M Y') : '—' }}</td>
                                            <td class="text-center">
                                                @if($item->expiry_date && \Carbon\Carbon::parse($item->expiry_date)->isPast())
                                                    <span class="badge bg-danger">Expired</span>
                                                @else
                                                    <span class="badge bg-success">Active</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-box-open" style="color:var(--text-muted);"></i>
                            <p>No purchase batches recorded for this product.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ===== Right Column ===== --}}
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-image" style="color:var(--primary);"></i>
                    <h3 class="card-title mb-0">Product Image</h3>
                </div>
                <div class="card-body text-center">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                             class="img-fluid product-detail-img">
                    @else
                        <div class="no-image-placeholder">
                            <i class="fas fa-camera"></i>
                            <p>No image uploaded</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-clock" style="color:var(--primary);"></i>
                    <h3 class="card-title mb-0">Timeline</h3>
                </div>
                <div class="card-body">
                    <div class="timeline-item mb-3">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="timeline-dot" style="background:#10b981;"></span>
                            <span class="fw-medium" style="font-size:.8125rem;">Created</span>
                        </div>
                        <p class="mb-0 ps-4" style="font-size:.8125rem; color:var(--text-secondary);">
                            {{ $product->created_at->format('d M Y, h:i A') }}
                        </p>
                    </div>
                    <div class="timeline-item">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="timeline-dot" style="background:#3b82f6;"></span>
                            <span class="fw-medium" style="font-size:.8125rem;">Last Updated</span>
                        </div>
                        <p class="mb-0 ps-4" style="font-size:.8125rem; color:var(--text-secondary);">
                            {{ $product->updated_at->format('d M Y, h:i A') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
