@extends('layouts.app')

@section('title', 'Purchase Returns')

@section('page_title', 'Purchase Returns')

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('purchase-returns.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Purchase Return
            </a>
        </div>
        <div class="card-body">
            @if($purchaseReturns->count())
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Invoice No</th>
                            <th>Purchase No</th>
                            <th>Vendor</th>
                            <th>Return Date</th>
                            <th>Total Amount</th>
                            <th>Net Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseReturns as $return)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $return->invoice_no }}</td>
                                <td>{{ $return->purchase->invoice_no }}</td>
                                <td>{{ $return->vendor->name }}</td>
                                <td>{{ $return->return_date }}</td>
                                <td>{{ number_format($return->total_amount, 2) }}</td>
                                <td>{{ number_format($return->net_amount, 2) }}</td>
                                <td>
                                    <a href="{{ route('purchase-returns.show', $return->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('purchase-returns.edit', $return->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <!-- <form action="{{ route('purchase-returns.destroy', $return->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this purchase return?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form> -->
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $purchaseReturns->links() }}
            @else
                <p>No purchase returns found.</p>
            @endif
        </div>
    </div>
@endsection
