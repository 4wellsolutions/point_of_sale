@extends('layouts.app')

@section('title', 'Edit Purchase')

@section('page_title', 'Edit Purchase')

@section('content')
    <form id="purchase-form">
        @csrf
        @method('PUT')

        {{-- ─── SECTION 1: Purchase Info ─── --}}
        <div class="card purchase-card mb-4">
            <div class="card-header purchase-card-header">
                <i class="fas fa-file-invoice me-2"></i>Purchase Information
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label for="vendor_id" class="form-label fw-semibold"><i
                                class="fas fa-store me-1 text-muted"></i>Vendor</label>
                        <select name="vendor_id" id="vendor_id"
                            class="form-control @error('vendor_id') is-invalid @enderror" required>
                        </select>
                        <div id="vendor_id_feedback" class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-4">
                        <label for="invoice_no" class="form-label fw-semibold"><i
                                class="fas fa-hashtag me-1 text-muted"></i>Invoice No.</label>
                        <div class="input-group">
                            <input type="text" name="invoice_no" value="{{ $purchase->invoice_no }}" id="invoice_no"
                                class="form-control" readonly style="background:#f8f9fa;">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="purchase_date" class="form-label fw-semibold"><i
                                class="fas fa-calendar-alt me-1 text-muted"></i>Purchase Date <span
                                class="text-danger">*</span></label>
                        <input type="date" name="purchase_date" id="purchase_date"
                            class="form-control @error('purchase_date') is-invalid @enderror" required
                            value="{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('Y-m-d') }}">
                        <div id="purchase_date_feedback" class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── SECTION 2: Product Selection ─── --}}
        <div class="card purchase-card mb-4">
            <div class="card-header purchase-card-header">
                <i class="fas fa-box-open me-2"></i>Add Products <span class="text-danger">*</span>
            </div>
            <div class="card-body">
                <div class="row g-2 align-items-end">
                    <div class="col">
                        <select id="product_select" class="form-select" aria-label="Select Product">
                            <option value="">Search & select a product...</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-primary px-4" id="add_product_btn" style="height:38px;">
                            <i class="fas fa-plus me-1"></i> Add
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── SECTION 3: Purchase Items Table ─── --}}
        <div class="card purchase-card mb-4">
            <div class="card-header purchase-card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-list-alt me-2"></i>Purchase Items</span>
                <span class="badge bg-primary" id="itemCountBadge">0 items</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-hover mb-0" id="purchase-items-table">
                        <thead>
                            <tr>
                                <th style="width:6%;">Image</th>
                                <th style="width:16%;">Product Name</th>
                                <th style="width:10%;">Expiry Date</th>
                                <th style="width:12%;">Location <span class="text-danger">*</span></th>
                                <th style="width:10%;">Batch No <span class="text-danger">*</span></th>
                                <th style="width:9%;">Qty <span class="text-danger">*</span></th>
                                <th style="width:13%;" title="Price Per Piece">Cost Price <span class="text-danger">*</span>
                                </th>
                                <th style="width:12%;">Total</th>
                                <th style="width:5%;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Existing items will be pre-populated via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ─── SECTION 4: Totals ─── --}}
        <div class="row mb-4">
            <div class="col-md-5 ms-auto">
                <div class="card purchase-card">
                    <div class="card-header purchase-card-header">
                        <i class="fas fa-calculator me-2"></i>Order Summary
                    </div>
                    <div class="card-body p-0">
                        <table class="table mb-0 summary-table">
                            <tr>
                                <td class="fw-semibold text-muted">Total Amount</td>
                                <td><input type="number" step="0.01" name="total_amount" id="total_amount"
                                        class="form-control form-control-sm text-end" readonly style="background:#f8f9fa;">
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted">Discount</td>
                                <td>
                                    <input type="number" step="0.01" name="discount_amount" id="discount_amount"
                                        class="form-control form-control-sm text-end @error('discount_amount') is-invalid @enderror"
                                        value="{{ old('discount_amount', $purchase->discount_amount ?? 0) }}" min="0">
                                    @error('discount_amount')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                            <tr class="table-active">
                                <td class="fw-bold">Net Amount</td>
                                <td><input type="number" step="0.01" name="net_amount" id="net_amount"
                                        class="form-control form-control-sm text-end fw-bold" readonly
                                        style="background:#e8f5e9; border-color:#4caf50;"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── SECTION 5: Payment Methods ─── --}}
        <div class="card purchase-card mb-4" id="payment-methods-container-wrapper">
            <div class="card-header purchase-card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-credit-card me-2"></i>Payment Methods</span>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-success" id="add_payment_method_btn">
                        <i class="fas fa-plus me-1"></i> Add Payment
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger ms-1" id="remove_all_payment_methods_btn"
                        style="display: none;">
                        <i class="fas fa-trash me-1"></i> Remove All
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="payment-methods-container">
                    <!-- Payment Method Rows -->
                </div>
                <div class="text-center text-muted py-3" id="no-payments-msg">
                    <i class="fas fa-info-circle me-1"></i> No payment methods added yet. Click "Add Payment" to begin.
                </div>
            </div>
        </div>

        {{-- ─── Total Payment ─── --}}
        <div class="row mb-4">
            <div class="col-md-5 ms-auto">
                <div class="card purchase-card">
                    <div class="card-body p-0">
                        <table class="table mb-0 summary-table">
                            <tr class="table-active">
                                <td class="fw-bold">Total Payment</td>
                                <td><input type="number" step="0.01" id="total_payment_amount"
                                        class="form-control form-control-sm text-end fw-bold" readonly
                                        style="background:#e3f2fd; border-color:#2196f3;"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── SECTION 6: Notes ─── --}}
        <div class="card purchase-card mb-4">
            <div class="card-header purchase-card-header">
                <i class="fas fa-sticky-note me-2"></i>Notes
            </div>
            <div class="card-body">
                <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"
                    placeholder="Add any notes about this purchase...">{{ old('notes', $purchase->notes) }}</textarea>
                <div id="notes_feedback" class="invalid-feedback"></div>
            </div>
        </div>

        {{-- ─── Submit ─── --}}
        <div class="d-flex gap-2 mb-4">
            <button type="submit" class="btn btn-success btn-lg px-4" id="btnSavePurchase">
                <i class="fas fa-save me-2"></i>Update Purchase
            </button>
            <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </form>

    <!-- Image Modal -->
    <div class="modal fade" id="productImageModal" tabindex="-1" aria-labelledby="productImageModalLabel"
        aria-hidden="true">
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
    <style type="text/css">
        /* ── Card Styling ── */
        .purchase-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .purchase-card-header {
            background: linear-gradient(135deg, #2c3e50 0%, #3a536b 100%);
            color: #fff;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 12px 20px;
            border: none;
        }

        .purchase-card .card-body {
            padding: 20px;
        }

        /* ── Select2 Matching ── */
        .select2-container--default .select2-selection--single {
            height: 38px !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.375rem !important;
            padding: 4px 8px !important;
            background-color: #fff !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
            color: #212529 !important;
            font-size: 1rem !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #6c757d !important;
        }

        /* ── Table Styling ── */
        #purchase-items-table thead th {
            background: linear-gradient(135deg, #34495e 0%, #4a6a85 100%);
            color: #fff;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 8px;
            border: none;
            vertical-align: middle;
            white-space: nowrap;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        #purchase-items-table tbody td {
            vertical-align: middle;
            padding: 8px;
            font-size: 0.9rem;
            border-color: #eee;
        }

        #purchase-items-table tbody tr:hover {
            background: #f0f7ff;
        }

        #purchase-items-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* ── Summary Table ── */
        .summary-table td {
            padding: 10px 16px;
            vertical-align: middle;
            border-color: #eee;
        }

        .summary-table td:first-child {
            width: 55%;
        }

        .summary-table input.form-control-sm {
            font-size: 0.95rem;
        }

        /* ── Product Thumb ── */
        .product-thumb {
            border-radius: 6px;
            transition: transform 0.2s;
            border: 2px solid #e9ecef;
        }

        .product-thumb:hover {
            transform: scale(1.1);
            border-color: #6c63ff;
        }

        /* ── Select2 with images ── */
        .select2-results__option img,
        .select2-selection__rendered img {
            width: 30px;
            height: 30px;
            margin-right: 10px;
            border-radius: 5px;
            vertical-align: middle;
        }

        /* ── Payment Methods ── */
        .payment-method-row {
            margin-bottom: 12px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            transition: box-shadow 0.2s;
        }

        .payment-method-row:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .payment-method-select {
            width: 100%;
        }

        /* ── Error Shadow ── */
        .error-shadow {
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.25) !important;
            border-color: #dc3545 !important;
        }

        /* ── Misc ── */
        .cursor-pointer {
            cursor: pointer;
        }

        .form-label {
            font-size: 0.875rem;
            margin-bottom: 4px;
        }

        .badge {
            font-size: 0.8rem;
        }

        /* ── Remove button style in table ── */
        #purchase-items-table .btn-danger {
            border-radius: 50%;
            width: 30px;
            height: 30px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ── Item count badge ── */
        #itemCountBadge {
            background: rgba(255, 255, 255, 0.2) !important;
            font-weight: 500;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function () {
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
                            q: params.term,
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

            // Pre-select existing vendor
            @if($purchase->vendor)
                var vendorOption = new Option('{{ $purchase->vendor->name }}', '{{ $purchase->vendor_id }}', true, true);
                $('#vendor_id').append(vendorOption).trigger('change');
            @endif

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
                            q: params.term,
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
                templateResult: formatProductOption,
                templateSelection: formatProductSelection
            });

            function formatProductOption(product) {
                if (product.loading) return product.text;
                if (product.image_url) {
                    return $("<div class='d-flex align-items-center'><img src='" + product.image_url + "' class='img-thumbnail' style='width:30px; height:30px; margin-right:10px;' />" + product.text + "</div>");
                } else {
                    return $("<div class='d-flex align-items-center'><span>N/A</span> " + product.text + "</div>");
                }
            }

            function formatProductSelection(product) {
                if (!product.id) return product.text;
                if (product.image_url) {
                    return $("<div class='d-flex align-items-center'><img src='" + product.image_url + "' class='img-thumbnail' style='width:30px; height:30px; margin-right:10px;' />" + product.text + "</div>");
                } else {
                    return $("<div class='d-flex align-items-center'><span>N/A</span> " + product.text + "</div>");
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
                $('.total_amount').each(function () {
                    let val = parseFloat($(this).val()) || 0;
                    totalAmount += val;
                });

                let discount = parseFloat($('#discount_amount').val()) || 0;
                let netAmount = totalAmount - discount;

                $('#total_amount').val(totalAmount.toFixed(2));
                $('#net_amount').val(netAmount.toFixed(2));

                updatePaymentTotals();
            }

            // Initialize Purchase Items and Payment Methods indices
            let purchaseItemIndex = 0;
            let paymentMethodIndex = 0;

            // Generate Location Options from Blade
            function getLocationOptions(selectedId) {
                let options = '<option value="">Select Location</option>';
                @foreach($locations as $location)
                    options += `<option value="{{ $location->id }}" ${selectedId == {{ $location->id }} ? 'selected' : ''}>{{ $location->name }}</option>`;
                @endforeach
                return options;
            }

            // Add a row to the purchase items table
            function addItemRow(productId, productName, productImage, expiryDate, locationId, batchNo, qty, purchasePrice) {
                let imageCellContent = productImage
                    ? `<img src="${productImage}" alt="${productName}" class="img-thumbnail product-thumb" data-image_url="${productImage}" style="width: 50px; height: 50px; cursor: pointer;">`
                    : `<span>N/A</span>`;

                let totalAmount = (qty * purchasePrice).toFixed(2);
                let expiryVal = expiryDate || '';

                let newRow = `
                    <tr data-product-image="${productImage}">
                        <td>${imageCellContent}</td>
                        <td>
                            <strong>${productName}</strong>
                            <input type="hidden" name="purchase_items[${purchaseItemIndex}][product_id]" value="${productId}">
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="date" name="purchase_items[${purchaseItemIndex}][expiry_date]" class="form-control" value="${expiryVal}">
                            </div>
                        </td>
                        <td>
                            <select name="purchase_items[${purchaseItemIndex}][location_id]" class="form-control location-select" required>
                                ${getLocationOptions(locationId)}
                            </select>
                            <div class="invalid-feedback"></div>
                        </td>
                        <td>
                            <input type="text" name="purchase_items[${purchaseItemIndex}][batch_no]" class="form-control" required value="${batchNo || ''}">
                            <div class="invalid-feedback"></div>
                        </td>
                        <td>
                            <input type="number" name="purchase_items[${purchaseItemIndex}][quantity]" class="form-control qty" required min="1" value="${qty || 1}">
                            <div class="invalid-feedback"></div>
                        </td>
                        <td>
                            <input type="number" step="0.01" name="purchase_items[${purchaseItemIndex}][purchase_price]"
                                   class="form-control purchase_price" required value="${purchasePrice || ''}">
                        </td>
                        <td>
                            <input type="number" step="0.01" name="purchase_items[${purchaseItemIndex}][total_amount]" class="form-control total_amount" value="${totalAmount}" readonly>
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
                initializeSelect2(`#purchase-items-table tbody tr:last .location-select`);
                updateTotals();
                updateItemCount();
            }

            // ═══ PRE-POPULATE EXISTING ITEMS ═══
            @foreach($purchase->purchaseItems as $item)
                addItemRow(
                    '{{ $item->product_id }}',
                    @json($item->product->name),
                    @json($item->product->image_url ? asset($item->product->image_url) : ''),
                    '{{ $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format("Y-m-d") : "" }}',
                    '{{ $item->location_id }}',
                    @json($item->batch_no),
                    {{ $item->quantity }},
                    {{ $item->purchase_price }}
                );
            @endforeach

            // ═══ PRE-POPULATE EXISTING PAYMENTS ═══
            @foreach($purchase->transactions as $transaction)
                addPaymentMethodRow('{{ $transaction->payment_method_id }}', {{ $transaction->amount }});
            @endforeach

            // Add Product to Purchase Items (new products)
            $('#add_product_btn').click(function () {
                let productId = $('#product_select').val();
                let productData = $('#product_select').select2('data')[0];

                if (!productId) {
                    toastr.warning('Please select a product.');
                    return;
                }

                let productImage = productData.image_url ? productData.image_url : '';
                let productName = productData.text;

                addItemRow(productId, productName, productImage, '', '', '', 1, '');

                // Clear the product selector
                $('#product_select').val(null).trigger('change');
            });

            // Remove Purchase Item
            $('#purchase-items-table').on('click', '.remove-purchase-item', function () {
                $(this).closest('tr').remove();
                updateTotals();
                updateItemCount();
            });

            // Calculate Total per Product
            $('#purchase-items-table').on('input', '.qty, .purchase_price', function () {
                let row = $(this).closest('tr');
                let qty = parseFloat(row.find('.qty').val()) || 0;
                let purchase_price = parseFloat(row.find('.purchase_price').val()) || 0;
                let total = qty * purchase_price;
                row.find('.total_amount').val(total.toFixed(2));
                updateTotals();
            });

            // Update net amount when discount_amount changes
            $('#discount_amount').on('input', function () {
                let discount = parseFloat($(this).val()) || 0;
                let totalAmount = parseFloat($('#total_amount').val()) || 0;
                let netAmount = totalAmount - discount;
                $('#net_amount').val(netAmount.toFixed(2));
                updatePaymentTotals();
            });

            // ═══ PAYMENT METHODS ═══

            function addPaymentMethodRow(selectedMethodId, amount) {
                let paymentMethods = @json($paymentMethods);
                let options = '<option value="">Select Payment Method</option>';
                paymentMethods.forEach(function (method) {
                    let selected = (selectedMethodId && method.id == selectedMethodId) ? 'selected' : '';
                    options += `<option value="${method.id}" ${selected}>${method.method_name}</option>`;
                });

                let amountDisplay = (selectedMethodId && amount) ? '' : 'display: none;';
                let amountValue = amount || '';

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
                            <input type="number" step="0.01" name="payment_methods[${paymentMethodIndex}][amount]" class="form-control payment-amount" min="0.01" style="${amountDisplay}" value="${amountValue}">
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
                toggleNoPaymentsMsg();
                updatePaymentTotals();
            }

            $('#add_payment_method_btn').click(function () {
                addPaymentMethodRow(null, null);
            });

            $('#payment-methods-container').on('click', '.remove-payment-method', function () {
                $(this).closest('.payment-method-row').remove();
                updatePaymentTotals();
                toggleRemoveAllButton();
                toggleNoPaymentsMsg();
            });

            $('#remove_all_payment_methods_btn').click(function () {
                $('#payment-methods-container').empty();
                $('#total_payment_amount').val('0.00');
                $('#payment-methods-container-wrapper').removeClass('error-shadow');
                $(this).hide();
                toggleNoPaymentsMsg();
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

            function toggleNoPaymentsMsg() {
                if ($('#payment-methods-container .payment-method-row').length > 0) {
                    $('#no-payments-msg').hide();
                } else {
                    $('#no-payments-msg').show();
                }
            }

            function updateItemCount() {
                let count = $('#purchase-items-table tbody tr').length;
                $('#itemCountBadge').text(count + (count === 1 ? ' item' : ' items'));
            }

            function updatePaymentTotals() {
                let totalPayment = 0;
                $('.payment-amount:visible').each(function () {
                    let val = parseFloat($(this).val()) || 0;
                    totalPayment += val;
                });

                let netAmount = parseFloat($('#net_amount').val()) || 0;
                $('#total_payment_amount').val(totalPayment.toFixed(2));

                $('#payment-methods-container-wrapper').removeClass('error-shadow');
                if (totalPayment > 0 && totalPayment !== netAmount) {
                    $('#payment-methods-container-wrapper').addClass('error-shadow');
                }
            }

            $('#payment-methods-container').on('input', '.payment-amount', function () {
                updatePaymentTotals();
            });

            // ═══ FORM SUBMISSION VIA AJAX (PUT) ═══
            $('#purchase-form').on('submit', function (e) {
                e.preventDefault();

                // Clear previous errors
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#payment-methods-container-wrapper').removeClass('error-shadow');

                let formData = $(this).serialize();

                $.ajax({
                    type: 'PUT',
                    url: '{{ route("purchases.update", $purchase->id) }}',
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                            window.location.href = response.redirect;
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorMessages = [];

                            $.each(errors, function (key, messages) {
                                if (key.startsWith('purchase_items')) {
                                    let input = $('[name="' + key + '"]');
                                    input.addClass('is-invalid');
                                    input.next('.invalid-feedback').text(messages[0]);
                                } else if (key.startsWith('payment_methods')) {
                                    if (key.endsWith('payment_method_id')) {
                                        $('.payment-method-select').each(function () {
                                            if ($(this).val() === '') {
                                                $(this).addClass('is-invalid');
                                                $(this).next('.invalid-feedback').text(messages[0]);
                                            }
                                        });
                                    } else if (key.endsWith('amount')) {
                                        $('.payment-amount').each(function () {
                                            if ($(this).val() === '' || parseFloat($(this).val()) <= 0) {
                                                $(this).addClass('is-invalid');
                                                $(this).next('.invalid-feedback').text(messages[0]);
                                            }
                                        });
                                    } else {
                                        $('#payment-methods-container-wrapper').addClass('error-shadow');
                                        toastr.error(messages[0]);
                                    }
                                } else {
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
            $('#purchase-items-table').on('click', '.product-thumb', function () {
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
    </script>
@endpush