@extends('exports.layout')
@section('content')
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Type</th>
                <th class="text-right">Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $customer)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="fw-bold">{{ $customer->name }}</td>
                    <td>{{ $customer->email ?? '—' }}</td>
                    <td>{{ $customer->phone ?? '—' }}</td>
                    <td>{{ $customer->type->name ?? '—' }}</td>
                    <td class="text-right">{{ setting('currency_symbol', '$') }}{{ number_format($customer->balance ?? 0, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <p><strong>Total Customers:</strong> {{ $customers->count() }}</p>
    </div>
@endsection