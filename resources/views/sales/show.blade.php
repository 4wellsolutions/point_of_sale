@extends('layouts.app')

@section('title', 'Sale Details')

@section('page_title', 'Sale Details')

@section('content')
    <!-- Print Button -->
    <div class="mb-4 text-end">
        <a href="{{ route('sales.pdf', $sale) }}" target="_blank" class="btn btn-secondary">
            <i class="fas fa-print"></i> PDF
        </a>
    </div>

    <!-- Sale Details Card -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5>Sale Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Customer Information -->
                <div class="col-md-4">
                    <strong>Customer:</strong>
                    <p>{{ $sale->customer->name }}</p>
                </div>
                <!-- Invoice Number -->
                <div class="col-md-4">
                    <strong>Invoice No.:</strong>
                    <p>{{ $sale->invoice_no }}</p>
                </div>
                <!-- Sale Date -->
                <div class="col-md-4">
                    <strong>Sale Date:</strong>
                    <p>{{ \Carbon\Carbon::parse($sale->sale_date)->format('F j, Y') }}</p>
                </div>
            </div>
            <div class="row">
                <!-- Total Amount -->
                <div class="col-md-4">
                    <strong>Total Amount ({{ setting('currency_symbol', '$') }}):</strong>
                    <p>{{ number_format($sale->total_amount, 2) }}</p>
                </div>
                <!-- Discount Amount -->
                <div class="col-md-4">
                    <strong>Discount ({{ setting('currency_symbol', '$') }}):</strong>
                    <p>{{ number_format($sale->discount_amount, 2) }}</p>
                </div>
                <!-- Net Amount -->
                <div class="col-md-4">
                    <strong>Net Amount ({{ setting('currency_symbol', '$') }}):</strong>
                    <p>{{ number_format($sale->net_amount, 2) }}</p>
                </div>
            </div>
            @if($sale->notes)
                <div class="row">
                    <div class="col-md-12">
                        <strong>Notes:</strong>
                        <p>{{ $sale->notes }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Sale Items Table -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <h5>Sale Items</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>Expiry Date</th>
                            <th>Location</th>
                            <th>Batch No.</th>
                            <th>Quantity</th>
                            <th>Sale Price</th>
                            <th>Total ({{ setting('currency_symbol', '$') }})</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->saleItems as $item)
                            <tr>
                                <td>
                                    @if($item->product->image_url)
                                        <img src="{{ asset($item->product->image_url) }}" alt="{{ $item->product->name }}"
                                            class="product-image">
                                    @else
                                        <img src="{{ asset('images/no-image.png') }}" alt="No Image" class="product-image">
                                    @endif
                                </td>
                                <td>{{ $item->product->name }}</td>
                                <td>
                                    @if($item->expiry_date)
                                        {{ \Carbon\Carbon::parse($item->expiry_date)->format('F j, Y') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $item->location->name }}</td>
                                <td>{{ $item->batch_no ?? 'N/A' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->sale_price, 2) }}</td>
                                <td>{{ number_format($item->total_amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="7" class="text-end">Total:</th>
                            <th>{{ number_format($sale->total_amount, 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="7" class="text-end">Discount:</th>
                            <th>{{ number_format($sale->discount_amount, 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="7" class="text-end">Net Amount:</th>
                            <th>{{ number_format($sale->net_amount, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Payment Methods Section -->
    @if($sale->transactions->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5>Payment Methods</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th class="text-start">Payment Method</th>
                                <th>Amount ({{ setting('currency_symbol', '$') }})</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->transactions as $transaction)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('F j, Y') }}</td>
                                    <td>{{ $transaction->paymentMethod->method_name }}</td>
                                    <td class="text-center">{{ number_format($transaction->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2">Total Payment:</th>
                                <th>{{ number_format($sale->transactions->sum('amount'), 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="2">Remaining Balance:</th>
                                <th>{{ number_format($sale->net_amount - $sale->transactions->sum('amount'), 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif

@endsection

@push('styles')
    <style>
        /* Product Image Styling */
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }

        /* Table Header Styling */
        table th {
            background-color: #f8f9fa;
            text-align: center;
        }

        /* Table Cell Alignment */
        table td,
        table th {
            vertical-align: middle !important;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .product-image {
                width: 40px;
                height: 40px;
            }

            .card-header h5 {
                font-size: 1.2em;
            }
        }

        /* Print Styles */
        @media print {

            .btn,
            .card-header,
            .table-responsive {
                display: none;
            }

            .card-body {
                margin: 0;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
    </script>
@endpush