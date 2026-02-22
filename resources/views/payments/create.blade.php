@extends('layouts.app')
@section('title', 'Record Payment')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('payments.index') }}">Payments</a></li>
    <li class="breadcrumb-item active">New Payment</li>
@endsection
@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="m-0"><i class="fas fa-money-check-alt me-2"></i>Record Payment to Vendor</h5>
            <a href="{{ route('payments.index') }}" class="btn btn-sm btn-secondary"><i
                    class="fas fa-arrow-left me-1"></i>Back</a>
        </div>
        <div class="card-body" style="max-width:600px">
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif
            <form action="{{ route('payments.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Vendor <span class="text-danger">*</span></label>
                    <select name="vendor_id" class="form-control @error('vendor_id') is-invalid @enderror" required>
                        <option value="">Select Vendor</option>
                        @foreach($vendors as $v)
                            <option value="{{ $v->id }}" {{ old('vendor_id') == $v->id ? 'selected' : '' }}>{{ $v->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('vendor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                    <select name="payment_method_id" class="form-control @error('payment_method_id') is-invalid @enderror"
                        required>
                        <option value="">Select Method</option>
                        @foreach($paymentMethods as $m)
                            <option value="{{ $m->id }}" {{ old('payment_method_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('payment_method_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Amount ({{ setting('currency_symbol', 'Rs.') }}) <span
                            class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="amount"
                        class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required>
                    @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" name="transaction_date"
                        class="form-control @error('transaction_date') is-invalid @enderror"
                        value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                    @error('transaction_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Save Payment</button>
                <a href="{{ route('payments.index') }}" class="btn btn-secondary ms-2">Cancel</a>
            </form>
        </div>
    </div>
@endsection