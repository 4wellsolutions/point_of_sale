@extends('layouts.app')

@section('title', 'Expense Details')
@section('page_title', 'Expense Details')

@section('content')

    <div class="detail-header mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="detail-icon-wrapper">
                    <i class="fas fa-receipt"></i>
                </div>
                <div>
                    <h2 class="mb-1" style="font-weight:700; font-size:1.5rem;">Expense #{{ $expense->id }}</h2>
                    <div class="d-flex align-items-center gap-2">
                        <span class="detail-id-badge">{{ $expense->expenseType->name }}</span>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-pen me-1"></i> Edit
                </a>
                <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-info-circle" style="color:var(--primary);"></i>
                    <h3 class="card-title mb-0">Expense Information</h3>
                </div>
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-sm-6">
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-tags"></i> Expense Type</span>
                                <span class="detail-value">{{ $expense->expenseType->name }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-calendar"></i> Date</span>
                                <span
                                    class="detail-value">{{ \Carbon\Carbon::parse($expense->date)->format('d M Y') }}</span>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="detail-row" style="border-right:none;">
                                <span class="detail-label"><i class="fas fa-align-left"></i> Description</span>
                                <span class="detail-value">{{ $expense->description ?? '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Amount Stat --}}
            <div class="card">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-12">
                            <div class="stat-tile" style="border-right:none;">
                                <div class="stat-tile-icon" style="background:rgba(239,68,68,.1); color:#ef4444;">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <div>
                                    <div class="stat-tile-value">
                                        {{ setting('currency_symbol', '$') }}{{ number_format($expense->amount, 2) }}</div>
                                    <div class="stat-tile-label">Amount</div>
                                </div>
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
                            {{ $expense->created_at->format('d M Y, h:i A') }}
                        </p>
                    </div>
                    <div class="timeline-item">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="timeline-dot" style="background:#3b82f6;"></span>
                            <span class="fw-medium" style="font-size:.8125rem;">Last Updated</span>
                        </div>
                        <p class="mb-0 ps-4" style="font-size:.8125rem; color:var(--text-secondary);">
                            {{ $expense->updated_at->format('d M Y, h:i A') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection