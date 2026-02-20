@extends('layouts.app')

@section('title', 'Ledgers')

@section('page_title', 'Manage Ledgers')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Ledgers</h3>
            <div class="card-tools">
                <a href="{{ route('ledgers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Ledgers
                </a>
            </div>
        </div>
        <div class="card-body">
            <form id="filter-form" action="{{ route('ledgers.index') }}" method="GET">
                @csrf
                <div class="row">

                    <!-- Ledger Type Filter -->
                    <div class="col-md-3">
                        <label for="ledgerable_type" class="form-label">Ledger Type</label>
                        <select name="ledgerable_type" id="ledgerable_type" class="form-control @error('ledgerable_type') is-invalid @enderror" required>
                            <option value="">Select Type</option>
                            <option value="customer" {{ request('ledgerable_type') == 'customer' ? 'selected' : '' }}>Customer</option>
                            <option value="vendor" {{ request('ledgerable_type') == 'vendor' ? 'selected' : '' }}>Vendor</option>
                        </select>
                        @error('ledgerable_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Select Customer or Vendor (AJAX Search) -->
                    <div class="col-md-3" id="ledgerable_select_container" style="display: {{ request('ledgerable_type') ? 'block' : 'none' }};">
                        <label for="ledgerable_id" class="form-label">Select <span id="ledgerable_label">
                            @if(request('ledgerable_type') == 'customer')
                                Customer
                            @elseif(request('ledgerable_type') == 'vendor')
                                Vendor
                            @else
                                Customer/Vendor
                            @endif
                        </span> <span class="text-danger">*</span></label>
                        <select name="ledgerable_id" id="ledgerable_id" class="form-control @error('ledgerable_id') is-invalid @enderror" style="width: 100%;">
                            @if(request('ledgerable_id'))
                                @php
                                    $ledgerableType = request('ledgerable_type') === 'customer' ? 'App\Models\Customer' : 'App\Models\Vendor';
                                    $ledgerable = $ledgerableType::find(request('ledgerable_id'));
                                @endphp
                                @if($ledgerable)
                                    <option value="{{ $ledgerable->id }}" selected>{{ $ledgerable->name }}</option>
                                @endif
                            @endif
                        </select>
                        @error('ledgerable_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Transaction Type Filter -->
                    <div class="col-md-2">
                        <label for="transaction_type" class="form-label">Transaction Type</label>
                        <select name="transaction_type" id="transaction_type" class="form-control @error('transaction_type') is-invalid @enderror">
                            <option value="">All</option>
                            <option value="sale" {{ request('transaction_type') == 'sale' ? 'selected' : '' }}>Sale</option>
                            <option value="purchase" {{ request('transaction_type') == 'purchase' ? 'selected' : '' }}>Purchase</option>
                            <option value="return" {{ request('transaction_type') == 'return' ? 'selected' : '' }}>Return</option>
                            <option value="payment" {{ request('transaction_type') == 'payment' ? 'selected' : '' }}>Payment</option>
                        </select>
                        @error('transaction_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Date Range Filters -->
                    <div class="col-md-2">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="form-control @error('start_date') is-invalid @enderror">
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="form-control @error('end_date') is-invalid @enderror">
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="row mt-3">
                    <div class="col-md-12 d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <button type="button" id="download-pdf-btn" class="btn btn-secondary">
                            <i class="fas fa-download"></i> View PDF
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Ledger Table -->
    <div class="card">
        <div class="card-body" id="ledger-table-container">
            @include('ledgers.table')
        </div>
    </div>
@endsection

@push("styles")
<style type="text/css">
        /* Select2 Styling */
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
            const ledgerableType = $('#ledgerable_type');
            const ledgerableSelectContainer = $('#ledgerable_select_container');
            const ledgerableSelect = $('#ledgerable_id');
            const ledgerableLabel = $('#ledgerable_label');

            // Function to initialize Select2 with AJAX for customers and vendors
            function initializeSelect2(type) {
                if (type === 'customer' || type === 'vendor') {
                    ledgerableSelect.select2({
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
                }
            }

            // Function to toggle the ledgerable fields
            function toggleLedgerableFields() {
                const selectedType = ledgerableType.val();

                if (selectedType === 'customer' || selectedType === 'vendor') {
                    ledgerableLabel.text(selectedType.charAt(0).toUpperCase() + selectedType.slice(1)); // Capitalize the label (Customer / Vendor)
                    ledgerableSelectContainer.show();

                    // Check if Select2 is initialized before destroying
                    if (ledgerableSelect.hasClass('select2-hidden-accessible')) {
                        ledgerableSelect.select2('destroy');
                        ledgerableSelect.empty(); // Clear previous options
                    }

                    initializeSelect2(selectedType);
                } else {
                    ledgerableSelectContainer.hide();
                    ledgerableSelect.val(null).trigger('change');
                }
            }

            // Initial toggle based on old input
            toggleLedgerableFields();

            // Event listener for ledgerable_type change
            ledgerableType.on('change', function () {
                toggleLedgerableFields();
            });

            // Initialize Select2 for ledgerable_id if already selected (on page load)
            @if(request('ledgerable_id'))
                ledgerableSelect.trigger('change');
            @endif

            // Handle the "Download PDF" button click event
            $('#download-pdf-btn').on('click', function () {
                const ledgerableType = $('#ledgerable_type').val();
                const ledgerableId = $('#ledgerable_id').val();
                const startDate = $('#start_date').val();
                const endDate = $('#end_date').val();
                const transactionType = $('#transaction_type').val();

                // Validate filters
                if ((ledgerableType === 'customer' || ledgerableType === 'vendor') && startDate && endDate) {
                    // Build the query string
                    let queryString = `?ledgerable_type=${ledgerableType}&start_date=${startDate}&end_date=${endDate}`;
                    if (ledgerableId) {
                        queryString += `&ledgerable_id=${ledgerableId}`;
                    }
                    if (transactionType) {
                        queryString += `&transaction_type=${transactionType}`;
                    }

                    // Open a new tab with the URL containing the query parameters
                    const url = `{{ route('ledgers.pdf', '') }}${queryString}`;
                    window.open(url, '_blank');
                } else {
                    alert('Please select a customer or vendor and specify the date range before downloading the PDF.');
                }
            });
        });
    </script>
@endpush
