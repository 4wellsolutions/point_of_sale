@extends('layouts.app')

@section('title', 'Add Transaction')

@section('page_title', 'Add Transaction')

@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h3 class="card-title m-0">Create New Transaction</h3>
            <div class="card-tools ms-auto">
                <a href="{{ route('transactions.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to Transactions
                </a>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('transactions.store') }}" method="POST" id="transactionForm">
                @csrf

                <!-- Transaction For: Customer or Vendor -->
                <div class="mb-3">
                    <label for="transactionable_type" class="form-label">Transaction For <span
                            class="text-danger">*</span></label>
                    <select name="transactionable_type" id="transactionable_type"
                        class="form-control @error('transactionable_type') is-invalid @enderror" required>
                        <option value="">Select Type</option>
                        <option value="customer" {{ old('transactionable_type') == 'customer' ? 'selected' : '' }}>Customer
                        </option>
                        <option value="vendor" {{ old('transactionable_type') == 'vendor' ? 'selected' : '' }}>Vendor</option>
                    </select>
                    @error('transactionable_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Select Customer or Vendor (AJAX Search) -->
                <div class="mb-3" id="transactionable_select_container" style="display: none;">
                    <label for="transactionable_id" class="form-label">Select <span
                            id="transactionable_label">Customer/Vendor</span> <span class="text-danger">*</span></label>
                    <select name="transactionable_id" id="transactionable_id"
                        class="form-control @error('transactionable_id') is-invalid @enderror" style="width: 100%;">
                        <!-- Options will be loaded via AJAX -->
                    </select>
                    @error('transactionable_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Payment Method -->
                <div class="mb-3">
                    <label for="payment_method_id" class="form-label">Payment Method <span
                            class="text-danger">*</span></label>
                    <select name="payment_method_id" id="payment_method_id"
                        class="form-control @error('payment_method_id') is-invalid @enderror" required>
                        <option value="">Select Payment Method</option>
                        @foreach ($paymentMethods as $method)
                            <option value="{{ $method->id }}" {{ old('payment_method_id') == $method->id ? 'selected' : '' }}>
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
                    <label for="amount" class="form-label">Amount ({{ setting('currency_symbol', '$') }}) <span
                            class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="amount" id="amount"
                        class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required>
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Transaction Type -->
                <div class="mb-3">
                    <label for="transaction_type" class="form-label">Transaction Type <span
                            class="text-danger">*</span></label>
                    <select name="transaction_type" id="transaction_type"
                        class="form-control @error('transaction_type') is-invalid @enderror" required>
                        <option value="">Select Type</option>
                        <option value="credit" {{ old('transaction_type') == 'credit' ? 'selected' : '' }}>Credit</option>
                        <option value="debit" {{ old('transaction_type') == 'debit' ? 'selected' : '' }}>Debit</option>
                    </select>
                    @error('transaction_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Transaction Date -->
                @php
                    use Carbon\Carbon;
                    $defaultDate = old('transaction_date', Carbon::now()->format('Y-m-d'));
                @endphp

                <div class="mb-3">
                    <label for="transaction_date" class="form-label">Transaction Date <span
                            class="text-danger">*</span></label>
                    <input type="date" name="transaction_date" id="transaction_date"
                        class="form-control @error('transaction_date') is-invalid @enderror" value="{{ $defaultDate }}"
                        required>
                    @error('transaction_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                <!-- Submit and Back Buttons -->
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Save Transaction
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
            const transactionType = $('#transaction_type');

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

                    // If there's old input, set the value
                    @if(old('transactionable_id'))
                        var option = new Option('{{ \App\Models\Customer::find(old("transactionable_id")) ? \App\Models\Customer::find(old("transactionable_id"))->name : (\App\Models\Vendor::find(old("transactionable_id")) ? \App\Models\Vendor::find(old("transactionable_id"))->name : "") }}', '{{ old("transactionable_id") }}', true, true);
                        transactionableSelect.append(option).trigger('change');
                    @endif
                }
            }

            // Function to toggle the transactionable fields
            function toggleTransactionableFields() {
                const selectedType = transactionableType.val();
                console.log("Selected Type: ", selectedType);
                if (selectedType === 'customer' || selectedType === 'vendor') {
                    transactionableLabel.text(selectedType.charAt(0).toUpperCase() + selectedType.slice(1));
                    transactionableSelectContainer.show();

                    // Destroy Select2 if already initialized
                    if ($.fn.select2 && transactionableSelect.hasClass('select2-hidden-accessible')) {
                        transactionableSelect.select2('destroy');
                    }

                    initializeSelect2(selectedType);
                } else {
                    transactionableSelectContainer.hide();
                    transactionableSelect.val(null).trigger('change');
                }

                // Update Transaction Type Options
                updateTransactionTypeOptions();
            }

            // Function to update Transaction Type options dynamically
            function updateTransactionTypeOptions() {
                const selectedType = transactionableType.val();
                transactionType.empty(); // Clear current options

                if (selectedType === 'vendor') {
                    transactionType.append(new Option('Debit (Inward)', 'debit'));
                    transactionType.append(new Option('Credit (Outward)', 'credit'));
                } else if (selectedType === 'customer') {
                    transactionType.append(new Option('Debit (Outward)', 'debit'));
                    transactionType.append(new Option('Credit (Inward)', 'credit'));
                }

                transactionType.trigger('change'); // Ensure the change reflects in UI
            }

            // Initial load based on old input
            toggleTransactionableFields();

            // Event listener for transactionable_type change
            transactionableType.on('change', function () {
                toggleTransactionableFields();
            });
        });
    </script>
@endpush