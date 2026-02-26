@extends('layouts.app')

@section('title', 'Order Booking Details')
@section('page_title', 'Order Booking Details')

@section('content')

    {{-- ===== Header Banner ===== --}}
    <div class="detail-header mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="detail-icon-wrapper">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div>
                    <h2 class="mb-1" style="font-weight:700; font-size:1.5rem;">Booking {{ $booking->invoice_no }}</h2>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="detail-id-badge">{{ $booking->customer->name }}</span>
                        <span class="badge bg-info">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</span>
                        @php
                            $color = match ($booking->status) {
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                default => 'warning'
                            };
                        @endphp
                        <span class="badge bg-{{ $color }}">{{ ucfirst($booking->status) }}</span>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Print
                </button>
                <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    {{-- ===== Financial Summary Tiles ===== --}}
    <div class="card mb-4 print-hide">
        <div class="card-body p-0">
            <div class="row g-0">
                <div class="col-sm-4">
                    <div class="stat-tile">
                        <div class="stat-tile-icon" style="background:rgba(59,130,246,.1); color:#3b82f6;">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <div>
                            <div class="stat-tile-value">
                                {{ setting('currency_symbol', '$') }}{{ format_number($booking->total_amount) }}
                            </div>
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
                                {{ setting('currency_symbol', '$') }}{{ format_number($booking->discount_amount) }}
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
                                {{ setting('currency_symbol', '$') }}{{ format_number($booking->net_amount) }}
                            </div>
                            <div class="stat-tile-label">Net Amount</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Booking Details ===== --}}
    <div class="card mb-4 print-no-border">
        <div class="card-header d-flex align-items-center gap-2 print-hide">
            <i class="fas fa-info-circle" style="color:var(--primary);"></i>
            <h3 class="card-title mb-0">Order Information</h3>
        </div>
        
        <!-- Print Header (Visible only when printing) -->
        <div class="print-only mb-4 w-100 text-center" style="display: none;">
            <h2>{{ setting('company_name', 'Your Company') }}</h2>
            <p>{{ setting('company_phone', '') }} | {{ setting('company_email', '') }}</p>
            <h4 class="mt-3 text-uppercase">Order Booking / Quotation</h4>
        </div>

        <div class="card-body p-0 print-p-0">
            <div class="row g-0">
                <div class="col-sm-6 col-6">
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-user print-hide"></i> Customer</span>
                        <span class="detail-value">{{ $booking->customer->name }}</span>
                    </div>
                </div>
                <div class="col-sm-6 col-6">
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-file-invoice print-hide"></i> Booking No</span>
                        <span class="detail-value font-monospace">{{ $booking->invoice_no }}</span>
                    </div>
                </div>
                <div class="col-sm-6 col-6">
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-calendar print-hide"></i> Date</span>
                        <span class="detail-value">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</span>
                    </div>
                </div>
                <div class="col-sm-6 col-6">
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-user-tie print-hide"></i> Salesman</span>
                        <span class="detail-value">{{ $booking->user->name ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($booking->notes)
        <div class="card mb-4 print-no-border">
            <div class="card-header d-flex align-items-center gap-2 print-hide">
                <i class="fas fa-sticky-note" style="color:var(--primary);"></i>
                <h3 class="card-title mb-0">Notes</h3>
            </div>
            <div class="card-body print-p-0">
                <div class="notes-block">{{ $booking->notes }}</div>
            </div>
        </div>
    @endif

    {{-- ===== Booking Items ===== --}}
    <div class="card mb-4 print-no-border">
        <div class="card-header d-flex align-items-center gap-2 print-hide">
            <i class="fas fa-list" style="color:var(--primary);"></i>
            <h3 class="card-title mb-0">Booking Items</h3>
            <span class="badge bg-info ms-auto">{{ $booking->items->count() }}</span>
        </div>
        <div class="card-body p-0 print-p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width:50px" class="print-hide">Image</th>
                            <th>Product</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($booking->items as $item)
                            <tr>
                                <td class="print-hide">
                                    @if($item->product->image_url)
                                        <img src="{{ asset($item->product->image_url) }}" alt="{{ $item->product->name }}"
                                            style="width:40px; height:40px; object-fit:cover; border-radius:4px;">
                                    @else
                                        <div style="width:40px; height:40px; background:#f1f5f9; border-radius:4px; display:flex; align-items:center; justify-content:center; color:#94a3b8;">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-medium">{{ $item->product->name ?? 'Unknown Product' }}</td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end">{{ format_number($item->unit_price) }}</td>
                                <td class="text-end fw-medium">{{ format_number($item->subtotal) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">Total Amount:</th>
                            <th class="text-end">{{ format_number($booking->total_amount) }}</th>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-end">Discount:</th>
                            <th class="text-end">{{ format_number($booking->discount_amount) }}</th>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-end">Net Amount:</th>
                            <th class="text-end">{{ format_number($booking->net_amount) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('styles')
<style>
    @media print {
        body { font-size: 12px; background: white !important; }
        .print-hide, .navbar, .main-sidebar, .main-footer, .breadcrumb, .content-header { display: none !important; }
        .print-only { display: block !important; }
        .content-wrapper { margin-left: 0 !important; padding: 0 !important; background: white !important; }
        .card { border: none !important; box-shadow: none !important; margin-bottom: 20px !important; }
        .print-no-border { border: none !important; }
        .print-p-0 { padding: 0 !important; }
        table { width: 100% !important; border-collapse: collapse !important; }
        th, td { border: 1px solid #ddd !important; padding: 8px !important; }
        tfoot th { border-top: 2px solid #333 !important; }
    }
</style>
@endpush
