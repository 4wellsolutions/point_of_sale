@extends('layouts.app')

@section('title', 'Flavour Details')
@section('page_title', 'Flavour Details')

@section('content')

    <div class="detail-header mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="detail-icon-wrapper">
                    <i class="fas fa-palette"></i>
                </div>
                <div>
                    <h2 class="mb-1" style="font-weight:700; font-size:1.5rem;">{{ $flavour->name }}</h2>
                    <span class="detail-id-badge">ID #{{ $flavour->id }}</span>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('flavours.edit', $flavour) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-pen me-1"></i> Edit
                </a>
                <a href="{{ route('flavours.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-info-circle" style="color:var(--primary);"></i>
                    <h3 class="card-title mb-0">Flavour Information</h3>
                </div>
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-sm-6">
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-tag"></i> Name</span>
                                <span class="detail-value">{{ $flavour->name }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-align-left"></i> Description</span>
                                <span class="detail-value">{{ $flavour->description ?? '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                            {{ $flavour->created_at->format('d M Y, h:i A') }}
                        </p>
                    </div>
                    <div class="timeline-item">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="timeline-dot" style="background:#3b82f6;"></span>
                            <span class="fw-medium" style="font-size:.8125rem;">Last Updated</span>
                        </div>
                        <p class="mb-0 ps-4" style="font-size:.8125rem; color:var(--text-secondary);">
                            {{ $flavour->updated_at->format('d M Y, h:i A') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection