@extends('layouts.app')

@section('title', 'Edit Transaction')

@section('page_title', 'Edit Transaction')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Edit Transaction #{{ $transaction->id }}</h3>
            <div class="card-tools">
                <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Transactions
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('transactions.update', $transaction->id) }}" method="POST" id="transactionForm">
                @csrf
                @method('PUT')

                <!-- Transaction For: Customer or Vendor -->
                <div class="mb-3">
                    <label for="transactionable_type" class="form-label">Transaction For <span class="text-danger">*</span></label>
                    <select name="transactionable_type" id="transactionable_type" class="form-control @error('transactionable_type') is-invalid @enderror" required>
                        <option value="">Select Type</option>
                        <option value="customer" {{ (old('transactionable_type', class_basename($transaction->transactionable_type)) == 'Customer') ? 'selected' : '' }}>Customer</option>
                        <option value="vendor" {{ (old('transactionable_type', class_basename($transaction->transactionable_type)) == 'Vendor') ? 'selected' : '' }}>Vendor</option>
                    </select>
                    @error('transactionable_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Select Customer or Vendor (AJAX Search) -->
                <div class="mb-3" id="transactionable_select_container" style="display: none;">
                    <label for="transactionable_id" class="form-label">Select <span id="transactionable_label">Customer/Vendor</span> <span class="text-danger">*</span></label>
                    <select name="transactionable_id" id="transactionable_id" class="form-control @error('transactionable_id') is-invalid @enderror" style="width: 100%;">
                        @if(old('transactionable_type', class_basename($transaction->transactionable_type)) == 'Customer')
                            <option value="{{ $transaction->transactionable->id }}" selected>{{ $transaction->transactionable->name }}</option>
                        @elseif(old('transactionable_type', class_basename($transaction->transactionable_type)) == 'Vendor')
                            <option value="{{ $transaction->transactionable->id }}" selected>{{ $transaction->transactionable->name }}</option>
                        @endif
                    </select>
                    @error('transactionable_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Payment Method -->
                <div class="mb-3">
                    <label for="payment_method_id" class="form-label">Payment Method <span class="text-danger">*</span></label>
                    <select name="payment_method_id" id="payment_method_id" class="form-control @error('payment_method_id') is-invalid @enderror" required>
                        <option value="">Select Payment Method</option>
                        @foreach ($paymentMethods as $method)
                            <option value="{{ $method->id }}" {{ (old('payment_method_id', $transaction->payment_method_id) == $method->id) ? 'selected' : '' }}>
                                {{ $method->method_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('payment_method_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Amount -->
                <div class="mb-3">
                    <label for="amount" class="form-label">Amount ($) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $transaction->amount) }}" required>
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Transaction Type -->
                <div class="mb-3">
                    <label for="transaction_type" class="form-label">Transaction Type <span class="text-danger">*</span></label>
                    <select name="transaction_type" id="transaction_type" class="form-control @error('transaction_type') is-invalid @enderror" required>
                        <option value="">Select Type</option>
                        <option value="credit" {{ (old('transaction_type', $transaction->transaction_type) == 'credit') ? 'selected' : '' }}>Credit</option>
                        <option value="debit" {{ (old('transaction_type', $transaction->transaction_type) == 'debit') ? 'selected' : '' }}>Debit</option>
                    </select>
                    @error('transaction_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Transaction Date -->
                <div class="mb-3">
                    <label for="transaction_date" class="form-label">Transaction Date <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="transaction_date" id="transaction_date" class="form-control @error('transaction_date') is-invalid @enderror" value="{{ old('transaction_date', $transaction->transaction_date->format('Y-m-d\TH:i')) }}" required>
                    @error('transaction_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit and Back Buttons -->
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </form>
        </div>
    </div>
@endsection

@push("styles")
<style type="text/css">
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
            const transactionableType = $('#transactionable_type');
            const transactionableSelectContainer = $('#transactionable_select_container');
            const transactionableSelect = $('#transactionable_id');
            const transactionableLabel = $('#transactionable_label');

            // Function to initialize Select2 with AJAX
            function initializeSelect2(type) {
                if (type === 'customer' || type === 'vendor') {
                    transactionableSelect.select2({
                        placeholder: 'Search and select',
                        allowClear: true,
                        ajax: {
                            url: type === 'customer' ? '{{ route("customers.search") }}' : '{{ route("vendors.search") }}',
                            dataType: 'json',
                            delay: 250,
                            data: function (params) {
                                return {
                                    q: params.term, // search term
                                    page: params.page || 1
                                };
                            },
                            processResults: function (data, params) {
                                params.page = params.page || 1;

                                return {
                                    results: data.results,
                                    pagination: {
                                        more: data.pagination.more
                                    }
                                };
                            },
                            cache: true
                        },
                        minimumInputLength: 1,
                    });

                    // If editing, pre-select the existing transactionable entity
                    @if(old('transactionable_id'))
                        var option = new Option('{{ \App\Models\Customer::find(old("transactionable_id")) ? \App\Models\Customer::find(old("transactionable_id"))->name : (\App\Models\Vendor::find(old("transactionable_id")) ? \App\Models\Vendor::find(old("transactionable_id"))->name : "") }}', '{{ old("transactionable_id") }}', true, true);
                        transactionableSelect.append(option).trigger('change');
                    @endif
                }
            }

            // Function to toggle the transactionable fields
            function toggleTransactionableFields() {
                const selectedType = transactionableType.val();
                if (selectedType === 'customer' || selectedType === 'vendor') {
                    transactionableLabel.text(selectedType.charAt(0).toUpperCase() + selectedType.slice(1));
                    transactionableSelectContainer.show();
                    initializeSelect2(selectedType);
                } else {
                    transactionableSelectContainer.hide();
                    transactionableSelect.val(null).trigger('change');
                }
            }

            // Initial toggle based on old input or existing transaction
            toggleTransactionableFields();

            // Event listener for transactionable_type change
            transactionableType.on('change', function () {
                // Destroy previous Select2 instance to prevent duplicates
                transactionableSelect.select2('destroy');
                toggleTransactionableFields();
            });
        });
    </script>
@endpush
