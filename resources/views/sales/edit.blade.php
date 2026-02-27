@extends('layouts.app')

@section('title', 'Edit Sale')
@section('page_title', 'Edit Sale — Invoice #{{ $sale->invoice_no }}')

@section('content')
    <form id="sale-form">
        @csrf
        @method('PUT')

        {{-- ─── SECTION 1: Sale Information ─── --}}
        <div class="card sale-card mb-4">
            <div class="card-header sale-card-header">
                <i class="fas fa-file-invoice me-2"></i>Sale Information
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label for="customer_id" class="form-label fw-semibold"><i
                                class="fas fa-user me-1 text-muted"></i>Customer <span class="text-danger">*</span></label>
                        <select name="customer_id" id="customer_id" class="form-control" required></select>
                        <div id="customer_id_feedback" class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-4">
                        <label for="invoice_no" class="form-label fw-semibold"><i
                                class="fas fa-hashtag me-1 text-muted"></i>Invoice No.</label>
                        <input type="text" value="{{ $sale->invoice_no }}" id="invoice_no" class="form-control" readonly
                            style="background:#f8f9fa;">
                    </div>
                    <div class="col-md-3">
                        <label for="sale_date" class="form-label fw-semibold"><i
                                class="fas fa-calendar-alt me-1 text-muted"></i>Sale Date <span
                                class="text-danger">*</span></label>
                        <input type="date" name="sale_date" id="sale_date" class="form-control" required
                            value="{{ \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d') }}">
                        <div id="sale_date_feedback" class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── SECTION 2: Product Selection ─── --}}
        <div class="card sale-card mb-4">
            <div class="card-header sale-card-header">
                <i class="fas fa-box-open me-2"></i>Add Products <span class="text-danger">*</span>
            </div>
            <div class="card-body">
                <div class="row g-2 align-items-end">
                    <div class="col">
                        <select id="product_select" class="form-select" aria-label="Select Product">
                            <option value="">Search &amp; select a product...</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-primary px-4" id="add_product_btn" style="height:38px;">
                            <i class="fas fa-plus me-1"></i>Add
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── SECTION 3: Sale Items Table ─── --}}
        <div class="card sale-card mb-4">
            <div class="card-header sale-card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-list-alt me-2"></i>Sale Items</span>
                <span class="badge bg-primary" id="itemCountBadge">0 items</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height:350px; overflow-y:auto;">
                    <table class="table table-hover table-sm mb-0" id="sale-items-table" style="font-size:0.8rem;">
                        <thead>
                            <tr>
                                <th style="width:4%;">Img</th>
                                <th style="width:16%;">Product</th>
                                <th style="width:10%;">Batch <span class="text-danger">*</span></th>
                                <th style="width:12%;">Location <span class="text-danger">*</span></th>
                                <th style="width:6%;">Avail</th>
                                <th style="width:9%;">Cost</th>
                                <th style="width:9%;">Price <span class="text-danger">*</span></th>
                                <th style="width:8%;">Disc</th>
                                <th style="width:6%;">Qty <span class="text-danger">*</span></th>
                                <th style="width:10%;">Total</th>
                                <th style="width:4%;"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ─── SECTION 4: Totals ─── --}}
        <div class="row mb-4">
            <div class="col-md-5 ms-auto">
                <div class="card sale-card">
                    <div class="card-header sale-card-header"><i class="fas fa-calculator me-2"></i>Order Summary</div>
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
                                        class="form-control form-control-sm text-end"
                                        value="{{ old('discount_amount', $sale->discount_amount ?? 0) }}" min="0">
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
        <div class="card sale-card mb-4" id="payment-methods-container-wrapper">
            <div class="card-header sale-card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-credit-card me-2"></i>Payment Methods</span>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-success" id="add_payment_method_btn">
                        <i class="fas fa-plus me-1"></i>Add Payment
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger ms-1" id="remove_all_payment_methods_btn"
                        style="display:none;">
                        <i class="fas fa-trash me-1"></i>Remove All
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="payment-methods-container"></div>
                <div class="text-center text-muted py-3" id="no-payments-msg">
                    <i class="fas fa-info-circle me-1"></i>No payment methods added yet.
                </div>
            </div>
        </div>

        {{-- ─── Total Payment ─── --}}
        <div class="row mb-4">
            <div class="col-md-5 ms-auto">
                <div class="card sale-card">
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
        <div class="card sale-card mb-4">
            <div class="card-header sale-card-header"><i class="fas fa-sticky-note me-2"></i>Notes</div>
            <div class="card-body">
                <textarea name="notes" id="notes" class="form-control" rows="3"
                    placeholder="Add any notes...">{{ old('notes', $sale->notes) }}</textarea>
            </div>
        </div>

        {{-- ─── Submit ─── --}}
        <div class="d-flex gap-2 mb-4">
            <button type="submit" class="btn btn-success btn-lg px-4" id="btnUpdateSale">
                <i class="fas fa-save me-2"></i>Update Sale
            </button>
            <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </form>

    <!-- Image Modal -->
    <div class="modal fade" id="productImageModal" tabindex="-1" aria-hidden="true">
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
    <style>
        .sale-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .06);
            overflow: hidden;
        }

        .sale-card-header {
            background: linear-gradient(135deg, #1a5276 0%, #2980b9 100%);
            color: #fff;
            font-weight: 600;
            font-size: .95rem;
            padding: 12px 20px;
            border: none;
        }

        .sale-card .card-body {
            padding: 20px;
        }

        .select2-container--default .select2-selection--single {
            height: 38px !important;
            border: 1px solid #ced4da !important;
            border-radius: .375rem !important;
            padding: 4px 8px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }

        #sale-items-table thead th {
            background: linear-gradient(135deg, #154360 0%, #1a6fa8 100%);
            color: #fff;
            font-weight: 600;
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            padding: 10px 8px;
            border: none;
            vertical-align: middle;
            white-space: nowrap;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        #sale-items-table tbody td {
            vertical-align: middle;
            padding: 8px;
            font-size: .9rem;
            border-color: #eee;
        }

        .summary-table td {
            padding: 10px 16px;
            vertical-align: middle;
            border-color: #eee;
        }

        .summary-table td:first-child {
            width: 55%;
        }

        .product-thumb {
            border-radius: 6px;
            transition: transform .2s;
            border: 2px solid #e9ecef;
        }

        .product-thumb:hover {
            transform: scale(1.1);
            border-color: #6c63ff;
        }

        .payment-method-row {
            margin-bottom: 12px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .error-shadow {
            box-shadow: 0 0 0 3px rgba(220, 53, 69, .25) !important;
            border-color: #dc3545 !important;
        }

        .below-cost {
            color: #dc3545;
            font-weight: 600;
        }

        #sale-items-table .btn-danger {
            border-radius: 50%;
            width: 30px;
            height: 30px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #itemCountBadge {
            background: rgba(255, 255, 255, .2) !important;
            font-weight: 500;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function () {

            // ═══ CUSTOMER SELECT2 ═══
            $('#customer_id').select2({
                placeholder: 'Select a customer',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: '{{ route("customers.search") }}',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ q: params.term, page: params.page || 1 }),
                    processResults: (data, params) => ({ results: data.results, pagination: { more: data.pagination.more } }),
                    cache: true
                }
            });

            @if($sale->customer)
                var custOption = new Option(@json($sale->customer->name), '{{ $sale->customer_id }}', true, true);
                $('#customer_id').append(custOption).trigger('change');
            @endif

            // ═══ PRODUCT SELECT2 ═══
            $('#product_select').select2({
                placeholder: 'Search & select a product',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: '{{ route("products.search") }}',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ q: params.term, page: params.page || 1 }),
                    processResults: (data, params) => ({ results: data.results, pagination: { more: data.pagination.more } }),
                    cache: true
                },
                templateResult: formatProduct,
                templateSelection: formatProductSel
            });

            function formatProduct(p) {
                if (p.loading) return p.text;
                return p.image_url
                    ? $(`<div class='d-flex align-items-center'><img src='${p.image_url}' style='width:30px;height:30px;margin-right:10px;border-radius:4px;'>${p.text}</div>`)
                    : p.text;
            }
            function formatProductSel(p) {
                if (!p.id) return p.text;
                return p.image_url ? $(`<div class='d-flex align-items-center'><img src='${p.image_url}' style='width:24px;height:24px;margin-right:8px;border-radius:4px;'>${p.text}</div>`) : p.text;
            }

            // ═══ LOCATION SELECT2 ═══
            function initLocSelect(selector) {
                $(selector).select2({ placeholder: 'Select location', allowClear: true, width: '100%' });
            }

            // ═══ TOTALS ═══
            function updateTotals() {
                let total = 0;
                $('.row_total').each(function () { total += parseFloat($(this).val()) || 0; });
                let disc = parseFloat($('#discount_amount').val()) || 0;
                $('#total_amount').val(total.toFixed(2).replace(/\.00$/, '').replace(/(\.\d)0$/, '$1'));
                $('#net_amount').val(Math.max(0, total - disc).toFixed(2).replace(/\.00$/, '').replace(/(\.\d)0$/, '$1'));
                updatePaymentTotals();
            }

            $('#discount_amount').on('input', updateTotals);

            // ═══ ITEM ROW ═══
            let itemIdx = 0;
            let pmIdx = 0;

            function getLocOptions(selectedId) {
                let opts = '<option value="">Select Location</option>';
                @foreach($locations as $loc)
                    opts += `<option value="{{ $loc->id }}" ${selectedId == {{ $loc->id }} ? 'selected' : ''}>{{ $loc->name }}</option>`;
                @endforeach
                                return opts;
            }

            function addItemRow(productId, productName, productImage, batchNo, locationId, availQty, costPrice, salePrice, qty, discount) {
                let img = productImage
                    ? `<img src="${productImage}" class="img-thumbnail product-thumb" style="width:40px;height:40px;cursor:pointer;" data-image_url="${productImage}">`
                    : '<span class="text-muted small">N/A</span>';

                discount = discount || 0;
                let rowTotal = ((qty || 1) * (salePrice || 0) - discount);
                if (rowTotal < 0) rowTotal = 0;
                rowTotal = rowTotal.toFixed(2).replace(/\.00$/, '').replace(/(\.\d)0$/, '$1');

                let row = `
                    <tr>
                        <td>${img}
                            <input type="hidden" name="sale_items[${itemIdx}][product_id]" value="${productId}">
                        </td>
                        <td>${productName}</td>
                        <td>
                            <select name="sale_items[${itemIdx}][batch_no]" class="form-select form-select-sm batch-select" required style="font-size:0.78rem;padding:3px 6px;">
                                <option value="">Select</option>
                            </select>
                        </td>
                        <td>
                            <select name="sale_items[${itemIdx}][location_id]" class="form-select form-select-sm location-select" required style="font-size:0.78rem;padding:3px 6px;">
                                ${getLocOptions(locationId)}
                            </select>
                        </td>
                        <td><input type="number" class="form-control form-control-sm avail-qty" readonly value="${availQty || 0}" style="background:#f8f9fa;font-size:0.78rem;padding:3px 4px;"></td>
                        <td>
                            <input type="number" step="0.01" name="sale_items[${itemIdx}][purchase_price]" class="form-control form-control-sm cost-price" value="${costPrice || 0}" readonly style="background:#f8f9fa;font-size:0.78rem;padding:3px 4px;">
                        </td>
                        <td>
                            <input type="number" step="0.01" name="sale_items[${itemIdx}][sale_price]" class="form-control form-control-sm sale-price" required value="${salePrice || ''}" style="font-size:0.78rem;padding:3px 4px;">
                        </td>
                        <td>
                            <input type="number" step="0.01" name="sale_items[${itemIdx}][discount]" class="form-control form-control-sm item-discount" value="${discount}" min="0" style="font-size:0.78rem;padding:3px 4px;">
                        </td>
                        <td>
                            <input type="number" name="sale_items[${itemIdx}][quantity]" class="form-control form-control-sm qty" required min="1" value="${qty || 1}" style="font-size:0.78rem;padding:3px 4px;">
                        </td>
                        <td>
                            <input type="number" step="0.01" name="sale_items[${itemIdx}][total_amount]" class="form-control form-control-sm row_total" readonly value="${rowTotal}" style="background:#f8f9fa;font-size:0.78rem;padding:3px 4px;">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm remove-item" style="padding:2px 6px;font-size:0.7rem;"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>`;


                let $row = $(row);
                $('#sale-items-table tbody').append($row);
                initLocSelect($row.find('.location-select'));

                // Load batches for this product
                loadBatches(productId, itemIdx, batchNo, locationId);

                itemIdx++;
                updateTotals();
                updateItemCount();
            }

            function loadBatches(productId, idx, selectedBatchNo, selectedLocationId) {
                $.get('/products/' + productId + '/batches', function (data) {
                    let bSel = $(`select[name="sale_items[${idx}][batch_no]"]`);
                    let opts = '<option value="">Select Batch</option>';
                    (data.batches || []).forEach(b => {
                        opts += `<option value="${b.batch_no}" ${b.batch_no == selectedBatchNo ? 'selected' : ''}>${b.batch_no}</option>`;
                    });
                    bSel.html(opts);
                    if (selectedBatchNo) bSel.trigger('change.loadStock|silent', [selectedLocationId]);
                });
            }

            // When batch changes — load locations/qty via BatchController
            $('#sale-items-table').on('change', '.batch-select', function (e, selectedLocationId) {
                let row = $(this).closest('tr');
                let batchNo = $(this).val();
                let idx = $('select.batch-select').index(this);
                let productId = row.find('input[name*="[product_id]"]').val();
                let locSel = row.find('.location-select');
                let availQtyEl = row.find('.avail-qty');
                let costEl = row.find('.cost-price');

                if (!batchNo) return;

                $.get(`/batches/${batchNo}/products/${productId}/locations`, function (resp) {
                    if (resp.success && resp.locations) {
                        let lopts = '<option value="">Select Location</option>';
                        resp.locations.forEach(l => {
                            lopts += `<option value="${l.id}" data-qty="${l.quantity}" data-cost="${l.purchase_price || 0}" ${l.id == selectedLocationId ? 'selected' : ''}>${l.name} (${l.quantity} avail)</option>`;
                        });
                        locSel.html(lopts);
                        // Trigger location change if selectedLocationId pre-set
                        if (selectedLocationId) locSel.trigger('change');
                    }
                });
            });

            $('#sale-items-table').on('change', '.location-select', function () {
                let opt = $(this).find(':selected');
                let qty = parseFloat(opt.data('qty')) || 0;
                let cost = parseFloat(opt.data('cost')) || 0;
                let row = $(this).closest('tr');
                row.find('.avail-qty').val(qty);
                row.find('.cost-price').val(cost.toFixed(2).replace(/\.00$/, '').replace(/(\.\d)0$/, '$1'));
                checkBelowCost(row);
                updateRowTotal(row);
            });

            $('#sale-items-table').on('input', '.sale-price, .qty, .item-discount', function () {
                let row = $(this).closest('tr');
                checkBelowCost(row);
                updateRowTotal(row);
            });

            function updateRowTotal(row) {
                let qty = parseFloat(row.find('.qty').val()) || 0;
                let price = parseFloat(row.find('.sale-price').val()) || 0;
                let discount = parseFloat(row.find('.item-discount').val()) || 0;
                let total = (qty * price) - discount;
                if (total < 0) total = 0;
                row.find('.row_total').val(total.toFixed(2).replace(/\.00$/, '').replace(/(\.\d)0$/, '$1'));
                updateTotals();
            }

            function checkBelowCost(row) {
                let sPrice = parseFloat(row.find('.sale-price').val()) || 0;
                let cPrice = parseFloat(row.find('.cost-price').val()) || 0;
                row.find('.sale-price').toggleClass('below-cost', sPrice > 0 && sPrice < cPrice);
            }

            $('#sale-items-table').on('click', '.remove-item', function () {
                $(this).closest('tr').remove();
                updateTotals();
                updateItemCount();
            });

            // Image modal
            $('#sale-items-table').on('click', '.product-thumb', function () {
                let url = $(this).data('image_url');
                $('#modalProductImage').attr('src', url).removeClass('d-none');
                $('#modalNoImage').addClass('d-none');
                new bootstrap.Modal(document.getElementById('productImageModal')).show();
            });

            // ═══ ADD PRODUCT BUTTON ═══
            $('#add_product_btn').click(function () {
                let productId = $('#product_select').val();
                let productData = $('#product_select').select2('data')[0];
                if (!productId) { toastr.warning('Please select a product.'); return; }
                addItemRow(productId, productData.text, productData.image_url || '', '', '', 0, 0, '', 1, 0);
                $('#product_select').val(null).trigger('change');
            });

            // ═══ PRE-POPULATE EXISTING ITEMS ═══
            @foreach($sale->saleItems as $item)
                addItemRow(
                    '{{ $item->product_id }}',
                    @json($item->product->name),
                    @json($item->product->image_url ? asset($item->product->image_url) : ''),
                    @json($item->batch_no),
                    '{{ $item->location_id }}',
                    0,
                                            {{ $item->purchase_price }},
                                            {{ $item->sale_price }},
                                            {{ $item->quantity }},
                    {{ $item->discount ?? 0 }}
                );
            @endforeach

            // ═══ PAYMENT METHODS ═══
            function addPaymentMethodRow(selectedMethodId, amount) {
                let methods = @json($paymentMethods);
                let opts = '<option value="">Select Payment Method</option>';
                methods.forEach(m => {
                    opts += `<option value="${m.id}" ${m.id == selectedMethodId ? 'selected' : ''}>${m.method_name}</option>`;
                });
                let amtVal = amount ? amount : '';
                let amtShow = amount ? '' : 'display:none;';
                let html = `
                                    <div class="row g-2 payment-method-row">
                                        <div class="col-md-6">
                                            <label class="form-label">Payment Method</label>
                                            <select name="payment_methods[${pmIdx}][payment_method_id]" class="form-select pm-select">${opts}</select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Amount ({{ setting('currency_symbol', '$') }})</label>
                                            <input type="number" step="0.01" name="payment_methods[${pmIdx}][amount]" class="form-control pm-amount" min="0.01" value="${amtVal}" style="${amtShow}">
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger remove-pm"><i class="fas fa-minus"></i></button>
                                        </div>
                                    </div>`;
                $('#payment-methods-container').append(html);
                pmIdx++;
                toggleRemoveAll();
                toggleNoPayments();
                updatePaymentTotals();
            }

            $('#add_payment_method_btn').click(() => addPaymentMethodRow(null, null));
            $('#payment-methods-container').on('click', '.remove-pm', function () {
                $(this).closest('.payment-method-row').remove();
                updatePaymentTotals(); toggleRemoveAll(); toggleNoPayments();
            });
            $('#remove_all_payment_methods_btn').click(function () {
                $('#payment-methods-container').empty();
                $('#total_payment_amount').val('0.00');
                $(this).hide();
                toggleNoPayments();
            });
            $('#payment-methods-container').on('change', '.pm-select', function () {
                let amt = $(this).closest('.payment-method-row').find('.pm-amount');
                $(this).val() ? amt.slideDown() : amt.slideUp().val('');
                updatePaymentTotals();
            });
            $('#payment-methods-container').on('input', '.pm-amount', updatePaymentTotals);

            function toggleRemoveAll() {
                $('#remove_all_payment_methods_btn').toggle($('#payment-methods-container .payment-method-row').length > 0);
            }
            function toggleNoPayments() {
                $('#no-payments-msg').toggle($('#payment-methods-container .payment-method-row').length === 0);
            }
            function updateItemCount() {
                let n = $('#sale-items-table tbody tr').length;
                $('#itemCountBadge').text(n + (n === 1 ? ' item' : ' items'));
            }
            function updatePaymentTotals() {
                let total = 0;
                $('.pm-amount:visible').each(function () { total += parseFloat($(this).val()) || 0; });
                let net = parseFloat($('#net_amount').val()) || 0;
                $('#total_payment_amount').val(total.toFixed(2).replace(/\.00$/, '').replace(/(\.\d)0$/, '$1'));
                let warn = total > 0 && Math.abs(total - net) > 0.01;
                $('#payment-methods-container-wrapper').toggleClass('error-shadow', warn);
            }

            // ═══ PRE-POPULATE EXISTING PAYMENTS ═══
            @foreach($sale->transactions as $tx)
                addPaymentMethodRow('{{ $tx->payment_method_id }}', {{ $tx->amount }});
            @endforeach

            // ═══ FORM SUBMISSION ═══
            $('#sale-form').on('submit', function (e) {
                e.preventDefault();
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#payment-methods-container-wrapper').removeClass('error-shadow');

                let $btn = $('#btnUpdateSale');
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Updating...');

                $.ajax({
                    type: 'PUT',
                    url: '{{ route("sales.update", $sale->id) }}',
                    data: $(this).serialize(),
                    success: function (resp) {
                        if (resp.success) {
                            toastr.success(resp.message);
                            window.location.href = resp.redirect;
                        } else {
                            toastr.error(resp.message);
                            $btn.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Update Sale');
                        }
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Update Sale');
                        let msg = 'An error occurred while updating the sale.';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            msg = xhr.responseJSON.error;
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }

                        if (xhr.status === 422 && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;
                            msg = Object.values(errors)[0][0];
                            $.each(errors, function (key, msgs) {
                                let input = $('[name="' + key + '"]');
                                input.addClass('is-invalid');
                                input.next('.invalid-feedback').text(msgs[0]);
                            });
                        }
                        toastr.error(msg);
                    }
                });
            });

        });
    </script>
@endpush