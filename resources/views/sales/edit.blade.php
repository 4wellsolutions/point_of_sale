@extends('layouts.app')

@section('title', 'Edit Sale #' . $sale->invoice_no)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Sales</a></li>
    <li class="breadcrumb-item active">Edit Sale #{{ $sale->invoice_no }}</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Sale #{{ $sale->invoice_no }}</h5>
            <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-secondary"><i
                    class="fas fa-arrow-left me-1"></i>Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('sales.update', $sale->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="customer_id" class="form-label">Customer <span class="text-danger">*</span></label>
                        <select name="customer_id" id="customer_id" class="form-control" required>
                            @if($sale->customer)
                                <option value="{{ $sale->customer->id }}" selected>{{ $sale->customer->name }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="invoice_no" class="form-label">Invoice No.</label>
                        <input type="text" value="{{ $sale->invoice_no }}" class="form-control" readonly>
                    </div>
                    <div class="col-md-3">
                        <label for="sale_date" class="form-label">Sale Date <span class="text-danger">*</span></label>
                        <input type="date" name="sale_date" id="sale_date"
                            class="form-control @error('sale_date') is-invalid @enderror"
                            value="{{ old('sale_date', $sale->sale_date) }}" required>
                        @error('sale_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label for="notes" class="form-label">Notes</label>
                        <input type="text" name="notes" id="notes" class="form-control"
                            value="{{ old('notes', $sale->notes) }}" placeholder="Optional notes">
                    </div>
                </div>

                {{-- Read-only Sale Items --}}
                <div class="mb-4">
                    <h5><i class="fas fa-boxes me-2"></i>Sale Items <small class="text-muted">(read-only)</small></h5>
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Location</th>
                                    <th class="text-end">Purchase Price</th>
                                    <th class="text-end">Sale Price</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->saleItems as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->product->name ?? '—' }}</td>
                                        <td>{{ $item->location->name ?? '—' }}</td>
                                        <td class="text-end">${{ number_format($item->purchase_price, 2) }}</td>
                                        <td class="text-end">${{ number_format($item->sale_price, 2) }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end fw-bold">${{ number_format($item->total_amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Totals --}}
                <div class="row mb-3">
                    <div class="col-md-4 offset-md-8">
                        <table class="table">
                            <tr>
                                <th>Total Amount ($):</th>
                                <td><input type="number" step="0.01" id="total_amount" class="form-control"
                                        value="{{ $sale->total_amount }}" readonly></td>
                            </tr>
                            <tr>
                                <th>Discount ($):</th>
                                <td>
                                    <input type="number" step="0.01" name="discount_amount" id="discount_amount"
                                        class="form-control @error('discount_amount') is-invalid @enderror"
                                        value="{{ old('discount_amount', $sale->discount_amount) }}" min="0">
                                    @error('discount_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </td>
                            </tr>
                            <tr>
                                <th>Net Amount ($):</th>
                                <td><input type="number" step="0.01" id="net_amount" class="form-control"
                                        value="{{ $sale->net_amount }}" readonly></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Update Sale</button>
                    <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">Cancel</a>
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
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function () {
            // Initialize Select2 for Customer with pre-selected value
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

            // Recalculate net amount when discount changes
            $('#discount_amount').on('input', function () {
                let total = parseFloat($('#total_amount').val()) || 0;
                let discount = parseFloat($(this).val()) || 0;
                $('#net_amount').val((total - discount).toFixed(2));
            });
        });
    </script>
@endpush