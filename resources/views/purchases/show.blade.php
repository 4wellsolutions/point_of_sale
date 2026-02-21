@extends('layouts.app')

@section('title', 'Purchase Details')
@section('page_title', 'Purchase Details')

@section('content')

    {{-- ===== Header Banner ===== --}}
    <div class="detail-header mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="detail-icon-wrapper">
                    <i class="fas fa-truck-loading"></i>
                </div>
                <div>
                    <h2 class="mb-1" style="font-weight:700; font-size:1.5rem;">Purchase {{ $purchase->invoice_no }}</h2>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="detail-id-badge">{{ $purchase->vendor->name }}</span>
                        <span
                            class="badge bg-info">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('purchases.pdf', $purchase) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-file-pdf me-1"></i> PDF
                </a>
                <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    {{-- ===== Financial Summary Tiles ===== --}}
    <div class="card mb-4">
        <div class="card-body p-0">
            <div class="row g-0">
                <div class="col-sm-4">
                    <div class="stat-tile">
                        <div class="stat-tile-icon" style="background:rgba(59,130,246,.1); color:#3b82f6;">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <div>
                            <div class="stat-tile-value">
                                {{ setting('currency_symbol', '$') }}{{ number_format($purchase->total_amount, 2) }}</div>
                            <div class="stat-tile-label">Total Amount</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="stat-tile">
                        <div class="stat-tile-icon" style="background:rgba(245,158,11,.1); color:#f59e0b;">
                            <i class="fas fa-percent"></i>
                        </div>
                        <div>
                            <div class="stat-tile-value">
                                {{ setting('currency_symbol', '$') }}{{ number_format($purchase->discount_amount, 2) }}
                            </div>
                            <div class="stat-tile-label">Discount</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="stat-tile">
                        <div class="stat-tile-icon" style="background:rgba(16,185,129,.1); color:#10b981;">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div>
                            <div class="stat-tile-value">
                                {{ setting('currency_symbol', '$') }}{{ number_format($purchase->net_amount, 2) }}</div>
                            <div class="stat-tile-label">Net Amount</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Purchase Details ===== --}}
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="fas fa-info-circle" style="color:var(--primary);"></i>
            <h3 class="card-title mb-0">Purchase Information</h3>
        </div>
        <div class="card-body p-0">
            <div class="row g-0">
                <div class="col-sm-6">
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-store"></i> Vendor</span>
                        <span class="detail-value">{{ $purchase->vendor->name }}</span>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-file-invoice"></i> Invoice No</span>
                        <span class="detail-value font-monospace">{{ $purchase->invoice_no }}</span>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-calendar"></i> Purchase Date</span>
                        <span
                            class="detail-value">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</span>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-user"></i> Created By</span>
                        <span class="detail-value">{{ $purchase->user->name ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($purchase->notes)
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-sticky-note" style="color:var(--primary);"></i>
                <h3 class="card-title mb-0">Notes</h3>
            </div>
            <div class="card-body">
                <div class="notes-block">{{ $purchase->notes }}</div>
            </div>
        </div>
    @endif

    {{-- ===== Purchase Items ===== --}}
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="fas fa-list" style="color:var(--primary);"></i>
            <h3 class="card-title mb-0">Purchase Items</h3>
            <span class="badge bg-info ms-auto">{{ $purchase->purchaseItems->count() }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width:50px">Image</th>
                            <th>Product</th>
                            <th>Batch No</th>
                            <th>Location</th>
                            <th>Expiry</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Purchase Price</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->purchaseItems as $item)
                            <tr>
                                <td>
                                    @if($item->product->image_url)
                                        <img src="{{ asset($item->product->image_url) }}" alt="{{ $item->product->name }}"
                                            class="product-thumb">
                                    @else
                                        <img src="{{ asset('images/no-image.png') }}" alt="No Image" class="product-thumb">
                                    @endif
                                </td>
                                <td class="fw-medium">{{ $item->product->name }}</td>
                                <td><span class="font-monospace">{{ $item->batch_no ?? '—' }}</span></td>
                                <td>{{ $item->location->name }}</td>
                                <td>
                                    @if($item->expiry_date)
                                        {{ \Carbon\Carbon::parse($item->expiry_date)->format('d M Y') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end">{{ number_format($item->purchase_price, 2) }}</td>
                                <td class="text-end fw-medium">{{ number_format($item->total_amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="7" class="text-end">Total:</th>
                            <th class="text-end">{{ number_format($purchase->total_amount, 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="7" class="text-end">Discount:</th>
                            <th class="text-end">{{ number_format($purchase->discount_amount, 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="7" class="text-end">Net Amount:</th>
                            <th class="text-end">{{ number_format($purchase->net_amount, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- ===== Payment Methods ===== --}}
    @if($purchase->transactions->count() > 0)
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-credit-card" style="color:var(--primary);"></i>
                <h3 class="card-title mb-0">Payment Methods</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Payment Method</th>
                                <th class="text-end">Amount ({{ setting('currency_symbol', '$') }})</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchase->transactions as $transaction)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}</td>
                                    <td>{{ $transaction->paymentMethod->method_name }}</td>
                                    <td class="text-end">{{ number_format($transaction->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2">Total Payment:</th>
                                <th class="text-end">{{ number_format($purchase->transactions->sum('amount'), 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="2">Remaining Balance:</th>
                                <th class="text-end">
                                    {{ number_format($purchase->net_amount - $purchase->transactions->sum('amount'), 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif

@endsection