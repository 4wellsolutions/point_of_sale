@extends('exports.layout')
@section('content')
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Type</th>
                <th>Party</th>
                <th>Payment Method</th>
                <th class="text-right">Amount</th>
                <th>Reference</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d M Y') }}</td>
                    <td>
                        <span class="badge {{ $transaction->type == 'payment' ? 'badge-danger' : 'badge-success' }}">
                            {{ ucfirst($transaction->type) }}
                        </span>
                    </td>
                    <td>{{ $transaction->vendor->name ?? $transaction->customer->name ?? '—' }}</td>
                    <td>{{ $transaction->paymentMethod->name ?? '—' }}</td>
                    <td class="text-right fw-bold">
                        {{ setting('currency_symbol', '$') }}{{ number_format($transaction->amount, 2) }}</td>
                    <td>{{ $transaction->reference ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <p><strong>Total Transactions:</strong> {{ $transactions->count() }}</p>
        <p><strong>Total Amount:</strong>
            {{ setting('currency_symbol', '$') }}{{ number_format($transactions->sum('amount'), 2) }}</p>
    </div>
@endsection