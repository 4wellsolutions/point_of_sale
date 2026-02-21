@extends('layouts.app')

@section('title', 'Purchase Return Details')
@section('page_title', 'Purchase Return Details')

@section('content')

    {{-- ===== Header Banner ===== --}}
    <div class="detail-header mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="detail-icon-wrapper" style="background:linear-gradient(135deg,#ef4444,#dc2626);">
                    <i class="fas fa-undo-alt"></i>
                </div>
                <div>
                    <h2 class="mb-1" style="font-weight:700; font-size:1.5rem;">Return {{ $purchaseReturn->invoice_no }}
                    </h2>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="detail-id-badge">{{ $purchaseReturn->purchase->vendor->name ?? 'N/A' }}</span>
                        <span class="badge bg-warning">{{ $purchaseReturn->return_date }}</span>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('purchase-returns.edit', $purchaseReturn) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-pen me-1"></i> Edit
                </a>
                <a href="{{ route('purchase-returns.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    {{-- ===== Financial Summary ===== --}}
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
                                {{ setting('currency_symbol', '$') }}{{ number_format($purchaseReturn->total_amount, 2) }}
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
                                {{ setting('currency_symbol', '$') }}{{ number_format($purchaseReturn->discount_amount, 2) }}
                            </div>
                            <div class="stat-tile-label">Discount</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="stat-tile">
                        <div class="stat-tile-icon" style="background:rgba(239,68,68,.1); color:#ef4444;">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div>
                            <div class="stat-tile-value">
                                {{ setting('currency_symbol', '$') }}{{ number_format($purchaseReturn->net_amount, 2) }}
                            </div>
                            <div class="stat-tile-label">Net Amount</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- ===== Vendor & Original Purchase ===== --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-store" style="color:var(--primary);"></i>
                    <h3 class="card-title mb-0">Vendor Information</h3>
                </div>
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-sm-6">
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-user"></i> Name</span>
                                <span class="detail-value">{{ $purchaseReturn->purchase->vendor->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-envelope"></i> Email</span>
                                <span class="detail-value">{{ $purchaseReturn->purchase->vendor->email ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-phone"></i> Phone</span>
                                <span class="detail-value">{{ $purchaseReturn->purchase->vendor->phone ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-map-marker-alt"></i> Address</span>
                                <span class="detail-value">{{ $purchaseReturn->purchase->vendor->address ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-file-invoice" style="color:var(--primary);"></i>
                    <h3 class="card-title mb-0">Original Purchase</h3>
                </div>
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-sm-6">
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-hashtag"></i> Invoice No</span>
                                <span
                                    class="detail-value font-monospace">{{ $purchaseReturn->purchase->invoice_no ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-calendar"></i> Purchase Date</span>
                                <span class="detail-value">{{ $purchaseReturn->purchase->purchase_date ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-calculator"></i> Total Amount</span>
                                <span
                                    class="detail-value">{{ setting('currency_symbol', '$') }}{{ number_format($purchaseReturn->purchase->total_amount, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-money-bill"></i> Net Amount</span>
                                <span
                                    class="detail-value">{{ setting('currency_symbol', '$') }}{{ number_format($purchaseReturn->purchase->net_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Returned Items ===== --}}
    <div class="card mt-4 mb-4">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="fas fa-list" style="color:var(--primary);"></i>
            <h3 class="card-title mb-0">Returned Items</h3>
            <span class="badge bg-info ms-auto">{{ $purchaseReturn->returnItems->count() }}</span>
        </div>
        <div class="card-body{{ $purchaseReturn->returnItems->count() ? ' p-0' : '' }}">
            @if($purchaseReturn->returnItems->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Batch No</th>
                                <th>Location</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseReturn->returnItems as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-medium">{{ $item->product->name ?? 'N/A' }}</td>
                                    <td><span class="font-monospace">{{ $item->purchaseItem->batch_no ?? 'N/A' }}</span></td>
                                    <td>{{ $item->purchaseItem->location->name ?? 'N/A' }}</td>
                                    <td class="text-end">{{ $item->quantity }}</td>
                                    <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end fw-medium">{{ number_format($item->total_amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-inbox" style="color:var(--text-muted);"></i>
                    <p>No items returned.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ===== Payment Methods ===== --}}
    @if($purchaseReturn->transactions->count() > 0)
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
                                <th>#</th>
                                <th>Payment Method</th>
                                <th class="text-end">Amount ({{ setting('currency_symbol', '$') }})</th>
                                <th>Transaction Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseReturn->transactions as $index => $transaction)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $transaction->paymentMethod->name ?? 'N/A' }}</td>
                                    <td class="text-end">{{ number_format($transaction->amount, 2) }}</td>
                                    <td>{{ $transaction->transaction_date->format('d M Y, h:i A') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if($purchaseReturn->notes)
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-sticky-note" style="color:var(--primary);"></i>
                <h3 class="card-title mb-0">Notes</h3>
            </div>
            <div class="card-body">
                <div class="notes-block">{{ $purchaseReturn->notes }}</div>
            </div>
        </div>
    @endif

    {{-- ===== Ledger Entries ===== --}}
    @if($purchaseReturn->ledgerEntries && $purchaseReturn->ledgerEntries->count() > 0)
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-book" style="color:var(--primary);"></i>
                <h3 class="card-title mb-0">Ledger Entries</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Description</th>
                                <th class="text-end">Debit</th>
                                <th class="text-end">Credit</th>
                                <th class="text-end">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseReturn->ledgerEntries as $ledger)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $ledger->date }}</td>
                                    <td>{{ $ledger->description }}</td>
                                    <td class="text-end">{{ number_format($ledger->debit, 2) }}</td>
                                    <td class="text-end">{{ number_format($ledger->credit, 2) }}</td>
                                    <td class="text-end fw-medium">{{ number_format($ledger->balance, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

@endsection