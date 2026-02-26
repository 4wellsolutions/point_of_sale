@extends('layouts.app')

@section('title', 'Create Order Booking')

@section('content_header')
    <h1>Create Order Booking</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form id="booking-form" action="{{ route('bookings.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <!-- Customer Selection -->
                    <div class="col-md-6">
                        <label for="customer_id" class="form-label">Customer <span class="text-danger">*</span></label>
                        <select name="customer_id" id="customer_id" class="form-control" required>
                        </select>
                    </div>

                    <!-- Status Selection -->
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="pending" selected>Pending</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <!-- Booking Date -->
                    <div class="col-md-3">
                        <label for="booking_date" class="form-label">Booking Date <span class="text-danger">*</span></label>
                        <input type="date" name="booking_date" id="booking_date" class="form-control" required
                            value="{{ now()->format('Y-m-d') }}">
                    </div>
                </div>

                <!-- Product Selection -->
                <div class="mb-4">
                    <h5>Products</h5>
                    <div class="row g-1">
                        <div class="col-11">
                            <select id="product_select" class="form-select" aria-label="Select Product">
                                <option value="">Select Product...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-1">
                            <button type="button" class="btn btn-primary w-100" id="add_product_btn">
                                <i class="fas fa-plus"></i> Add
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Booking Items Grid -->
                <div class="mb-4">
                    <h5>Order Items</h5>
                    <table class="table table-bordered" id="booking-items-table">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th width="15%">Available Stock</th>
                                <th width="15%">Unit Price ({{ setting('currency_symbol', '$') }})</th>
                                <th width="15%">Quantity</th>
                                <th width="15%">Subtotal ({{ setting('currency_symbol', '$') }})</th>
                                <th width="5%" class="text-center"><i class="fas fa-trash"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dynamic rows will be appended here -->
                        </tbody>
                    </table>
                </div>

                <!-- Totals, Discount, Net Amount -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <!-- Notes area -->
                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes" rows="4" class="form-control"
                                placeholder="Any additional details or terms..."></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th>Total Amount ({{ setting('currency_symbol', '$') }}):</th>
                                <td><input type="number" step="0.01" name="total_amount" id="total_amount"
                                        class="form-control text-end" value="0.00" readonly></td>
                            </tr>
                            <tr>
                                <th>Discount ({{ setting('currency_symbol', '$') }}):</th>
                                <td>
                                    <input type="number" step="0.01" name="discount_amount" id="discount_amount"
                                        class="form-control text-end" value="0.00" min="0">
                                </td>
                            </tr>
                            <tr>
                                <th>Net Amount ({{ setting('currency_symbol', '$') }}):</th>
                                <td><input type="number" step="0.01" name="net_amount" id="net_amount"
                                        class="form-control text-end fw-bold" value="0.00" readonly></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="text-end">
                    <a href="{{ route('bookings.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-success" id="submit_btn">
                        <i class="fas fa-save"></i> Save Booking
                    </button>
                </div>
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

        .error-border {
            border: 1px solid red !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function () {
            // Initialize Select2 for Customer Selection via AJAX
            $('#customer_id').select2({
                placeholder: 'Search for a customer...',
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

            // Initialize Select2 for Product Selection
            $('#product_select').select2({
                placeholder: 'Search and select product...',
                allowClear: true,
                width: '100%'
            });

            let itemIndex = 0;

            function addProduct() {
                let productId = $('#product_select').val();

                if (!productId) {
                    toastr.warning('Please select a product first.');
                    return;
                }

                if ($('#booking-items-table tbody tr[data-product-id="' + productId + '"]').length > 0) {
                    toastr.error('This product is already added to the booking.');
                    $('#product_select').val(null).trigger('change');
                    return;
                }

                // Fetch product details (price + actual available stock)
                $.ajax({
                    url: '/bookings/product/' + productId,
                    type: 'GET',
                    success: function (data) {
                        let newRow = `
                                <tr data-product-id="${data.id}">
                                    <td>
                                        ${data.name}
                                        <input type="hidden" name="product_id[${itemIndex}]" value="${data.id}">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control available-qty bg-light" readonly value="${data.available_stock}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="unit_price[${itemIndex}]" class="form-control unit-price" required min="0" value="${data.price}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="quantity[${itemIndex}]" class="form-control qty" required min="0.01" value="1">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control item-total bg-light" value="${data.price}" readonly>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm remove-item">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;

                        $('#booking-items-table tbody').append(newRow);
                        itemIndex++;
                        $('#product_select').val(null).trigger('change');
                        updateTotals();
                    },
                    error: function () {
                        toastr.error('Failed to fetch product details.');
                    }
                });
            }

            $('#add_product_btn').click(addProduct);

            // Removing item
            $('#booking-items-table').on('click', '.remove-item', function () {
                $(this).closest('tr').remove();
                updateTotals();
            });

            // Calculate Item Total
            $('#booking-items-table').on('input', '.qty, .unit-price', function () {
                let row = $(this).closest('tr');
                let qty = parseFloat(row.find('.qty').val()) || 0;
                let price = parseFloat(row.find('.unit-price').val()) || 0;
                let total = qty * price;
                row.find('.item-total').val(total.toFixed(2).replace(/\.00$/, '').replace(/(\.\d)0$/, '$1'));

                // Show warning if booking more than available (optional, since it's just a booking)
                let available = parseFloat(row.find('.available-qty').val()) || 0;
                if (qty > available) {
                    row.find('.qty').addClass('border-warning');
                } else {
                    row.find('.qty').removeClass('border-warning');
                }

                updateTotals();
            });

            // Update Global Totals
            function updateTotals() {
                let total = 0;
                $('.item-total').each(function () {
                    total += parseFloat($(this).val()) || 0;
                });

                let discount = parseFloat($('#discount_amount').val()) || 0;
                let net = total - discount;

                $('#total_amount').val(total.toFixed(2).replace(/\.00$/, '').replace(/(\.\d)0$/, '$1'));
                $('#net_amount').val(net.toFixed(2).replace(/\.00$/, '').replace(/(\.\d)0$/, '$1'));
            }

            $('#discount_amount').on('input', updateTotals);

            // AJAX Submit
            $('#booking-form').submit(function (e) {
                e.preventDefault();

                if ($('#booking-items-table tbody tr').length === 0) {
                    toastr.error('Please add at least one product to the booking.');
                    return;
                }

                let btn = $('#submit_btn');
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (res) {
                        if (res.success) {
                            toastr.success(res.message);
                            window.location.href = res.redirect;
                        }
                    },
                    error: function (xhr) {
                        btn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Booking');
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            for (let key in errors) {
                                toastr.error(errors[key][0]);
                            }
                        } else {
                            toastr.error(xhr.responseJSON?.message || 'An error occurred.');
                        }
                    }
                });
            });
        });
    </script>
@endpush