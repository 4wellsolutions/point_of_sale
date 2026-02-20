@extends('layouts.app')

@section('title', 'Add Sales Return')

@section('page_title', 'Add Sales Return')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('sales-returns.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="sale_id" class="form-label">Sale <span class="text-danger">*</span></label>
                    <select name="sale_id" class="form-control" required>
                        <option value="">Select Sale</option>
                        @foreach($sales as $sale)
                            <option value="{{ $sale->id }}" {{ old('sale_id') == $sale->id ? 'selected' : '' }}>
                                Sale #{{ $sale->id }} - {{ $sale->product->name ?? 'N/A' }} -
                                {{ $sale->customer->name ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="qty_returned" class="form-label">Quantity Returned <span
                            class="text-danger">*</span></label>
                    <input type="number" name="qty_returned" class="form-control" required min="1"
                        value="{{ old('qty_returned') }}">
                </div>
                <div class="mb-3">
                    <label for="return_reason" class="form-label">Return Reason</label>
                    <textarea name="return_reason" class="form-control" rows="4">{{ old('return_reason') }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="refund_amount" class="form-label">Refund Amount ({{ setting('currency_symbol', '$') }})
                        <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="refund_amount" class="form-control" required min="0"
                        value="{{ old('refund_amount') }}">
                </div>
                <button class="btn btn-success" type="submit">
                    <i class="fas fa-save"></i> Save Sales Return
                </button>
                <a href="{{ route('sales-returns.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </form>
        </div>
    </div>
@endsection