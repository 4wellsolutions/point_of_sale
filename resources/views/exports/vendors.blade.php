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
            @foreach($vendors as $vendor)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="fw-bold">{{ $vendor->name }}</td>
                    <td>{{ $vendor->email ?? '—' }}</td>
                    <td>{{ $vendor->phone ?? '—' }}</td>
                    <td>{{ $vendor->type->name ?? '—' }}</td>
                    <td class="text-right">{{ setting('currency_symbol', '$') }}{{ number_format($vendor->balance ?? 0, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <p><strong>Total Vendors:</strong> {{ $vendors->count() }}</p>
    </div>
@endsection