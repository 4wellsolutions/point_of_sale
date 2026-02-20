@extends('layouts.app')

@section('title', 'Edit Stock Adjustment')

@section('content_header')
    <h1>Edit Stock Adjustment</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('stock_adjustments.update', $stockAdjustment->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="purchase_id" class="form-label">Purchase</label>
                    <select name="purchase_id" id="purchase_id" class="form-select @error('purchase_id') is-invalid @enderror">
                        <option value="">Select Purchase</option>
                        @foreach($purchases as $purchase)
                            <option value="{{ $purchase->id }}" {{ old('purchase_id', $stockAdjustment->purchase_id) == $purchase->id ? 'selected' : '' }}>
                                {{ $purchase->id }} - {{ $purchase->name ?? 'Purchase Name' }}
                            </option>
                        @endforeach
                    </select>
                    @error('purchase_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="adjustment_type" class="form-label">Adjustment Type</label>
                    <select name="adjustment_type" id="adjustment_type" class="form-select @error('adjustment_type') is-invalid @enderror">
                        <option value="">Select Type</option>
                        <option value="increase" {{ old('adjustment_type', $stockAdjustment->adjustment_type) == 'increase' ? 'selected' : '' }}>Increase</option>
                        <option value="decrease" {{ old('adjustment_type', $stockAdjustment->adjustment_type) == 'decrease' ? 'selected' : '' }}>Decrease</option>
                    </select>
                    @error('adjustment_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $stockAdjustment->quantity) }}" class="form-control @error('quantity') is-invalid @enderror">
                    @error('quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="reason" class="form-label">Reason</label>
                    <input type="text" name="reason" id="reason" value="{{ old('reason', $stockAdjustment->reason) }}" class="form-control @error('reason') is-invalid @enderror">
                    @error('reason')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="date" class="form-label">Adjustment Date</label>
                    <input type="date" name="date" id="date" value="{{ old('date', \Carbon\Carbon::parse($stockAdjustment->date)->format('Y-m-d')) }}" class="form-control @error('date') is-invalid @enderror">
                    @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-save"></i> Update Adjustment
                </button>
                <a href="{{ route('stock_adjustments.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
