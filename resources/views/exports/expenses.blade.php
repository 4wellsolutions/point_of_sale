@extends('exports.layout')
@section('content')
    <table>
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
            @foreach($expenses as $expense)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($expense->date)->format('d M Y') }}</td>
                    <td>{{ $expense->expenseType->name ?? '—' }}</td>
                    <td>{{ $expense->description }}</td>
                    <td class="text-right fw-bold">${{ number_format($expense->amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <p><strong>Total Expenses:</strong> {{ $expenses->count() }}</p>
        <p><strong>Total Amount:</strong> ${{ number_format($expenses->sum('amount'), 2) }}</p>
    </div>
@endsection