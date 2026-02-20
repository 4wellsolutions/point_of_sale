@extends('layouts.app')

@section('title', 'Sales Returns')

@section('page_title', 'Sales Returns')

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('sales-returns.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Sales Return
            </a>
        </div>
        <div class="card-body">
            @if($salesReturns->count())
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Sale ID</th>
                            <th>Product</th>
                            <th>Customer</th>
                            <th>Quantity Returned</th>
                            <th>Return Reason</th>
                            <th>Refund Amount ({{ setting('currency_symbol', '$') }})</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesReturns as $return)
                            <tr>
                                <td>{{ $return->id }}</td>
                                <td>{{ $return->sale->id }}</td>
                                <td>{{ $return->sale->product->name ?? 'N/A' }}</td>
                                <td>{{ $return->sale->customer->name ?? 'N/A' }}</td>
                                <td>{{ $return->qty_returned }}</td>
                                <td>{{ $return->return_reason ?? 'N/A' }}</td>
                                <td>{{ setting('currency_symbol', '$') }}{{ number_format($return->refund_amount, 2) }}</td>
                                <td>
                                    <a href="{{ route('sales-returns.show', $return) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('sales-returns.edit', $return) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('sales-returns.destroy', $return) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this sales return?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" type="submit">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="d-flex justify-content-center my-3">
                    {{ $salesReturns->links('pagination::bootstrap-5') }}
                </div>
            @else
                <p>No sales returns found.</p>
            @endif
        </div>
    </div>
@endsection