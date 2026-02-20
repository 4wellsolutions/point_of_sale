@extends('layouts.app')

@section('title', 'Edit Stock Adjustment')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('stock-loss-damage.index') }}">Stock Adjustments</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Stock Adjustment</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-1"></i>
                Only the <strong>reason</strong> and <strong>date</strong> can be edited. To change product, batch, or quantity, delete this record and create a new one.
            </div>

            <!-- Read-only summary -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <label class="form-label text-muted">Product</label>
                    <p class="fw-bold">{{ $adjustment->product->name ?? '—' }}</p>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted">Category</label>
                    <p>
                        @if($adjustment->category === 'damage')
                            <span class="badge bg-danger">Damage</span>
                        @elseif($adjustment->category === 'loss')
                            <span class="badge bg-warning text-dark">Loss</span>
                        @else
                            <span class="badge bg-info">Adjustment</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted">Type</label>
                    <p>
                        @if($adjustment->type === 'decrease')
                            <span class="badge bg-danger">Decrease</span>
                        @else
                            <span class="badge bg-success">Increase</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted">Quantity</label>
                    <p class="fw-bold">{{ $adjustment->quantity }}</p>
                </div>
            </div>

            <hr>

            <!-- Editable fields -->
            <form action="{{ route('stock-loss-damage.update', $adjustment->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                            required value="{{ old('date', \Carbon\Carbon::parse($adjustment->date)->format('Y-m-d')) }}">
                        @error('date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                    <textarea name="reason" class="form-control @error('reason') is-invalid @enderror"
                        rows="3" required>{{ old('reason', $adjustment->reason) }}</textarea>
                    @error('reason')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <button class="btn btn-success" type="submit">
                    <i class="fas fa-save me-1"></i> Update
                </button>
                <a href="{{ route('stock-loss-damage.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </form>
        </div>
    </div>
@endsection
