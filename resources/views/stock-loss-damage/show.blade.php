@extends('layouts.app')

@section('title', 'View Stock Adjustment')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('stock-loss-damage.index') }}">Stock Adjustments</a></li>
    <li class="breadcrumb-item active">View</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Stock Adjustment Details</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('stock-loss-damage.edit', $adjustment->id) }}" class="btn btn-sm btn-warning">
                    <i class="fas fa-edit me-1"></i>Edit
                </a>
                <a href="{{ route('stock-loss-damage.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th class="text-muted" style="width:40%">Product</th>
                            <td><strong>{{ $adjustment->product->name ?? '—' }}</strong></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Category</th>
                            <td>
                                @if($adjustment->category === 'damage')
                                    <span class="badge bg-danger">Damage</span>
                                @elseif($adjustment->category === 'loss')
                                    <span class="badge bg-warning text-dark">Loss</span>
                                @else
                                    <span class="badge bg-info">Adjustment</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Type</th>
                            <td>
                                @if($adjustment->type === 'decrease')
                                    <span class="badge bg-danger"><i class="fas fa-arrow-down me-1"></i>Decrease</span>
                                @else
                                    <span class="badge bg-success"><i class="fas fa-arrow-up me-1"></i>Increase</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Quantity</th>
                            <td><strong class="fs-5">{{ $adjustment->quantity }}</strong></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th class="text-muted" style="width:40%">Date</th>
                            <td>{{ \Carbon\Carbon::parse($adjustment->date)->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Batch ID</th>
                            <td>{{ $adjustment->batch_id }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Location ID</th>
                            <td>{{ $adjustment->location_id }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Created</th>
                            <td>{{ $adjustment->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mt-3">
                <h6 class="text-muted mb-2">Reason</h6>
                <div class="bg-light p-3 rounded">
                    {{ $adjustment->reason }}
                </div>
            </div>
        </div>
    </div>
@endsection