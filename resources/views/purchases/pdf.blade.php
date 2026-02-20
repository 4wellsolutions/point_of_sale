@extends('layouts.pdf')

@section('title', 'Sale Details')

@section('content')

    <div class="card">
        <div class="card-header">
            Purchase Information
        </div>
        <div class="card-body">
            <table class="fixed-width-table">
                <tr>
                    <th>Vendor:</th>
                    <td>{{ $purchase->vendor->name }}</td>
                    <th>Invoice No.:</th>
                    <td>{{ $purchase->invoice_no }}</td>
                </tr>
                <tr>
                    <th>Purchase Date:</th>
                    <td>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('F j, Y') }}</td>
                    <th>Total Amount ($):</th>
                    <td>{{ number_format($purchase->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <th>Discount ($):</th>
                    <td>{{ number_format($purchase->discount_amount, 2) }}</td>
                    <th>Net Amount ($):</th>
                    <td>{{ number_format($purchase->net_amount, 2) }}</td>
                </tr>
            </table>

            @if($purchase->notes)
                <div class="notes-section"><strong>Notes:</strong> {{ $purchase->notes }}</div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Purchase Items
        </div>
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Expiry Date</th>
                        <th>Location</th>
                        <th>Batch No.</th>
                        <th>Quantity</th>
                        <th>Cost Per Piece ($)</th>
                        <th>Total ($)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->purchaseItems as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('F j, Y') : 'N/A' }}</td>
                            <td>{{ $item->location->name }}</td>
                            <td>{{ $item->batch_no ?? 'N/A' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->cost_per_piece, 2) }}</td>
                            <td>{{ number_format($item->total_amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="summary-row">
                    <tr>
                        <th colspan="6">Total:</th>
                        <td>{{ number_format($purchase->total_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <th colspan="6">Discount:</th>
                        <td>{{ number_format($purchase->discount_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <th colspan="6">Net Amount:</th>
                        <td>{{ number_format($purchase->net_amount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    @if($purchase->transactions->count() > 0)
        <div class="card">
            <div class="card-header">
                Payment Methods
            </div>
            <div class="card-body">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Payment Method</th>
                            <th>Amount ($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->transactions as $transaction)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('F j, Y') }}</td>
                                <td>{{ $transaction->paymentMethod->method_name }}</td>
                                <td>{{ number_format($transaction->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="summary-row">
                        <tr>
                            <th colspan="2">Total Payment:</th>
                            <td>{{ number_format($purchase->transactions->sum('amount'), 2) }}</td>
                        </tr>
                        <tr>
                            <th colspan="2">Remaining Balance:</th>
                            <td>{{ number_format($purchase->net_amount - $purchase->transactions->sum('amount'), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
@stop
