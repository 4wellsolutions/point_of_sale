@extends('layouts.pdf')

@section('title', 'Sale Details')

@section('content')

    <!-- Sale Header -->
    <div style="text-align: center; margin-bottom: 20px;">
        <h1>Sale Details</h1>
    </div>

    <!-- Sale Information -->
    <div style="margin-bottom: 20px;">
        <h4>Sale Information</h4>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
            <tr>
                <td style="width: 33%;"><strong>Customer:</strong></td>
                <td style="width: 67%;">{{ $sale->customer->name }}</td>
            </tr>
            <tr>
                <td><strong>Invoice No.:</strong></td>
                <td>{{ $sale->invoice_no }}</td>
            </tr>
            <tr>
                <td><strong>Sale Date:</strong></td>
                <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('F j, Y') }}</td>
            </tr>
            <tr>
                <td><strong>Total Amount ($):</strong></td>
                <td>{{ number_format($sale->total_amount, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Discount ($):</strong></td>
                <td>{{ number_format($sale->discount_amount, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Net Amount ($):</strong></td>
                <td>{{ number_format($sale->net_amount, 2) }}</td>
            </tr>
        </table>

        @if($sale->notes)
            <h5>Notes:</h5>
            <p>{{ $sale->notes }}</p>
        @endif
    </div>

    <!-- Sale Items Table -->
    <div style="margin-bottom: 20px;">
        <h4>Sale Items</h4>
        <table style="width: 100%; border: 1px solid #ddd; border-collapse: collapse; margin-bottom: 10px;">
            <thead>
                <tr>
                    <th style="border: 1px solid #ddd; padding: 5px;">Product Name</th>
                    <th style="border: 1px solid #ddd; padding: 5px;">Expiry Date</th>
                    <th style="border: 1px solid #ddd; padding: 5px;">Location</th>
                    <th style="border: 1px solid #ddd; padding: 5px;">Batch No.</th>
                    <th style="border: 1px solid #ddd; padding: 5px;">Quantity</th>
                    <th style="border: 1px solid #ddd; padding: 5px;">Sale Price</th>
                    <th style="border: 1px solid #ddd; padding: 5px;">Total ($)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->saleItems as $item)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 5px;">{{ $item->product->name }}</td>
                        <td style="border: 1px solid #ddd; padding: 5px;">
                            @if($item->expiry_date)
                                {{ \Carbon\Carbon::parse($item->expiry_date)->format('F j, Y') }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td style="border: 1px solid #ddd; padding: 5px;">{{ $item->location->name }}</td>
                        <td style="border: 1px solid #ddd; padding: 5px;">{{ $item->batch_no ?? 'N/A' }}</td>
                        <td style="border: 1px solid #ddd; padding: 5px;">{{ $item->quantity }}</td>
                        <td style="border: 1px solid #ddd; padding: 5px;">{{ number_format($item->sale_price, 2) }}</td>
                        <td style="border: 1px solid #ddd; padding: 5px;">{{ number_format($item->total_amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="6" style="border: 1px solid #ddd; padding: 5px; text-align: right;">Total:</th>
                    <th style="border: 1px solid #ddd; padding: 5px;">{{ number_format($sale->total_amount, 2) }}</th>
                </tr>
                <tr>
                    <th colspan="6" style="border: 1px solid #ddd; padding: 5px; text-align: right;">Discount:</th>
                    <th style="border: 1px solid #ddd; padding: 5px;">{{ number_format($sale->discount_amount, 2) }}</th>
                </tr>
                <tr>
                    <th colspan="6" style="border: 1px solid #ddd; padding: 5px; text-align: right;">Net Amount:</th>
                    <th style="border: 1px solid #ddd; padding: 5px;">{{ number_format($sale->net_amount, 2) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Payment Methods Section -->
    @if($sale->transactions->count() > 0)
        <div style="margin-bottom: 20px;">
            <h4>Payment Methods</h4>
            <table style="width: 100%; border: 1px solid #ddd; border-collapse: collapse; margin-bottom: 10px;">
                <thead>
                    <tr>
                        <th style="border: 1px solid #ddd; padding: 5px;">Date</th>
                        <th style="border: 1px solid #ddd; padding: 5px;">Payment Method</th>
                        <th style="border: 1px solid #ddd; padding: 5px;">Amount ($)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->transactions as $transaction)
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 5px;">
                                {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('F j, Y') }}
                            </td>
                            <td style="border: 1px solid #ddd; padding: 5px;">{{ $transaction->paymentMethod->method_name }}</td>
                            <td style="border: 1px solid #ddd; padding: 5px; text-align: right;">{{ number_format($transaction->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" style="border: 1px solid #ddd; padding: 5px; text-align: right;">Total Payment:</th>
                        <th style="border: 1px solid #ddd; padding: 5px; text-align: right;">{{ number_format($sale->transactions->sum('amount'), 2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="2" style="border: 1px solid #ddd; padding: 5px; text-align: right;">Remaining Balance:</th>
                        <th style="border: 1px solid #ddd; padding: 5px; text-align: right;">{{ number_format($sale->net_amount - $sale->transactions->sum('amount'), 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif

@endsection
