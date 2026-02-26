@extends('exports.layout')
@section('content')
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Invoice No</th>
                <th>Customer</th>
                <th>Date</th>
                <th class="text-right">Total</th>
                <th class="text-right">Discount</th>
                <th class="text-right">Net Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $sale->invoice_no }}</td>
                    <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                    <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</td>
                    <td class="text-right">{{ setting('currency_symbol', '$') }}{{ format_number($sale->total_amount) }}</td>
                    <td class="text-right">{{ setting('currency_symbol', '$') }}{{ format_number($sale->discount_amount) }}
                    </td>
                    <td class="text-right fw-bold">
                        {{ setting('currency_symbol', '$') }}{{ format_number($sale->net_amount) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <p><strong>Total Sales:</strong> {{ $sales->count() }}</p>
        <p><strong>Total Amount:</strong>
            {{ setting('currency_symbol', '$') }}{{ format_number($sales->sum('net_amount'), 2) }}</p>
    </div>
@endsection