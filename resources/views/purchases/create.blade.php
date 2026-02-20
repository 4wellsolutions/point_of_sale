@extends('layouts.app')

@section('title', 'Add Purchase')

@section('page_title', 'Add Purchase')

@section('content')
    <div class="card">
        <div class="card-body">
            <form id="purchase-form">
                @csrf
                <!-- Vendor Selection, Invoice Number, and Purchase Date -->
                <div class="row mb-3">
                    <!-- Vendor Selection -->
                    <div class="col-md-6">
                        <label for="vendor_id" class="form-label">Vendor</label>
                        <select name="vendor_id" id="vendor_id" class="form-control @error('vendor_id') is-invalid @enderror" required>
                            <!-- Options loaded via Select2 AJAX -->
                        </select>
                        <div id="vendor_id_feedback" class="invalid-feedback"></div>
                    </div>

                    <!-- Invoice Number -->
                    <div class="col-md-3">
                        <label for="invoice_no" class="form-label">Invoice No.</label>
                        <div class="input-group">
                            <input type="text" name="invoice_no" value="{{$invoice_no}}" id="invoice_no" class="form-control @error('invoice_no') is-invalid @enderror" readonly>
                            <button type="button" class="btn btn-outline-secondary" id="refresh_invoice_btn">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <div id="invoice_no_feedback" class="invalid-feedback"></div>
                        </div>
                    </div>

                    <!-- Purchase Date -->
                    <div class="col-md-3">
                        <label for="purchase_date" class="form-label">Purchase Date <span class="text-danger">*</span></label>
                        <input type="date" name="purchase_date" id="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror" required value="{{ old('purchase_date', now()->format('Y-m-d')) }}">
                        <div id="purchase_date_feedback" class="invalid-feedback"></div>
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

                <!-- Purchase Items List -->
                <div class="mb-4">
                    <h5>Purchase Items</h5>
                    <div style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-bordered table-sm" id="purchase-items-table">
                            <thead>
                                <tr>
                                    <th style="width: 10%;">Image</th>
                                    <th style="width: 20%;">Product Name</th>
                                    <th style="width: 10%;">Expiry Date </th>
                                    <th style="width: 10%;">Location <span class="text-danger">*</span></th>
                                    <th style="width: 10%;">Batch No<span class="text-danger">*</span></th>
                                    <th style="width: 10%;">Quantity <span class="text-danger">*</span></th>
                                    <th style="width: 10%;" title="Price Per Piece">Cost Price<span class="text-danger">*</span></th>
                                    <th style="width: 10%;" title="Sale Per Piece">Sale Price<span class="text-danger">*</span></th> 
                                    <th style="width: 15%;">Total</th> 
                                    <th style="width: 5%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dynamic Purchase Items will be appended here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Total, Discount, Net Amount -->
                <div class="row mb-3">
                    <div class="col-md-5 offset-md-7">
                        <table class="table">
                            <tr>
                                <th>Total Amount ($):</th>
                                <td><input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control" readonly></td>
                            </tr>
                            <tr>
                                <th>Discount ($):</th>
                                <td>
                                    <input type="number" step="0.01" name="discount_amount" id="discount_amount" class="form-control @error('discount_amount') is-invalid @enderror" value="{{ old('discount_amount', 0) }}" min="0">
                                    @error('discount_amount')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <th>Net Amount ($):</th>
                                <td><input type="number" step="0.01" name="net_amount" id="net_amount" class="form-control" readonly></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="mb-4 p-2" id="payment-methods-container-wrapper">
                    <h5>Payment Methods</h5>
                    <button type="button" class="btn btn-success mb-3" id="add_payment_method_btn">
                        <i class="fas fa-plus"></i> Add Payment Method
                    </button>
                    <button type="button" class="btn btn-danger mb-3 ms-2" id="remove_all_payment_methods_btn" style="display: none;">
                        <i class="fas fa-trash"></i> Remove All
                    </button>
                    <div id="payment-methods-container">
                        <!-- Payment Method Rows will be appended here -->
                    </div>
                </div>

                <!-- Total Payment Display (Optional) -->
                <div class="row mb-3">
                    <div class="col-md-5 offset-md-7">
                        <table class="table">
                            <tr>
                                <th>Total Payment ($):</th>
                                <td><input type="number" step="0.01" id="total_payment_amount" class="form-control" readonly></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                    <div id="notes_feedback" class="invalid-feedback"></div>
                </div>

                <!-- Submit Buttons -->
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Save Purchase
                </button>
                <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </form>
        </div>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="productImageModal" tabindex="-1" aria-labelledby="productImageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center" id="modalContent">
                    <img src="" alt="Product Image" id="modalProductImage" class="img-fluid d-none">
                    <p id="modalNoImage" class="d-none">N/A</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <!-- Existing Styles -->
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

        /* Table Headers */
        table th {
            background-color: #f8f9fa;
            font-weight: bold;
            vertical-align: middle;
        }

        /* Equal width for columns */
        table td:nth-child(1),
        table td:nth-child(2),
        table td:nth-child(3),
        table td:nth-child(4),
        table td:nth-child(5),
        table td:nth-child(6),
        table td:nth-child(7),
        table td:nth-child(8),
        table td:nth-child(9) { /* Updated to include new column */
            vertical-align: middle;
        }

        /* Cursor pointer for calendar icon */
        .cursor-pointer {
            cursor: pointer;
        }

        /* Product thumbnail styling */
        .product-thumb {
            margin-right: 10px;
            border-radius: 5px;
        }

        /* Select2 with images */
        .select2-results__option img {
            width: 30px;
            height: 30px;
            margin-right: 10px;
            border-radius: 5px;
            vertical-align: middle;
        }
        .select2-selection__rendered img {
            width: 30px;
            height: 30px;
            margin-right: 10px;
            border-radius: 5px;
            vertical-align: middle;
        }

        /* Additional styles for payment methods */
        .payment-method-row {
            margin-bottom: 10px;
        }

        .payment-method-select {
            width: 100%;
        }

        /* Error Shadow for Payment Methods */
        .error-shadow {
            box-shadow: 0 0 10px rgba(220, 53, 69, 0.5);
        }
    </style>
