@extends('layouts.app')

@section('title', 'Edit Purchase')

@section('page_title', 'Edit Purchase')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('purchases.update', $purchase->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Product Selection and Vendor Selection in a single row -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="product_id" class="form-label">Product <span class="text-danger">*</span></label>
                        <select name="product_id" id="product_id" class="form-control select2-ajax @error('product_id') is-invalid @enderror" required>
                            @if(old('product_id'))
                                <!-- Pre-selected option will be appended via JavaScript -->
                            @else
                                <option value="">Select Product</option>
                            @endif
                        </select>
                        @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="vendor_id" class="form-label">Vendor <span class="text-danger">*</span></label>
                        <select name="vendor_id" id="vendor_id" class="form-control select2-ajax @error('vendor_id') is-invalid @enderror" required>
                            @if(old('vendor_id'))
                                <!-- Pre-selected option will be appended via JavaScript -->
                            @else
                                <option value="">Select Vendor</option>
                            @endif
                        </select>
                        @error('vendor_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Batch Number and Invoice Number in a single row -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="batch_no" class="form-label">Batch No.</label>
                        <input type="text" name="batch_no" id="batch_no" class="form-control @error('batch_no') is-invalid @enderror" value="{{ old('batch_no', $purchase->batch_no) }}">
                        @error('batch_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="invoice_no" class="form-label">Invoice No.</label>
                        <input type="text" name="invoice_no" id="invoice_no" class="form-control @error('invoice_no') is-invalid @enderror" value="{{ old('invoice_no', $purchase->invoice_no) }}">
                        @error('invoice_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Purchase Date and Expiry Date in a single row -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="purchase_date" class="form-label">Purchase Date <span class="text-danger">*</span></label>
                        <input type="date" name="purchase_date" id="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror" required value="{{ old('purchase_date', $purchase->purchase_date->format('Y-m-d')) }}">
                        @error('purchase_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="col-md-6">
                        <label for="expiry_date" class="form-label">Expiry Date</label>
                        <input type="date" name="expiry_date" id="expiry_date" class="form-control @error('expiry_date') is-invalid @enderror" value="{{ old('expiry_date', $purchase->expiry_date ? $purchase->expiry_date->format('Y-m-d') : '') }}">
                        @error('expiry_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Quantity Received and Cost Price in a single row -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="qty_received" class="form-label">Quantity Received <span class="text-danger">*</span></label>
                        <input type="number" name="qty_received" id="qty_received" class="form-control @error('qty_received') is-invalid @enderror" required min="1" value="{{ old('qty_received', $purchase->qty_received) }}">
                        @error('qty_received')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="cost_price" class="form-label">Cost Price ($) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="cost_price" id="cost_price" class="form-control @error('cost_price') is-invalid @enderror" required min="0" value="{{ old('cost_price', $purchase->cost_price) }}">
                        @error('cost_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Sale Price and Total Cost in a single row -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="sale_price" class="form-label">Sale Price ($) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="sale_price" id="sale_price" class="form-control @error('sale_price') is-invalid @enderror" required min="0" value="{{ old('sale_price', $purchase->sale_price) }}">
                        @error('sale_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="total_cost" class="form-label">Total Cost ($) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="total_cost" id="total_cost" class="form-control @error('total_cost') is-invalid @enderror" required min="0" value="{{ old('total_cost', $purchase->total_cost) }}">
                        @error('total_cost')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Total Sale in a single row -->
                <div class="mb-3">
                    <label for="total_sale" class="form-label">Total Sale ($) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="total_sale" id="total_sale" class="form-control @error('total_sale') is-invalid @enderror" required min="0" value="{{ old('total_sale', $purchase->total_sale) }}">
                    @error('total_sale')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Purchase
                </button>
                <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <!-- Select2 CSS (if not already included in layouts.app) -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style type="text/css">
        .select2-selection{
            height: 38px !important;
        }
    </style>
@endpush

@push('scripts')
    <!-- Ensure jQuery is loaded before this script -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize AJAX-based Select2 for Products
            $('#product_id').select2({
                placeholder: 'Select a product',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: '{{ route("products.search") }}',
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
                            results: data.data.map(function(product) {
                                return {
                                    id: product.id,
                                    text: product.name + ' (SKU: ' + (product.sku || 'N/A') + ')'
                                };
                            }),
                            pagination: {
                                more: data.current_page < data.last_page
                            }
                        };
                    },
                    cache: true,
                    error: function(xhr, status, error) {
                        console.error('Select2 AJAX Error:', error);
                    }
                },
                minimumInputLength: 1,
            });

            // Initialize AJAX-based Select2 for Vendors
            $('#vendor_id').select2({
                placeholder: 'Select a vendor',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: '{{ route("vendors.search") }}',
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
                            results: data.data.map(function(vendor) {
                                return {
                                    id: vendor.id,
                                    text: vendor.name
                                };
                            }),
                            pagination: {
                                more: data.current_page < data.last_page
                            }
                        };
                    },
                    cache: true,
                    error: function(xhr, status, error) {
                        console.error('Select2 AJAX Error:', error);
                    }
                },
                minimumInputLength: 1,
            });

            // Function to calculate Total Cost and Total Sale
            function calculateTotals() {
                var qty = parseFloat($('#qty_received').val()) || 0;
                var costPrice = parseFloat($('#cost_price').val()) || 0;
                var salePrice = parseFloat($('#sale_price').val()) || 0;

                var totalCost = qty * costPrice;
                var totalSale = qty * salePrice;

                // Update the total fields
                $('#total_cost').val(totalCost.toFixed(2));
                $('#total_sale').val(totalSale.toFixed(2));
            }

            // Attach event listeners to input fields
            $('#qty_received, #cost_price, #sale_price').on('input', function() {
                calculateTotals();
            });

            // Pre-select existing product and vendor
            @php
                $selectedProduct = old('product_id') ?? $purchase->product_id;
                $selectedVendor = old('vendor_id') ?? $purchase->vendor_id;
            @endphp

            // Function to fetch and pre-select a product
            function preselectProduct(productId) {
                if (!productId) return;

                // Make an AJAX request to fetch the product details
                $.ajax({
                    type: 'GET',
                    url: '{{ route("products.search") }}',
                    data: {
                        q: '', // empty query to fetch all (could be optimized)
                        page: 1
                    },
                    success: function(data) {
                        var product = data.data.find(p => p.id == productId);
                        if (product) {
                            var option = new Option(product.name + ' (SKU: ' + (product.sku || 'N/A') + ')', product.id, true, true);
                            $('#product_id').append(option).trigger('change');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error pre-selecting product:', error);
                    }
                });
            }

            // Function to fetch and pre-select a vendor
            function preselectVendor(vendorId) {
                if (!vendorId) return;

                // Make an AJAX request to fetch the vendor details
                $.ajax({
                    type: 'GET',
                    url: '{{ route("vendors.search") }}',
                    data: {
                        q: '', // empty query to fetch all (could be optimized)
                        page: 1
                    },
                    success: function(data) {
                        var vendor = data.data.find(v => v.id == vendorId);
                        if (vendor) {
                            var option = new Option(vendor.name, vendor.id, true, true);
                            $('#vendor_id').append(option).trigger('change');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error pre-selecting vendor:', error);
                    }
                });
            }

            // Pre-select the product and vendor
            preselectProduct({{ $selectedProduct }});
            preselectVendor({{ $selectedVendor }});
        });
    </script>
@endpush
