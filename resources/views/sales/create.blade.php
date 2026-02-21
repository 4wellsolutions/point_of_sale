@extends('layouts.app')

@section('title', 'Create Sale')

@section('content_header')
    <h1>Create Sale</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form id="sale-form" action="{{ route('sales.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <!-- Customer Selection -->
                    <div class="col-md-6">
                        <label for="customer_id" class="form-label">Customer</label>
                        <select name="customer_id" id="customer_id" class="form-control" required>
                        </select>
                    </div>

                    <!-- Invoice Number -->
                    <div class="col-md-3">
                        <label for="invoice_no" class="form-label">Invoice No.</label>
                        <div class="input-group">
                            <input type="text" name="invoice_no" value="{{$invoice_no}}" id="invoice_no"
                                class="form-control @error('invoice_no') is-invalid @enderror" readonly>
                            <button type="button" class="btn btn-outline-secondary" id="refresh_invoice_btn">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <div id="invoice_no_feedback" class="invalid-feedback"></div>
                        </div>
                    </div>

                    <!-- Sale Date -->
                    <div class="col-md-3">
                        <label for="sale_date" class="form-label">Sale Date</label>
                        <input type="date" name="sale_date" id="sale_date" class="form-control" required
                            value="{{ now()->format('Y-m-d') }}">
                    </div>
                </div>

                <!-- Product Selection -->
                <div class="mb-4">
                    <h5>Products <span class="text-danger">*</span></h5>
                    <div class="row g-1">
                        <div class="col-11"> <!-- Adjust the width of the select element -->
                            <select id="product_select" class="form-select" aria-label="Select Product">
                                <option value="">Select Product</option>
                                <!-- Options loaded via Select2 AJAX -->
                            </select>
                        </div>
                        <div class="col-1"> <!-- Adjust the width of the button -->
                            <button type="button" class="btn btn-primary w-100" id="add_product_btn">
                                <i class="fas fa-plus"></i> Add
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sale Items Grid -->
                <div class="mb-4">
                    <h5>Sale Items</h5>
                    <table class="table table-bordered" id="sale-items-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Batch No</th>
                                <th>Location</th>
                                <th>Available Qty</th>
                                <th>Cost Price ({{ setting('currency_symbol', '$') }})</th>
                                <th>Sale Price ({{ setting('currency_symbol', '$') }})</th>
                                <th>Quantity</th>
                                <th>Item Total ({{ setting('currency_symbol', '$') }})</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dynamic rows will be appended here -->
                        </tbody>
                    </table>
                </div>

                <!-- Totals, Discount, Net Amount -->
                <div class="row mb-3">
                    <div class="col-md-4 offset-md-8">
                        <table class="table">
                            <tr>
                                <th>Total Amount ({{ setting('currency_symbol', '$') }}):</th>
                                <td><input type="number" step="0.01" name="total_amount" id="total_amount"
                                        class="form-control" readonly></td>
                            </tr>
                            <tr>
                                <th>Discount ({{ setting('currency_symbol', '$') }}):</th>
                                <td>
                                    <input type="number" step="0.01" name="discount_amount" id="discount_amount"
                                        class="form-control" value="0" min="0">
                                </td>
                            </tr>
                            <tr>
                                <th>Net Amount ({{ setting('currency_symbol', '$') }}):</th>
                                <td><input type="number" step="0.01" name="net_amount" id="net_amount" class="form-control"
                                        readonly></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Payment Methods Section -->
                <div class="mb-4 p-2" id="payment-methods-container-wrapper">
                    <h5>Payment Methods</h5>
                    <button type="button" class="btn btn-success mb-3" id="add_payment_method_btn">
                        <i class="fas fa-plus"></i> Add Payment Method
                    </button>
                    <button type="button" class="btn btn-danger mb-3 ms-2" id="remove_all_payment_methods_btn"
                        style="display: none;">
                        <i class="fas fa-trash"></i> Remove All
                    </button>
                    <div id="payment-methods-container">
                        <!-- Payment Method Rows will be appended here -->
                    </div>
                </div>

                <!-- Total Payment Display (Optional) -->
                <div class="row mb-3">
                    <div class="col-md-4 offset-md-8">
                        <table class="table">
                            <tr>
                                <th>Total Payment ({{ setting('currency_symbol', '$') }}):</th>
                                <td><input type="number" step="0.01" id="total_payment_amount" class="form-control"
                                        readonly></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Submit Sale
                </button>
                <a href="{{ route('sales.index') }}" class="btn btn-secondary">Cancel</a>
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

        /* Style for error highlighting */
        .error-border {
            border: 1px solid red !important;
        }

        /* Below-cost warning */
        .below-cost-warning {
            border-color: #f59e0b !important;
            background-color: #fffbeb !important;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.25) !important;
        }

        .below-cost-text {
            color: #d97706;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 2px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function () {
            // Initialize Select2 for Customer Selection via AJAX
            $('#customer_id').select2({
                placeholder: 'Select a customer',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: '{{ route("customers.search") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { q: params.term };
                    },
                    processResults: function (data) {
                        return { results: data.results };
                    }
                }
            });

            // Initialize Select2 for Product Selection via AJAX
            $('#product_select').select2({
                placeholder: 'Select a product',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: '{{ route("products.search") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { q: params.term };
                    },
                    processResults: function (data) {
                        return { results: data.results };
                    }
                }
            });

            let saleItemIndex = 0;

            // Define a function that adds a product row to the sale items grid
            function addProduct() {
                let productId = $('#product_select').val();
                let productData = $('#product_select').select2('data')[0];

                if (!productId) {
                    toastr.warning('Please select a product.');
                    $('#product-input-group').addClass('error-border');
                    return;
                } else {
                    $('#product-input-group').removeClass('error-border');
                }

                // Check if the product already exists in the grid
                if ($('#sale-items-table tbody tr[data-product-id="' + productId + '"]').length > 0) {
                    toastr.error('Product already added');
                    $('#product_select').val(null).trigger('change'); // Clear selection
                    return;
                }

                // Create new sale item row with hidden purchase_price field
                let newRow = `
                        <tr data-product-id="${productId}">
                            <td>
                                ${productData.text}
                                <input type="hidden" name="sale_items[${saleItemIndex}][product_id]" value="${productId}">
                            </td>
                            <td>
                                <select name="sale_items[${saleItemIndex}][batch_no]" class="form-select batch-select" required>
                                    <option value="">Loading batches...</option>
                                </select>
                            </td>
                            <td>
                                <select name="sale_items[${saleItemIndex}][location_id]" class="form-select location-select" required>
                                    <option value="">Select Batch First</option>
                                </select>
                            </td>
                            <td>
                                <input type="number" class="form-control available-qty" readonly value="0">
                            </td>
                            <td>
                                <input type="number" step="0.01" class="form-control cost_price_display" readonly value="0.00" style="background:#f8f9fa;">
                                <input type="hidden" name="sale_items[${saleItemIndex}][purchase_price]" class="purchase_price_hidden" value="0.00">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="sale_items[${saleItemIndex}][sale_price]" class="form-control sale_price" required min="0" value="0.00">
                                <div class="below-cost-text d-none">⚠ Below cost price!</div>
                            </td>
                            <td>
                                <input type="number" name="sale_items[${saleItemIndex}][quantity]" class="form-control sale-qty" required min="1">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="sale_items[${saleItemIndex}][total_amount]" class="form-control item-total" value="0.00" readonly>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-sale-item">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                $('#sale-items-table tbody').append(newRow);
                loadBatches(productId, saleItemIndex);
                saleItemIndex++;

                // Clear the product select after adding the row
                $('#product_select').val(null).trigger('change');
            }

            // Bind the Add button click event to addProduct
            $('#add_product_btn').click(function () {
                addProduct();
            });

            // Automatically add product when selected from select2
            $('#product_select').on('select2:select', function (e) {
                addProduct();
            });

            // Remove Sale Item from the grid
            $('#sale-items-table').on('click', '.remove-sale-item', function () {
                $(this).closest('tr').remove();
                updateTotals();
            });

            // Load batches for a given product using its ID and index for name attributes
            function loadBatches(productId, index) {
                let batchSelect = $(`select[name="sale_items[${index}][batch_no]"]`);
                $.ajax({
                    url: '/products/' + productId + '/batches',
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        let options = '<option value="">Select Batch</option>';
                        data.batches.forEach(batch => {
                            options += `<option value="${batch.batch_no}">${batch.batch_no}</option>`;
                        });
                        batchSelect.html(options);
                    }
                });
            }

            // On Batch Change, load related locations (with sale and purchase prices)
            $('#sale-items-table').on('change', '.batch-select', function () {
                let row = $(this).closest('tr');
                let batchNo = $(this).val();
                let productId = row.data('product-id');
                let locationSelect = row.find('.location-select');

                $.ajax({
                    url: `/batches/${batchNo}/products/${productId}/locations`,
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            let options = '<option value="">Select Location</option>';
                            response.locations.forEach(location => {
                                options += `<option value="${location.id}" data-quantity="${location.quantity}" data-sale-price="${location.sale_price}" data-purchase-price="${location.purchase_price}">${location.name}</option>`;
                            });
                            locationSelect.html(options);
                        } else {
                            toastr.error(response.error);
                        }
                    }
                });
            });

            // Update available quantity and auto-fill sale/purchase prices when location is selected
            $('#sale-items-table').on('change', '.location-select', function () {
                let selectedOption = $(this).find(':selected');
                let row = $(this).closest('tr');
                let availableQtyInput = row.find('.available-qty');
                let quantity = selectedOption.data('quantity') || 0;
                availableQtyInput.val(quantity);

                // Auto-populate the sale price (will be 0, admin sets it)
                let salePrice = selectedOption.data('sale-price') || 0;
                row.find('.sale_price').val(parseFloat(salePrice).toFixed(2));

                // Populate the visible cost price and hidden purchase_price field
                let purchasePrice = selectedOption.data('purchase-price') || 0;
                row.find('.cost_price_display').val(parseFloat(purchasePrice).toFixed(2));
                row.find('.purchase_price_hidden').val(parseFloat(purchasePrice).toFixed(2));

                // Check below-cost warning
                checkBelowCost(row);

                // Recalculate the item total
                calculateItemTotal(row);
            });

            // Calculate Item Total when sale quantity changes
            $('#sale-items-table').on('input', '.sale-qty', function () {
                let row = $(this).closest('tr');
                calculateItemTotal(row);
            });

            // Calculate the total for an item and update totals for the sale
            function calculateItemTotal(row) {
                let qty = parseFloat(row.find('.sale-qty').val()) || 0;
                let salePrice = parseFloat(row.find('.sale_price').val()) || 0;
                let itemTotal = qty * salePrice;
                row.find('.item-total').val(itemTotal.toFixed(2));
                updateTotals();
            }

            // Below-cost check function
            function checkBelowCost(row) {
                let salePrice = parseFloat(row.find('.sale_price').val()) || 0;
                let purchasePrice = parseFloat(row.find('.purchase_price_hidden').val()) || 0;
                let warningText = row.find('.below-cost-text');
                let salePriceInput = row.find('.sale_price');

                if (salePrice > 0 && purchasePrice > 0 && salePrice < purchasePrice) {
                    salePriceInput.addClass('below-cost-warning');
                    warningText.removeClass('d-none');
                } else {
                    salePriceInput.removeClass('below-cost-warning');
                    warningText.addClass('d-none');
                }
            }

            // Listen for sale_price changes to recalculate and check below-cost
            $('#sale-items-table').on('input', '.sale_price', function () {
                let row = $(this).closest('tr');
                calculateItemTotal(row);
                checkBelowCost(row);
            });

            // Validate sale quantity against available quantity
            $('#sale-items-table').on('input', '.sale-qty', function () {
                let row = $(this).closest('tr');
                let availableQty = parseInt(row.find('.available-qty').val()) || 0;
                let saleQty = parseInt($(this).val()) || 0;

                if (saleQty > availableQty) {
                    toastr.error("Cannot sell more than available quantity.");
                    $(this).val(availableQty);
                    calculateItemTotal(row);
                }
            });

            // Update Totals, Discount, and Net Amount of the sale
            function updateTotals() {
                let totalAmount = 0;
                $('.item-total').each(function () {
                    totalAmount += parseFloat($(this).val()) || 0;
                });

                let discount = parseFloat($('#discount_amount').val()) || 0;
                let netAmount = totalAmount - discount;

                $('#total_amount').val(totalAmount.toFixed(2));
                $('#net_amount').val(netAmount.toFixed(2));

                updatePaymentTotals();
            }

            // Update totals when discount amount changes
            $('#discount_amount').on('input', function () {
                updateTotals();
            });

            // -----------------------------
            // Payment Methods Functionality
            // -----------------------------
            let paymentMethodIndex = 0;

            function addPaymentMethodRow() {
                let paymentMethods = @json($paymentMethods);
                let options = '<option value="">Select Payment Method</option>';
                paymentMethods.forEach(function (method) {
                    options += `<option value="${method.id}">${method.method_name}</option>`;
                });

                let newRow = `
                        <div class="row g-2 payment-method-row">
                            <div class="col-md-6">
                                <label class="form-label">Payment Method</label>
                                <select name="payment_methods[${paymentMethodIndex}][payment_method_id]" class="form-select payment-method-select">
                                    ${options}
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Amount ({{ setting('currency_symbol', '$') }})</label>
                                <input type="number" step="0.01" name="payment_methods[${paymentMethodIndex}][amount]" class="form-control payment-amount" min="0.01" style="display: none;">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-danger remove-payment-method">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                    `;
                $('#payment-methods-container').append(newRow);
                paymentMethodIndex++;
                toggleRemoveAllButton();
            }

            $('#add_payment_method_btn').click(function () {
                addPaymentMethodRow();
            });

            $('#payment-methods-container').on('click', '.remove-payment-method', function () {
                $(this).closest('.payment-method-row').remove();
                updatePaymentTotals();
                toggleRemoveAllButton();
            });

            $('#remove_all_payment_methods_btn').click(function () {
                $('#payment-methods-container').empty();
                $('#total_payment_amount').val('0.00');
                $('#payment-methods-container-wrapper').removeClass('error-shadow');
                $(this).hide();
                toastr.info('All payment methods have been removed.');
            });

            $('#payment-methods-container').on('change', '.payment-method-select', function () {
                let selectedValue = $(this).val();
                let amountField = $(this).closest('.payment-method-row').find('.payment-amount');

                if (selectedValue) {
                    amountField.slideDown();
                } else {
                    amountField.slideUp();
                    amountField.val('');
                }
                updatePaymentTotals();
            });

            function toggleRemoveAllButton() {
                if ($('#payment-methods-container .payment-method-row').length > 0) {
                    $('#remove_all_payment_methods_btn').show();
                } else {
                    $('#remove_all_payment_methods_btn').hide();
                }
            }

            function updatePaymentTotals() {
                let totalPayment = 0;
                $('.payment-amount:visible').each(function () {
                    totalPayment += parseFloat($(this).val()) || 0;
                });
                $('#total_payment_amount').val(totalPayment.toFixed(2));

                // Remove error shadow if exists
                $('#payment-methods-container-wrapper').removeClass('error-shadow');

                // Apply error shadow if payments do not match net amount
                let netAmount = parseFloat($('#net_amount').val()) || 0;
                if (totalPayment > 0 && totalPayment !== netAmount) {
                    $('#payment-methods-container-wrapper').addClass('error-shadow');
                }
            }

            $('#payment-methods-container').on('input', '.payment-amount', function () {
                updatePaymentTotals();
            });

            // -----------------------------
            // AJAX Form Submission with Validation
            // -----------------------------
            $('#sale-form').submit(function (e) {
                e.preventDefault();
                let hasError = false;

                // Remove previous error indicators
                $('#customer_id').next('.select2').find('.select2-selection').removeClass('error-border');
                $('#product-input-group').removeClass('error-border');

                // Validate customer selection
                if (!$('#customer_id').val()) {
                    $('#customer_id').next('.select2').find('.select2-selection').addClass('error-border');
                    hasError = true;
                }

                // Validate that at least one product is added
                if ($('#sale-items-table tbody tr').length === 0) {
                    $('#product-input-group').addClass('error-border');
                    hasError = true;
                }

                if (hasError) {
                    toastr.error('Please select a customer and add at least one product.');
                    return;
                }

                // Serialize the form data and submit via AJAX
                let formData = $(this).serialize();
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        toastr.success('Sale created successfully!');
                        window.location.href = '{{ route("sales.index") }}';
                    },
                    error: function (xhr) {
                        toastr.error('An error occurred while submitting the sale.');
                    }
                });
            });
        });

    </script>
@endpush