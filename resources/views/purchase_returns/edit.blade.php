@extends('layouts.app')

@section('title', 'Edit Purchase Return')

@section('page_title', 'Edit Purchase Return')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('purchase-returns.update', $purchaseReturn) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="purchase_id" class="form-label">Purchase <span class="text-danger">*</span></label>
                    <select name="purchase_id" class="form-control" required>
                        <option value="">Select Purchase</option>
                        @foreach($purchases as $purchase)
                            <option value="{{ $purchase->id }}" {{ old('purchase_id', $purchaseReturn->purchase_id) == $purchase->id ? 'selected' : '' }}>
                                {{ $purchase->batch_no }} - {{ $purchase->product->name ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="qty_returned" class="form-label">Quantity Returned <span class="text-danger">*</span></label>
                    <input type="number" name="qty_returned" class="form-control" required min="1" value="{{ old('qty_returned', $purchaseReturn->qty_returned) }}">
                </div>
                <div class="mb-3">
                    <label for="return_reason" class="form-label">Return Reason <span class="text-danger">*</span></label>
                    <input type="text" name="return_reason" class="form-control" required value="{{ old('return_reason', $purchaseReturn->return_reason) }}">
                </div>
                <div class="mb-3">
                    <label for="amount_refunded" class="form-label">Amount Refunded ($) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="amount_refunded" class="form-control" required min="0" value="{{ old('amount_refunded', $purchaseReturn->amount_refunded) }}">
                </div>
                <button class="btn btn-success" type="submit">
                    <i class="fas fa-save"></i> Update Purchase Return
                </button>
                <a href="{{ route('purchase-returns.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </form>
        </div>
    </div>
@endsection
