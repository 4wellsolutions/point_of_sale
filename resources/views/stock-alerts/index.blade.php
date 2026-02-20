@extends('layouts.app')

@section('title', 'Stock Alerts')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Stock Alerts</li>
@endsection

@section('content')
    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="display-6 fw-bold text-danger">{{ $lowStockProducts->count() }}</div>
                    <p class="text-muted mb-0"><i class="fas fa-arrow-down me-1"></i>Low Stock Products</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="display-6 fw-bold text-warning">{{ $expiringBatches->count() }}</div>
                    <p class="text-muted mb-0"><i class="fas fa-clock me-1"></i>Expiring Soon</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="display-6 fw-bold text-dark">{{ $expiredBatches->count() }}</div>
                    <p class="text-muted mb-0"><i class="fas fa-times-circle me-1"></i>Already Expired</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'low-stock' ? 'active' : '' }}" data-bs-toggle="tab" href="#low-stock"
                        role="tab">
                        <i class="fas fa-arrow-down me-1 text-danger"></i>Low Stock
                        <span class="badge bg-danger ms-1">{{ $lowStockProducts->count() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'expiring' ? 'active' : '' }}" data-bs-toggle="tab" href="#expiring"
                        role="tab">
                        <i class="fas fa-clock me-1 text-warning"></i>Expiring Soon
                        <span class="badge bg-warning text-dark ms-1">{{ $expiringBatches->count() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'expired' ? 'active' : '' }}" data-bs-toggle="tab" href="#expired"
                        role="tab">
                        <i class="fas fa-times-circle me-1 text-dark"></i>Expired
                        <span class="badge bg-dark ms-1">{{ $expiredBatches->count() }}</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body p-0">
            <div class="tab-content">
                {{-- Low Stock Tab --}}
                <div class="tab-pane fade {{ $tab === 'low-stock' ? 'show active' : '' }}" id="low-stock" role="tabpanel">
                    @if($lowStockProducts->count() > 0)
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th class="text-center">Current Stock</th>
                                        <th class="text-center">Reorder Level</th>
                                        <th class="text-center">Shortage</th>
                                        <th class="text-center">Severity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStockProducts as $product)
                                        @php
                                            $shortage = $product->reorder_level - $product->current_stock;
                                            $severity = $product->current_stock == 0 ? 'critical' : ($product->current_stock <= $product->reorder_level * 0.5 ? 'warning' : 'low');
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $product->name }}</strong>
                                                @if($product->sku)<br><small class="text-muted">SKU:
                                                {{ $product->sku }}</small>@endif
                                            </td>
                                            <td class="text-center fw-bold {{ $product->current_stock == 0 ? 'text-danger' : '' }}">
                                                {{ $product->current_stock }}
                                            </td>
                                            <td class="text-center">{{ $product->reorder_level }}</td>
                                            <td class="text-center text-danger fw-bold">-{{ $shortage }}</td>
                                            <td class="text-center">
                                                @if($severity === 'critical')
                                                    <span class="badge bg-danger"><i class="fas fa-exclamation-circle me-1"></i>Out of
                                                        Stock</span>
                                                @elseif($severity === 'warning')
                                                    <span class="badge bg-warning text-dark"><i
                                                            class="fas fa-exclamation-triangle me-1"></i>Very Low</span>
                                                @else
                                                    <span class="badge bg-info"><i class="fas fa-info-circle me-1"></i>Low</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-check-circle text-success"></i>
                            <p>All products are stocked above reorder level!</p>
                        </div>
                    @endif
                </div>

                {{-- Expiring Soon Tab --}}
                <div class="tab-pane fade {{ $tab === 'expiring' ? 'show active' : '' }}" id="expiring" role="tabpanel">
                    @if($expiringBatches->count() > 0)
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th>Batch No</th>
                                        <th class="text-center">Stock Qty</th>
                                        <th>Expiry Date</th>
                                        <th class="text-center">Days Left</th>
                                        <th class="text-center">Urgency</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expiringBatches as $batch)
                                        @php
                                            $urgency = $batch->days_until_expiry <= 7 ? 'critical' : ($batch->days_until_expiry <= 14 ? 'warning' : 'moderate');
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ $batch->product->name ?? '—' }}</strong></td>
                                            <td><span class="badge bg-secondary">{{ $batch->batch_no }}</span></td>
                                            <td class="text-center">{{ $batch->remaining_stock }}</td>
                                            <td>{{ \Carbon\Carbon::parse($batch->expiry_date)->format('d M Y') }}</td>
                                            <td
                                                class="text-center fw-bold {{ $batch->days_until_expiry <= 7 ? 'text-danger' : 'text-warning' }}">
                                                {{ $batch->days_until_expiry }} days
                                            </td>
                                            <td class="text-center">
                                                @if($urgency === 'critical')
                                                    <span class="badge bg-danger"><i class="fas fa-fire me-1"></i>Critical</span>
                                                @elseif($urgency === 'warning')
                                                    <span class="badge bg-warning text-dark"><i
                                                            class="fas fa-exclamation me-1"></i>Warning</span>
                                                @else
                                                    <span class="badge bg-info"><i class="fas fa-clock me-1"></i>Moderate</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-check-circle text-success"></i>
                            <p>No batches expiring in the next {{ $expiringDays }} days!</p>
                        </div>
                    @endif
                </div>

                {{-- Expired Tab --}}
                <div class="tab-pane fade {{ $tab === 'expired' ? 'show active' : '' }}" id="expired" role="tabpanel">
                    @if($expiredBatches->count() > 0)
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th>Batch No</th>
                                        <th class="text-center">Remaining Stock</th>
                                        <th>Expired On</th>
                                        <th class="text-center">Days Ago</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expiredBatches as $batch)
                                        <tr class="table-danger bg-opacity-10">
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ $batch->product->name ?? '—' }}</strong></td>
                                            <td><span class="badge bg-secondary">{{ $batch->batch_no }}</span></td>
                                            <td class="text-center fw-bold text-danger">{{ $batch->remaining_stock }}</td>
                                            <td>{{ \Carbon\Carbon::parse($batch->expiry_date)->format('d M Y') }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-dark">{{ $batch->days_expired }} days ago</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-check-circle text-success"></i>
                            <p>No expired batches with remaining stock!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection