@extends('exports.layout')
@section('content')
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Invoice No</th>
                <th>Vendor</th>
                <th>Date</th>
                <th class="text-right">Total</th>
                <th class="text-right">Discount</th>
                <th class="text-right">Net Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchases as $purchase)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $purchase->invoice_no }}</td>
                    <td>{{ $purchase->vendor->name ?? '—' }}</td>
                    <td>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</td>
                    <td class="text-right">{{ setting('currency_symbol', '$') }}{{ number_format($purchase->total_amount, 2) }}
                    </td>
                    <td class="text-right">
                        {{ setting('currency_symbol', '$') }}{{ number_format($purchase->discount_amount, 2) }}</td>
                    <td class="text-right fw-bold">
                        {{ setting('currency_symbol', '$') }}{{ number_format($purchase->net_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <p><strong>Total Purchases:</strong> {{ $purchases->count() }}</p>
        <p><strong>Total Amount:</strong>
            {{ setting('currency_symbol', '$') }}{{ number_format($purchases->sum('net_amount'), 2) }}</p>
    </div>
@endsection