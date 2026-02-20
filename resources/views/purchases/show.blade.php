@extends('layouts.app')

@section('title', 'Purchase Details')

@section('page_title', 'Purchase Details')

@section('content')
    <!-- Print Button -->
    <div class="mb-4 text-end">
        <a href="{{route('purchases.pdf', $purchase)}}" target="_blank" class="btn btn-secondary">
            <i class="fas fa-print"></i> PDF
        </a>
    </div>

    <!-- Purchase Details Card -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5>Purchase Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Vendor Information -->
                <div class="col-md-4">
                    <strong>Vendor:</strong>
                    <p>{{ $purchase->vendor->name }}</p>
                </div>
                <!-- Invoice Number -->
                <div class="col-md-4">
                    <strong>Invoice No.:</strong>
                    <p>{{ $purchase->invoice_no }}</p>
                </div>
                <!-- Purchase Date -->
                <div class="col-md-4">
                    <strong>Purchase Date:</strong>
                    <p>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('F j, Y') }}</p>
                </div>
            </div>
            <div class="row">
                <!-- Total Amount -->
                <div class="col-md-4">
                    <strong>Total Amount ({{ setting('currency_symbol', '$') }}):</strong>
                    <p>{{ number_format($purchase->total_amount, 2) }}</p>
                </div>
                <!-- Discount Amount -->
                <div class="col-md-4">
                    <strong>Discount ({{ setting('currency_symbol', '$') }}):</strong>
                    <p>{{ number_format($purchase->discount_amount, 2) }}</p>
                </div>
                <!-- Net Amount -->
                <div class="col-md-4">
                    <strong>Net Amount ({{ setting('currency_symbol', '$') }}):</strong>
                    <p>{{ number_format($purchase->net_amount, 2) }}</p>
                </div>
            </div>
            @if($purchase->notes)
                <div class="row">
                    <div class="col-md-12">
                        <strong>Notes:</strong>
                        <p>{{ $purchase->notes }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Purchase Items Table -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <h5>Purchase Items</h5>
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
                            <th>Purchase Price ({{ setting('currency_symbol', '$') }})</th>
                            <th>Sale Price ({{ setting('currency_symbol', '$') }})</th>
                            <th>Total ({{ setting('currency_symbol', '$') }})</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->purchaseItems as $item)
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
                                <td>{{ number_format($item->purchase_price, 2) }}</td>
                                <td>{{ number_format($item->sale_price, 2) }}</td>
                                <td>{{ number_format($item->total_amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="8" class="text-start">Total Amount:</th>
                            <th>{{ number_format($purchase->total_amount, 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="8" class="text-start">Discount Amount:</th>
                            <th>{{ number_format($purchase->discount_amount, 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="8" class="text-start">Net Amount:</th>
                            <th>{{ number_format($purchase->net_amount, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Payment Methods Section -->
    @if($purchase->transactions->count() > 0)
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
                            @foreach($purchase->transactions as $transaction)
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
                                <th>{{ number_format($purchase->transactions->sum('amount'), 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="2">Remaining Balance:</th>
                                <th>{{ number_format($purchase->net_amount - $purchase->transactions->sum('amount'), 2) }}</th>
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