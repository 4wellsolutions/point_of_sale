@extends('layouts.app')

@section('title', 'Inventory Transactions')

@section('page_title', 'Inventory Transactions')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('inventory.index') }}" method="GET" id="filterForm">
                <div class="row">
                    <!-- Product Dropdown with AJAX Select2 -->
                    <div class="col-md-3">
                        <label for="product_id">Product</label>
                        <select name="product_id" id="product_id" class="form-control select2-ajax">
                            @if(request('product_id'))
                                @php
                                    $selectedProduct = \App\Models\Product::find(request('product_id'));
                                @endphp
                                @if($selectedProduct)
                                    <option value="{{ $selectedProduct->id }}" selected>{{ $selectedProduct->name }} (SKU: {{ $selectedProduct->sku }})</option>
                                @endif
                            @endif
                        </select>
                    </div>

                    <!-- Transaction Type Filter -->
                    <div class="col-md-3">
                        <label for="transaction_type">Transaction Type</label>
                        <select name="transaction_type" class="form-control">
                            <option value="">All</option>
                            <option value="purchase" {{ request('transaction_type') == 'purchase' ? 'selected' : '' }}>Purchase</option>
                            <option value="sale" {{ request('transaction_type') == 'sale' ? 'selected' : '' }}>Sale</option>
                            <option value="adjustment" {{ request('transaction_type') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                            <option value="purchase_return" {{ request('transaction_type') == 'purchase_return' ? 'selected' : '' }}>Purchase Return</option>
                            <option value="sales_return" {{ request('transaction_type') == 'sales_return' ? 'selected' : '' }}>Sales Return</option>
                        </select>
                    </div>

                    <!-- User Dropdown -->
                    <div class="col-md-3">
                        <label for="user_id">User</label>
                        <select name="user_id" class="form-control">
                            <option value="">All</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range Filters -->
                    <div class="col-md-3">
                        <label>From Date</label>
                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                    </div>

                    <div class="col-md-3">
                        <label>To Date</label>
                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-md-6 mt-4">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                        <a href="{{ route('inventory.viewPdf', request()->all()) }}" class="btn btn-danger" target="_blank">
                            <i class="fas fa-file-pdf"></i> View PDF
                        </a>
                        <button type="button" id="resetFilters" class="btn btn-secondary"><i class="fas fa-sync"></i> Reset</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card mt-3">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Transaction Type</th>
                        <th>Quantity</th>
                        <th>User</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->id }}</td>
                            <td>{{ $transaction->product->name }}</td>
                            <td>{{ class_basename($transaction->transactionable_type) }}</td>
                            <td>{{ $transaction->quantity }}</td>
                            <td>{{ $transaction->user ? $transaction->user->name : 'N/A' }}</td>
                            <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $transactions->links("pagination::bootstrap-5") }}
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        // Initialize Select2 with AJAX for Products Dropdown
        $('.select2-ajax').select2({
            placeholder: "Search for a product...",
            allowClear: true,
            ajax: {
                url: "{{ route('products.search') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.results, function (item) {
                            return {
                                id: item.id,
                                text: item.text, // Product name + SKU
                                image: item.image_url // Image URL
                            };
                        }),
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            },
            templateResult: formatProduct,
            templateSelection: formatProductSelection
        });

        // Format dropdown items (show image + text)
        function formatProduct(product) {
            if (!product.id) {
                return product.text;
            }
            let image = product.image ? `<img src="${product.image}" class="img-thumbnail" style="width: 40px; height: 40px; margin-right: 10px;">` : '';
            return $('<span>' + image + product.text + '</span>');
        }

        // Format selected product display
        function formatProductSelection(product) {
            return product.text || product.id;
        }

        // Auto-select the filtered product (if exists)
        let selectedProductId = "{{ request('product_id') }}";
        let selectedProductText = "{{ isset($selectedProduct) ? $selectedProduct->name . ' (SKU: ' . $selectedProduct->sku . ')' : '' }}";

        if (selectedProductId) {
            let newOption = new Option(selectedProductText, selectedProductId, true, true);
            $('#product_id').append(newOption).trigger('change');
        }

        // Reset Filters Button
        $('#resetFilters').click(function () {
            $('#filterForm')[0].reset(); // Reset the form
            $('.select2-ajax').val(null).trigger('change'); // Reset Select2 dropdown
            window.location.href = "{{ route('inventory.index') }}"; // Reload page
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
