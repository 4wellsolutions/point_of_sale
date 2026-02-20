@extends('layouts.app')

@section('title', 'Sales Return Details')

@section('page_title', 'Sales Return Details')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Sales Return #{{ $salesReturn->id }}</h3>
            <div>
                <a href="{{ route('sales-returns.edit', $salesReturn) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('sales-returns.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <p><strong>Sale ID:</strong> {{ $salesReturn->sale->id }}</p>
            <p><strong>Product:</strong> {{ $salesReturn->sale->product->name ?? 'N/A' }}</p>
            <p><strong>Customer:</strong> {{ $salesReturn->sale->customer->name ?? 'N/A' }}</p>
            <p><strong>Quantity Returned:</strong> {{ $salesReturn->qty_returned }}</p>
            <p><strong>Return Reason:</strong> {{ $salesReturn->return_reason ?? 'N/A' }}</p>
            <p><strong>Refund Amount:</strong>
                {{ setting('currency_symbol', '$') }}{{ number_format($salesReturn->refund_amount, 2) }}</p>
            <p><strong>Created At:</strong> {{ $salesReturn->created_at->format('d M Y, H:i') }}</p>
            <p><strong>Updated At:</strong> {{ $salesReturn->updated_at->format('d M Y, H:i') }}</p>
        </div>
    </div>
@endsection