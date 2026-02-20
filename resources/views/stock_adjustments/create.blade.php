@extends('layouts.app')

@section('title', 'Create Stock Adjustment')

@section('content_header')
    <h1>Create Stock Adjustment</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form id="stock-adjustment-form" action="{{ route('stock_adjustments.store') }}" method="POST">
                @csrf

                <!-- Adjustment Date -->
                <div class="mb-3">
                    <label for="date" class="form-label">Adjustment Date</label>
                    <input type="date" name="date" id="date" class="form-control" required value="{{ now()->format('Y-m-d') }}">
                </div>

                <!-- Product Selection with Input Group -->
                <div class="mb-4">
                    <label for="product_select" class="form-label">Select Product</label>
                    <div class="input-group">
                        <select id="product_select" class="form-select" style="width: 100%;">
                            <option value="">Search for a product</option>
                        </select>
                        <button type="button" class="btn btn-primary" id="add_product_btn">
                            <i class="fas fa-plus"></i> Add Product
                        </button>
                    </div>
                </div>

                <!-- Adjustment Items Grid -->
                <div class="mb-4">
                    <h5>Stock Adjustment Items</h5>
                    <table class="table table-bordered" id="adjustment-items-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Adjustment Type</th>
                                <th>Reason (Optional)</th>
                                <th>Batch No</th>
                                <th>Location</th>
                                <th>Available Qty</th>
                                <th>Adjust Qty</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dynamic rows will be appended here -->
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Submit Adjustment
                </button>
                <a href="{{ route('stock_adjustments.index') }}" class="btn btn-secondary">Cancel</a>
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
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
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

        let adjustmentItemIndex = 0;

        $('#add_product_btn').click(function() {
            let productId = $('#product_select').val();
            let productData = $('#product_select').select2('data')[0];

            if (!productId) {
                toastr.warning('Please select a product.');
                return;
            }

            let newRow = `
                <tr data-product-id="${productId}">
                    <td>
                        ${productData.text}
                        <input type="hidden" name="stock_adjustments[${adjustmentItemIndex}][product_id]" value="${productId}">
                    </td>
                    <td>
                        <select name="stock_adjustments[${adjustmentItemIndex}][adjustment_type]" class="form-select adjustment-type" required>
                            <option value="increase">Increase</option>
                            <option value="decrease">Decrease</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="stock_adjustments[${adjustmentItemIndex}][reason]" class="form-control">
                    </td>
                    <td>
                        <select name="stock_adjustments[${adjustmentItemIndex}][batch_no]" class="form-select batch-select" required>
                            <option value="">Loading batches...</option>
                        </select>
                    </td>
                    <td>
                        <select name="stock_adjustments[${adjustmentItemIndex}][location_id]" class="form-select location-select" required>
                            <option value="">Select Batch First</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" class="form-control available-qty" readonly value="0">
                    </td>
                    <td>
                        <input type="number" name="stock_adjustments[${adjustmentItemIndex}][adjust_qty]" class="form-control adjust-qty" required min="1">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-adjustment-item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#adjustment-items-table tbody').append(newRow);
            loadBatches(productId, adjustmentItemIndex);
            adjustmentItemIndex++;
            $('#product_select').val(null).trigger('change');
        });

        $('#adjustment-items-table').on('click', '.remove-adjustment-item', function() {
            $(this).closest('tr').remove();
        });

        function loadBatches(productId, index) {
            let batchSelect = $(`select[name="stock_adjustments[${index}][batch_no]"]`);
            $.ajax({
                url: '/products/' + productId + '/batches',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    let options = '<option value="">Select Batch</option>';
                    data.batches.forEach(batch => {
                        options += `<option value="${batch.batch_no}">${batch.batch_no}</option>`;
                    });
                    batchSelect.html(options);
                }
            });
        }

        $('#adjustment-items-table').on('change', '.batch-select', function() {
            let row = $(this).closest('tr');
            let batchNo = $(this).val();
            let locationSelect = row.find('.location-select');

            $.ajax({
                url: '/batches/' + batchNo + '/locations',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        let options = '<option value="">Select Location</option>';
                        response.locations.forEach(location => {
                            options += `<option value="${location.id}" data-quantity="${location.quantity}">${location.name}</option>`;
                        });
                        locationSelect.html(options);
                    } else {
                        toastr.error(response.error);
                    }
                }
            });
        });

        $('#adjustment-items-table').on('change', '.location-select', function() {
            let selectedOption = $(this).find(':selected');
            let availableQtyInput = $(this).closest('tr').find('.available-qty');
            let quantity = selectedOption.data('quantity') || 0;
            availableQtyInput.val(quantity);
        });

        $('#adjustment-items-table').on('input', '.adjust-qty', function() {
            let row = $(this).closest('tr');
            let adjustmentType = row.find('.adjustment-type').val();
            let availableQty = parseInt(row.find('.available-qty').val()) || 0;
            let adjustQty = parseInt($(this).val()) || 0;

            if (adjustmentType === 'decrease' && adjustQty > availableQty) {
                toastr.error("Cannot decrease more than available quantity.");
                $(this).val(availableQty);
            }
        });
    });
</script>
@endpush
