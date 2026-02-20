@extends('layouts.app')

@section('title', 'Stock Adjustments')

@section('content_header')
    <h1>Stock Adjustments</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('stock_adjustments.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Stock Adjustment
            </a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Reason</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockAdjustments as $adjustment)
                        <tr>
                            <td>{{ $adjustment->id }}</td>
                            <td>{{ $adjustment->product->name }}</td>
                            <td>{{ ucfirst($adjustment->Type) }}</td>
                            <td>{{ $adjustment->quantity }}</td>
                            <td>{{ $adjustment->reason }}</td>
                            <td>{{ \Carbon\Carbon::parse($adjustment->date)->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('stock_adjustments.show', $adjustment->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('stock_adjustments.edit', $adjustment->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('stock_adjustments.destroy', $adjustment->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this adjustment?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>                                
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">No stock adjustments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <!-- Pagination Links -->
            <div class="mt-3">
                {{ $stockAdjustments->links() }}
            </div>
        </div>
    </div>
@endsection