@endpush

@push('scripts')    
    <script>
        $(document).ready(function() {
            // Initialize Select2 for Vendor with AJAX
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
                            results: data.results,
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                }
            });

            // Initialize Select2 for Product Selection with AJAX and Images
            $('#product_select').select2({ 
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
                            page: params.page || 1 // page number for pagination
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
                templateResult: formatProductOption,
                templateSelection: formatProductSelection
            });

            // Function to format product options with images in Select2
            function formatProductOption(product) {
                if (product.loading) {
                    return product.text;
                }

                if (product.image_url) {
                    return $( 
                        "<div class='d-flex align-items-center'><img src='" + product.image_url + "' class='img-thumbnail' style='width:30px; height:30px; margin-right:10px;' />" + product.text + "</div>"
                    );
                } else {
                    return $( 
                        "<div class='d-flex align-items-center'><span>N/A</span> " + product.text + "</div>"
                    );
                }
            }

            function formatProductSelection(product) {
                if (!product.id) {
                    return product.text;
                }

                if (product.image_url) {
                    return $( 
                        "<div class='d-flex align-items-center'><img src='" + product.image_url + "' class='img-thumbnail' style='width:30px; height:30px; margin-right:10px;' />" + product.text + "</div>"
                    );
                } else {
                    return $( 
                        "<div class='d-flex align-items-center'><span>N/A</span> " + product.text + "</div>"
                    );
                }
            }

            // Function to initialize Select2 on Location dropdowns
            function initializeSelect2(selector) {
                $(selector).select2({
                    placeholder: 'Select a location',
                    allowClear: true,
                    width: '100%',
                });
            }

            // Function to update totals
            function updateTotals() {
                let totalAmount = 0;
                $('.total_amount').each(function() { 
                    let val = parseFloat($(this).val()) || 0;
                    totalAmount += val;
                });

                let discount = parseFloat($('#discount_amount').val()) || 0;
                let netAmount = totalAmount - discount;

                $('#total_amount').val(totalAmount.toFixed(2));
                $('#net_amount').val(netAmount.toFixed(2));

                updatePaymentTotals();
            }


            // Refresh Invoice Number on Button Click
            $('#refresh_invoice_btn').click(function() {
                fetchInvoiceNumber();
                toastr.info('Invoice number refreshed.');
            });

            // Initialize Purchase Items and Payment Methods indices
            let purchaseItemIndex = 0;
            let paymentMethodIndex = 0; // Start at 0 since no initial rows

            // Add Product to Purchase Items
            $('#add_product_btn').click(function() {
                let productId = $('#product_select').val();
                let productData = $('#product_select').select2('data')[0];

                if (!productId) {
                    toastr.warning('Please select a product.');
                    return;
                }

                // Check if product is already added
                let exists = false;
                $('#purchase-items-table tbody tr').each(function() {
                    let existingProductId = $(this).find('input[name^="purchase_items"][name$="[product_id]"]').val();
                    if (existingProductId == productId) {
                        exists = true;
                        toastr.warning('This product has already been added.');
                        return false; // Break the loop
                    }
                });

                if (exists) return;

                let rowCount = $('#purchase-items-table tbody tr').length;
                let productImage = productData.image_url ? productData.image_url : null; // Set to null if no image
                let productName = productData.text;

                let imageCellContent = productImage 
                    ? `<img src="${productImage}" alt="${productName}" class="img-thumbnail product-thumb" data-image_url="${productImage}" style="width: 50px; height: 50px; cursor: pointer;">`
                    : `<span>N/A</span>`;

                // Generate Location Options from Blade
                let locationOptions = '<option value="">Select Location</option>';
                @foreach($locations as $location)
                    locationOptions += `<option value="{{ $location->id }}">{{ $location->name }}</option>`;
                @endforeach

                let newRow = `
                    <tr data-product-image="${productImage}">
                        <td>
                            ${imageCellContent}
                        </td>
                        <td>
                            <strong>${productName}</strong>
                            <input type="hidden" name="purchase_items[${purchaseItemIndex}][product_id]" value="${productId}">
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="date" name="purchase_items[${purchaseItemIndex}][expiry_date]" class="form-control" placeholder="Select Date">
                            </div>
                        </td>
                        <td>
                            <select name="purchase_items[${purchaseItemIndex}][location_id]" class="form-control location-select" required>
                                ${locationOptions}
                            </select>
                            <div class="invalid-feedback"></div>
                        </td>
                        <td>
                            <input type="text" name="purchase_items[${purchaseItemIndex}][batch_no]" class="form-control" required>
                            <div class="invalid-feedback"></div>
                        </td>
                        <td>
                            <input type="number" name="purchase_items[${purchaseItemIndex}][quantity]" class="form-control qty" required min="1" value="1">
                            <div class="invalid-feedback"></div>
                        </td>
                        <td>
                            <input type="number" step="0.01" name="purchase_items[${purchaseItemIndex}][purchase_price]" 
                                   class="form-control purchase_price" required>
                        </td>
                        <td>
                            <input type="number" step="0.01" name="purchase_items[${purchaseItemIndex}][sale_price]" 
                                   class="form-control sale_price" required>
                        </td>
                        <td>
                            <input type="number" step="0.01" name="purchase_items[${purchaseItemIndex}][total_amount]" class="form-control total_amount" value="0.00" readonly>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-purchase-item">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#purchase-items-table tbody').append(newRow);
                purchaseItemIndex++;

                // Initialize Select2 for the new Location dropdown
                initializeSelect2(`#purchase-items-table tbody tr:last .location-select`);

                updateTotals();
            });

            // Remove Purchase Item
            $('#purchase-items-table').on('click', '.remove-purchase-item', function() {
                $(this).closest('tr').remove();
                updateTotals();
            });

            // Calculate Total per Product
            $('#purchase-items-table').on('input', '.qty, .purchase_price', function() {
                let row = $(this).closest('tr');
                let qty = parseFloat(row.find('.qty').val()) || 0;
                let purchase_price = parseFloat(row.find('.purchase_price').val()) || 0;
                let total = qty * purchase_price;
                row.find('.total_amount').val(total.toFixed(2));
                updateTotals();
            });


            // Update net amount when discount_amount changes
            $('#discount_amount').on('input', function() {
                let discount = parseFloat($(this).val()) || 0;
                let totalAmount = parseFloat($('#total_amount').val()) || 0;
                let netAmount = totalAmount - discount;
                $('#net_amount').val(netAmount.toFixed(2));
                updatePaymentTotals();
            });

            // Payment Methods Functionality

            // Function to add a new payment method row
            function addPaymentMethodRow() {
                let paymentMethods = @json($paymentMethods);
                let options = '<option value="">Select Payment Method</option>';
                paymentMethods.forEach(function(method) {
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
                            <label class="form-label">Amount ($)</label>
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

            // Add Payment Method on Click
            $('#add_payment_method_btn').click(function() {
                addPaymentMethodRow();
            });

            // Remove Payment Method on Click
            $('#payment-methods-container').on('click', '.remove-payment-method', function() {
                $(this).closest('.payment-method-row').remove();
                updatePaymentTotals();
                toggleRemoveAllButton();
            });

            // Remove All Payment Methods on Click
            $('#remove_all_payment_methods_btn').click(function() {
                $('#payment-methods-container').empty();
                $('#total_payment_amount').val('0.00');
                $('#payment-methods-container-wrapper').removeClass('error-shadow');
                $(this).hide();
                toastr.info('All payment methods have been removed.');
            });

            // Show/Hide Amount Field based on Payment Method Selection
            $('#payment-methods-container').on('change', '.payment-method-select', function() {
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

            // Function to toggle the "Remove All" button visibility
            function toggleRemoveAllButton() {
                if ($('#payment-methods-container .payment-method-row').length > 0) {
                    $('#remove_all_payment_methods_btn').show();
                } else {
                    $('#remove_all_payment_methods_btn').hide();
                }
            }

            // Function to update payment totals
            function updatePaymentTotals() {
                let totalPayment = 0;
                $('.payment-amount:visible').each(function() {
                    let val = parseFloat($(this).val()) || 0;
                    totalPayment += val;
                });

                let netAmount = parseFloat($('#net_amount').val()) || 0;

                $('#total_payment_amount').val(totalPayment.toFixed(2));

                // Remove previous error shadow
                $('#payment-methods-container-wrapper').removeClass('error-shadow');

                // Apply error shadow only if payment methods are provided and do not match net amount
                if (totalPayment > 0 && totalPayment !== netAmount) {
                    $('#payment-methods-container-wrapper').addClass('error-shadow');
                }
            }

            // Trigger update when payment amounts change
            $('#payment-methods-container').on('input', '.payment-amount', function() {
                updatePaymentTotals();
            });

            // Form Submission via AJAX
            $('#purchase-form').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission

                // Clear previous errors
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#payment-methods-container-wrapper').removeClass('error-shadow');

                // Calculate total payment
                let totalPayment = 0;
                $('.payment-amount:visible').each(function() {
                    let val = parseFloat($(this).val()) || 0;
                    totalPayment += val;
                });

                // Get net amount
                let netAmount = parseFloat($('#net_amount').val()) || 0;

                // Validation: If payment methods are provided, their total should match net amount
                // Uncomment the following block if you want to enforce matching totals
                /*
                if (totalPayment > 0 && totalPayment !== netAmount) {
                    $('#payment-methods-container-wrapper').addClass('error-shadow');
                    toastr.error('Total payment amount must equal the net amount.');
                    return;
                }
                */

                // Proceed with AJAX form submission
                let formData = $(this).serialize();

                $.ajax({
                    type: 'POST',
                    url: '{{ route("purchases.store") }}',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            window.location.href = response.redirect;
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorMessages = [];

                            // Iterate over errors and display them
                            $.each(errors, function(key, messages) {
                                if (key.startsWith('purchase_items')) {
                                    // Handle purchase items errors
                                    let inputName = key;
                                    let input = $('[name="' + inputName + '"]');
                                    input.addClass('is-invalid');
                                    input.next('.invalid-feedback').text(messages[0]);
                                } else if (key.startsWith('payment_methods')) {
                                    // Handle payment methods errors
                                    if (key.endsWith('payment_method_id')) {
                                        $('.payment-method-select').addClass('is-invalid');
                                        $('.payment-method-select').each(function() {
                                            if ($(this).val() === '') {
                                                $(this).addClass('is-invalid');
                                                $(this).next('.invalid-feedback').text(messages[0]);
                                            }
                                        });
                                    } else if (key.endsWith('amount')) {
                                        $('.payment-amount').addClass('is-invalid');
                                        $('.payment-amount').each(function() {
                                            if ($(this).val() === '' || parseFloat($(this).val()) <= 0) {
                                                $(this).addClass('is-invalid');
                                                $(this).next('.invalid-feedback').text(messages[0]);
                                            }
                                        });
                                    } else {
                                        // General payment methods errors
                                        $('#payment-methods-container-wrapper').addClass('error-shadow');
                                        toastr.error(messages[0]);
                                    }
                                } else if (key === 'location_id') {
                                    // This may not be necessary since location_id is now per purchase item
                                    // However, if there are global location_id errors, handle them here
                                    $('#location_id').addClass('is-invalid');
                                    $('#location_id_feedback').text(messages[0]);
                                } else {
                                    // Handle other errors
                                    let input = $('[name="' + key + '"]');
                                    input.addClass('is-invalid');
                                    input.next('.invalid-feedback').text(messages[0]);
                                    errorMessages.push(messages[0]);
                                }
                            });

                            if (errorMessages.length > 0) {
                                toastr.error(errorMessages.join('<br>'));
                            } else {
                                toastr.error('Please fix the errors and try again.');
                            }
                        } else {
                            toastr.error('An unexpected error occurred.');
                        }
                    }
                });
            });

            // Handle product thumbnail click to show modal
            $('#purchase-items-table').on('click', '.product-thumb', function() {
                let imageUrl = $(this).data('image_url');
                if (imageUrl) {
                    $('#modalProductImage').attr('src', imageUrl).removeClass('d-none');
                    $('#modalNoImage').addClass('d-none');
                } else {
                    $('#modalProductImage').addClass('d-none');
                    $('#modalNoImage').removeClass('d-none');
                }
                var modal = new bootstrap.Modal(document.getElementById('productImageModal'), {
                    keyboard: false
                });
                modal.show();
            });
        });

        // Function to trigger the date picker for expiry date
        function triggerExpiryDatePicker(element) {
            $(element).closest('.input-group').find('input[type="date"]').focus();
        }
    </script> 
@endpush
