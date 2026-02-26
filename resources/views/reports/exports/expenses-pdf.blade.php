@extends('exports.layout')
@section('content')
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Type</th>
                <th>Description</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $i => $r)
                <tr class="{{ $i % 2 == 0 ? 'row-alt' : '' }}">
                    <td>{{ $i + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($r->date)->format('d M Y') }}</td>
                    <td>{{ $r->expenseType->name ?? '—' }}</td>
                    <td>{{ $r->description ?? '—' }}</td>
                    <td class="text-right"><strong>{{ setting('currency_symbol') }}{{ format_number($r->amount) }}</strong>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4">Total ({{ $records->count() }} records)</th>
                <th class="text-right">{{ setting('currency_symbol') }}{{ format_number($records->sum('amount'), 2) }}</th>
            </tr>
        </tfoot>
    </table>
@endsection