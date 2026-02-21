@extends('layouts.app')

@section('title', 'Stock Loss/Damage Details')
@section('page_title', 'Stock Loss/Damage Details')

@section('content')

    {{-- ===== Header Banner ===== --}}
    <div class="detail-header mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="detail-icon-wrapper" style="background:linear-gradient(135deg,#ef4444,#dc2626);">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <h2 class="mb-1" style="font-weight:700; font-size:1.5rem;">Stock Adjustment #{{ $stockAdjustment->id }}
                    </h2>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        @if($stockAdjustment->type == 'loss')
                            <span class="badge bg-danger">Loss</span>
                        @elseif($stockAdjustment->type == 'damage')
                            <span class="badge bg-warning text-dark">Damage</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($stockAdjustment->type) }}</span>
                        @endif
                        <span
                            class="detail-id-badge">{{ \Carbon\Carbon::parse($stockAdjustment->date)->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('stock-loss-damage.edit', $stockAdjustment) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-pen me-1"></i> Edit
                </a>
                <a href="{{ route('stock-loss-damage.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            {{-- Product & Adjustment Details --}}
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-info-circle" style="color:var(--primary);"></i>
                    <h3 class="card-title mb-0">Adjustment Details</h3>
                </div>
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-sm-6">
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-box"></i> Product</span>
                                <span class="detail-value">{{ $stockAdjustment->product->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-folder"></i> Category</span>
                                <span class="detail-value">{{ $stockAdjustment->product->category->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-exclamation-circle"></i> Type</span>
                                <span class="detail-value">
                                    @if($stockAdjustment->type == 'loss')
                                        <span class="badge bg-danger">Loss</span>
                                    @elseif($stockAdjustment->type == 'damage')
                                        <span class="badge bg-warning text-dark">Damage</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($stockAdjustment->type) }}</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-calendar"></i> Date</span>
                                <span
                                    class="detail-value">{{ \Carbon\Carbon::parse($stockAdjustment->date)->format('d M Y') }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-barcode"></i> Batch ID</span>
                                <span class="detail-value font-monospace">{{ $stockAdjustment->batch_id ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-map-marker-alt"></i> Location</span>
                                <span class="detail-value">{{ $stockAdjustment->location->name ?? '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quantity --}}
            <div class="card mb-4">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-12">
                            <div class="stat-tile" style="border-right:none;">
                                <div class="stat-tile-icon" style="background:rgba(239,68,68,.1); color:#ef4444;">
                                    <i class="fas fa-minus-circle"></i>
                                </div>
                                <div>
                                    <div class="stat-tile-value">{{ number_format($stockAdjustment->quantity) }}</div>
                                    <div class="stat-tile-label">Quantity Affected</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Reason --}}
            @if($stockAdjustment->reason)
                <div class="card">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i class="fas fa-comment" style="color:var(--primary);"></i>
                        <h3 class="card-title mb-0">Reason</h3>
                    </div>
                    <div class="card-body">
                        <div class="notes-block">{{ $stockAdjustment->reason }}</div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
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
                            {{ $stockAdjustment->created_at->format('d M Y, h:i A') }}
                        </p>
                    </div>
                    <div class="timeline-item">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="timeline-dot" style="background:#3b82f6;"></span>
                            <span class="fw-medium" style="font-size:.8125rem;">Last Updated</span>
                        </div>
                        <p class="mb-0 ps-4" style="font-size:.8125rem; color:var(--text-secondary);">
                            {{ $stockAdjustment->updated_at->format('d M Y, h:i A') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection