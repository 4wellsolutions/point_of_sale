@extends('layouts.app')

@section('title', 'Add Purchase Return')

@section('page_title', 'Add Purchase Return')

@section('content')
    <div class="card">
        <div class="card-body">
            <form id="purchaseReturnForm" method="POST" action="{{ route('purchase-returns.store') }}">
                @csrf
                <!-- Error and Success Messages -->
                <div id="form-errors" class="alert alert-danger d-none">
                    <ul></ul>
                </div>
                <div id="form-success" class="alert alert-success d-none">
                    Purchase Return has been successfully added.
                </div>
                
                <!-- Invoice Number and Return Date -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="invoice_no" class="form-label">Invoice Number <span class="text-danger">*</span></label>
                        <input type="text" name="invoice_no" value="{{ $invoice_no }}" id="invoice_no" class="form-control" readonly required>
                        @error('invoice_no')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="return_date" class="form-label">Return Date <span class="text-danger">*</span></label>
                        <input type="date" name="return_date" id="return_date" class="form-control" value="{{ old('return_date', date('Y-m-d')) }}" required>
                        @error('return_date')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                
                <!-- Original Purchase and Vendor -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="purchase_id" class="form-label">Original Purchase <span class="text-danger">*</span></label>
                        <select name="purchase_id" id="purchase_id" class="form-control" required></select>
                        @error('purchase_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="vendor_name" class="form-label">Vendor</label>
                        <input type="text" name="vendor_name" id="vendor_name" class="form-control" readonly>
                        @error('vendor_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                
                <hr>
                
                <!-- Return Items Section -->
                <h5>Return Items</h5>
                <table class="table table-bordered" id="returnItemsTable">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Batch No</th>
                            <th>Quantity</th>
                            <th>Cost per Piece</th>
                            <th>Return Quantity</th>
                            <th>Return Unit Price</th>
                            <th>Total Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Return items will be dynamically added here based on the selected purchase -->
                    </tbody>
                </table>
                <!-- Removed "Add Return Item" Button -->
                
                <hr>
                
                <!-- Totals Section -->
                <div class="row mb-3">
                    <div class="col-md-4 offset-md-8">
                        <table class="table">
                            <tr>
                                <th>Total Amount:</th>
                                <td>
                                    <span id="totalAmount">0.00</span>
                                    <input type="hidden" name="total_amount" id="total_amount" value="0.00">
                                </td>
                            </tr>
                            <tr>
                                <th>Discount Amount:</th>
                                <td>
                                    <input type="number" step="0.01" name="discount_amount" id="discount_amount" class="form-control" value="{{ old('discount_amount', 0) }}">
                                    @error('discount_amount')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <th>Net Amount:</th>
                                <td>
                                    <span id="netAmount">0.00</span>
                                    <input type="hidden" name="net_amount" id="net_amount" value="0.00">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <hr>
                
                <!-- Payment Methods Section -->
                <h5>Payment Methods</h5>
                <table class="table table-bordered" id="paymentMethodsTable">
                    <thead>
                        <tr>
                            <th>Payment Method</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Payment methods will be dynamically added here -->
                    </tbody>
                </table>
                <!-- Button to Add Payment Method -->
                <button type="button" class="btn btn-secondary" id="addPaymentMethod">
                    <i class="fas fa-plus"></i> Add Payment Method
                </button>
                
                <hr>
                
                <!-- Notes Section -->
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                    @error('notes')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- Form Buttons -->
                <button class="btn btn-success" type="submit">
                    <i class="fas fa-save"></i> Save Purchase Return
                </button>
                <a href="{{ route('purchase-returns.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </form>
        </div>
    </div>

    <!-- Template for Return Item Row -->
    <template id="returnItemTemplate">
        <tr>
            <td>
                <input type="hidden" name="return_items[__INDEX__][product_id]" class="product_id" value="__PRODUCT_ID__">
                <input type="hidden" name="return_items[__INDEX__][purchase_item_id]" class="purchase_item_id" value="__PURCHASE_ITEM_ID__">
                <span class="product-name">__PRODUCT_NAME__</span>
            </td>
            <td>
                <span class="batch-no">__BATCH_NO__</span>
            </td>
            <td>
                <span class="quantity">__QUANTITY__</span>
            </td>
            <td>
                <span class="cost-per-piece">__COST_PER_PIECE__</span>
            </td>
            <td>
                <input type="number" name="return_items[__INDEX__][quantity]" class="form-control return-quantity" min="1" required>
            </td>
            <td>
                <input type="number" step="0.01" name="return_items[__INDEX__][unit_price]" class="form-control return-unit-price" min="0" required>
            </td>
            <td>
                <span class="total-amount">0.00</span>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm removeReturnItem">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    </template>

    <!-- Template for Payment Method Row -->
    <template id="paymentMethodTemplate">
        <tr>
            <td>
                <select name="payment_methods[__INDEX__][payment_method_id]" class="form-control payment-method-select" required>
                    <option value="">Select Payment Method</option>
                    @foreach(\App\Models\PaymentMethod::all() as $method)
                        <option value="{{ $method->id }}">{{ $method->method_name }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" step="0.01" name="payment_methods[__INDEX__][amount]" class="form-control payment-amount" min="0.01" required>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm removePaymentMethod">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    </template>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2 for Original Purchase with AJAX
        $('#purchase_id').select2({
            placeholder: 'Select Original Purchase',
            allowClear: true,
            minimumInputLength: 2,
            ajax: {
                url: "{{ route('purchases.search') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
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

        // Variables to keep track of indices
        let returnItemIndex = 0;
        let paymentMethodIndex = 0;

        // Function to populate return items based on selected purchase
        function populateReturnItems(items) {
            $('#returnItemsTable tbody').empty(); // Clear existing items
            returnItemIndex = 0; // Reset index

            items.forEach(function(item) {
                const template = $('#returnItemTemplate').html()
                    .replace(/__INDEX__/g, returnItemIndex)
                    .replace('__PRODUCT_ID__', item.product_id)
                    .replace('__PURCHASE_ITEM_ID__', item.id)
                    .replace('__PRODUCT_NAME__', item.product.name)
                    .replace('__BATCH_NO__', item.batch_no)
                    .replace('__QUANTITY__', item.quantity)
                    .replace('__COST_PER_PIECE__', item.unit_price);

                $('#returnItemsTable tbody').append(template);

                const newRow = $('#returnItemsTable tbody tr').last();
                newRow.find('.return-quantity').val(0); // Initial return quantity
                newRow.find('.return-unit-price').val(item.unit_price);

                returnItemIndex++;
            });

            // After populating, recalculate totals
            calculateTotals();
        }

        // Fetch and display vendor information when a purchase is selected
        $('#purchase_id').on('select2:select', function(e) {
            const purchaseId = e.params.data.id;
            let url = `/purchases/${purchaseId}`;
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#vendor_name').val(data.vendor_name);
                    populateReturnItems(data.purchase_items);
                },
                error: function() {
                    $('#vendor_name').val('');
                    $('#returnItemsTable tbody').empty();
                    calculateTotals();
                }
            });
        });

        // Clear vendor information and return items when selection is cleared
        $('#purchase_id').on('select2:clear', function() {
            $('#vendor_name').val('');
            $('#returnItemsTable tbody').empty();
            calculateTotals();
        });

        // Remove Return Item
        $('#returnItemsTable').on('click', '.removeReturnItem', function() {
            $(this).closest('tr').remove();
            calculateTotals();
        });

        // Add Payment Method
        $('#addPaymentMethod').on('click', function() {
            const template = $('#paymentMethodTemplate').html()
                .replace(/__INDEX__/g, paymentMethodIndex);
            $('#paymentMethodsTable tbody').append(template);
            paymentMethodIndex++;
        });

        // Remove Payment Method
        $('#paymentMethodsTable').on('click', '.removePaymentMethod', function() {
            $(this).closest('tr').remove();
            calculateTotals();
        });

        // Calculate total amount when quantity or unit price changes
        $('#returnItemsTable').on('input', '.return-quantity, .return-unit-price', function() {
            const row = $(this).closest('tr');
            const returnQuantity = parseFloat(row.find('.return-quantity').val()) || 0;
            const unitPrice = parseFloat(row.find('.return-unit-price').val()) || 0;
            const totalAmount = returnQuantity * unitPrice;
            row.find('.total-amount').text(totalAmount.toFixed(2));
            calculateTotals();
        });

        // Recalculate totals when discount amount changes
        $('#discount_amount').on('input', function() {
            calculateTotals();
        });

        // Recalculate totals when payment amounts change
        $('#paymentMethodsTable').on('input', '.payment-amount', function() {
            calculateTotals();
        });

        // Function to calculate total and net amounts
        function calculateTotals() {
            let totalAmount = 0;
            $('.total-amount').each(function() {
                totalAmount += parseFloat($(this).text()) || 0;
            });
            $('#totalAmount').text(totalAmount.toFixed(2));
            $('#total_amount').val(totalAmount.toFixed(2)); // Update hidden input

            const discountAmount = parseFloat($('#discount_amount').val()) || 0;
            const netAmount = totalAmount - discountAmount;
            $('#netAmount').text(netAmount.toFixed(2));
            $('#net_amount').val(netAmount.toFixed(2)); // Update hidden input

            // Optionally, validate that netAmount equals sum of payment amounts
            let totalPayments = 0;
            $('.payment-amount').each(function() {
                totalPayments += parseFloat($(this).val()) || 0;
            });

            // You can add visual cues or warnings if totalPayments !== netAmount
        }

        // AJAX Form Submission
        $('#purchaseReturnForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission

            // Clear previous errors and success messages
            $('#form-errors').addClass('d-none').find('ul').empty();
            $('#form-success').addClass('d-none');

            // Disable the submit button to prevent multiple submissions
            const submitButton = $(this).find('button[type="submit"]');
            submitButton.prop('disabled', true);

            // Collect form data
            const formData = $(this).serialize();

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    // Handle success
                    $('#form-success').removeClass('d-none');
                    // Reset the form
                    $('#purchaseReturnForm')[0].reset();
                    $('#purchase_id').val(null).trigger('change');
                    $('#returnItemsTable tbody').empty();
                    $('#paymentMethodsTable tbody').empty();
                    calculateTotals();
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        // Handle validation errors
                        const errors = xhr.responseJSON.errors;
                        const errorList = $('#form-errors ul');
                        $.each(errors, function(key, messages) {
                            messages.forEach(function(message) {
                                errorList.append('<li>' + message + '</li>');
                            });
                        });
                        $('#form-errors').removeClass('d-none');
                    } else {
                        // Handle other errors
                        alert('An unexpected error occurred. Please try again.');
                    }
                },
                complete: function() {
                    // Re-enable the submit button
                    submitButton.prop('disabled', false);
                }
            });
        });
    });
</script>
@endpush

@push("styles")
<style>
    .select2-container--default .select2-selection--single {
        height: 38px !important;
    }
</style>
@endpush
