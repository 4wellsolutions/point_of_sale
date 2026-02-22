@extends('layouts.app')
@section('title', 'Edit Receipt')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('receipts.index') }}">Receipts</a></li>
    <li class="breadcrumb-item active">Edit Receipt #{{ $receipt->id }}</li>
@endsection
@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="m-0"><i class="fas fa-hand-holding-usd me-2"></i>Edit Receipt #{{ $receipt->id }}</h5>
            <a href="{{ route('receipts.index') }}" class="btn btn-sm btn-secondary"><i
                    class="fas fa-arrow-left me-1"></i>Back</a>
        </div>
        <div class="card-body" style="max-width:600px">
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif
            <form action="{{ route('receipts.update', $receipt->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Customer (Select2 AJAX, pre-selected) --}}
                <div class="mb-3">
                    <label class="form-label">Customer <span class="text-danger">*</span></label>
                    <select name="customer_id" id="customer_id"
                        class="form-control @error('customer_id') is-invalid @enderror" style="width:100%" required>
                        @if($currentParty)
                            <option value="{{ $currentParty->id }}" selected>{{ $currentParty->name }}</option>
                        @endif
                    </select>
                    @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Payment Method --}}
                <div class="mb-3">
                    <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                    <select name="payment_method_id" class="form-control @error('payment_method_id') is-invalid @enderror"
                        required>
                        <option value="">Select Method</option>
                        @foreach($paymentMethods as $m)
                            <option value="{{ $m->id }}" {{ $receipt->payment_method_id == $m->id ? 'selected' : '' }}>
                                {{ $m->method_name }}
                            </option>
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
                        value="{{ old('amount', $receipt->amount) }}" required>
                    @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Date --}}
                <div class="mb-3">
                    <label class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" name="transaction_date"
                        class="form-control @error('transaction_date') is-invalid @enderror"
                        value="{{ old('transaction_date', $receipt->transaction_date) }}" required>
                    @error('transaction_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Update Receipt</button>
                <a href="{{ route('receipts.index') }}" class="btn btn-secondary ms-2">Cancel</a>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .select2-container .select2-selection--single {
            height: 38px !important;
            padding: 0.25rem !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#customer_id').select2({
                placeholder: 'Search customer by name...',
                allowClear: true,
                minimumInputLength: 0,
                ajax: {
                    url: '{{ route("customers.search") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) { return { q: params.term || '', page: params.page || 1 }; },
                    processResults: function (data, params) {
                        return { results: data.results, pagination: { more: data.pagination.more } };
                    },
                    cache: true
                }
            });
        });
    </script>
@endpush