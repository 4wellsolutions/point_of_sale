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

                {{-- Vendor (Select2 AJAX) --}}
                <div class="mb-3">
                    <label class="form-label">Vendor <span class="text-danger">*</span></label>
                    <select name="vendor_id" id="vendor_id"
                        class="form-control @error('vendor_id') is-invalid @enderror" style="width:100%" required>
                        <option value="">Search vendor...</option>
                    </select>
                    @error('vendor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Payment Method --}}
                <div class="mb-3">
                    <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                    <select name="payment_method_id"
                        class="form-control @error('payment_method_id') is-invalid @enderror" required>
                        <option value="">Select Method</option>
                        @foreach($paymentMethods as $m)
                            <option value="{{ $m->id }}" {{ old('payment_method_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->method_name }}</option>
                        @endforeach
                    </select>
                    @error('payment_method_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Amount --}}
                <div class="mb-3">
                    <label class="form-label">Amount ({{ setting('currency_symbol', 'Rs.') }}) <span
                            class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="amount"
                        class="form-control @error('amount') is-invalid @enderror"
                        value="{{ old('amount') }}" required>
                    @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Date --}}
                <div class="mb-3">
                    <label class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" name="transaction_date"
                        class="form-control @error('transaction_date') is-invalid @enderror"
                        value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                    @error('transaction_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Notes --}}
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

@push('styles')
<style>
    .select2-container .select2-selection--single { height: 38px !important; padding: 0.25rem !important; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 38px !important; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 38px !important; }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function () {
        $('#vendor_id').select2({
            placeholder: 'Search vendor by name...',
            allowClear: true,
            minimumInputLength: 0,
            ajax: {
                url: '{{ route("vendors.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term || '', page: params.page || 1 };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: { more: data.pagination.more }
                    };
                },
                cache: true
            }
        });

        @if(old('vendor_id'))
            $('#vendor_id').append(new Option('{{ old("vendor_id") }}', '{{ old("vendor_id") }}', true, true)).trigger('change');
        @endif
    });
</script>
@endpush