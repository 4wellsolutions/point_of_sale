@extends('exports.layout')
@section('content')
    <table class="data-table">
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
            @foreach($records as $i => $r)
                <tr class="{{ $i % 2 == 0 ? 'row-alt' : '' }}">
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $r->invoice_no }}</td>
                    <td>{{ $r->vendor->name ?? '—' }}</td>
                    <td>{{ \Carbon\Carbon::parse($r->purchase_date)->format('d M Y') }}</td>
                    <td class="text-right">{{ setting('currency_symbol') }}{{ format_number($r->total_amount, 2) }}</td>
                    <td class="text-right">{{ setting('currency_symbol') }}{{ format_number($r->discount_amount, 2) }}</td>
                    <td class="text-right">
                        <strong>{{ setting('currency_symbol') }}{{ format_number($r->net_amount, 2) }}</strong>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4">Totals ({{ $records->count() }} records)</th>
                <th class="text-right">{{ setting('currency_symbol') }}{{ format_number($records->sum('total_amount'), 2) }}
                </th>
                <th class="text-right">
                    {{ setting('currency_symbol') }}{{ format_number($records->sum('discount_amount'), 2) }}
                </th>
                <th class="text-right">{{ setting('currency_symbol') }}{{ format_number($records->sum('net_amount'), 2) }}
                </th>
            </tr>
        </tfoot>
    </table>
@endsection