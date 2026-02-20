@extends('layouts.app')

@section('title', 'Product Details')

@section('page_title', 'Product Details')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center py-2">
            <h3 class="card-title me-auto">{{ $product->name }}</h3>
            <div class="card-tools">
                <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>SKU:</strong> {{ $product->sku }}<br>
                    <strong>Description:</strong> {{ $product->description ?? 'N/A' }}<br>
                    <strong>Flavour:</strong> {{ $product->flavour->name }}<br>
                    <strong>Packing:</strong> {{ $product->packing->type }} ({{ $product->packing->unit_size }})<br>
                    <strong>Category:</strong> {{ $product->category ? $product->category->name : 'N/A' }}<br>
                    <strong>Barcode:</strong> {{ $product->barcode ?? 'N/A' }}<br>
                    <strong>Weight:</strong> {{ $product->weight ?? 'N/A' }}<br>
                    <strong>Volume:</strong> {{ $product->volume ?? 'N/A' }}<br>
                    <strong>GST:</strong> {{ $product->gst ?? 'N/A' }}%<br>
                    <strong>Status:</strong> 
                        @if($product->status == 'active')
                            <span class="badge bg-success">Active</span>
                        @elseif($product->status == 'inactive')
                            <span class="badge bg-secondary">Inactive</span>
                        @else
                            <span class="badge bg-danger">Discontinued</span>
                        @endif
                    <br>
                    <strong>Reorder Level:</strong> {{ $product->reorder_level }}<br>
                    <strong>Max Stock Level:</strong> {{ $product->max_stock_level }}<br>
                    <strong>Created At:</strong> {{ $product->created_at->format('d M Y, H:i') }}<br>
                    <strong>Updated At:</strong> {{ $product->updated_at->format('d M Y, H:i') }}<br>
                </div>
                <div class="col-md-6 text-center">
                    <strong>Product Image:</strong><br>
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="Product Image" class="img-fluid" style="max-width: 100%; max-height: 300px;">
                    @else
                        <p>No image available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Add a section for Batches -->
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Batches</h3>
        </div>
        <div class="card-body">
            @if($product->purchases->count())
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Batch No</th>
                            <th>Purchase Date</th>
                            <th>Qty Received</th>
                            <th>Qty Returned</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($product->purchases as $purchase)
                            <tr>
                                <td>{{ $purchase->batch->batch_no }}</td>
                                <td>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</td>
                                <td>{{ $purchase->qty_received }}</td>
                                <td>{{ $purchase->purchaseReturns->sum('qty_returned') }}</td>
                                <td>{{ $purchase->expiry_date ? $purchase->expiry_date->format('d M Y') : 'N/A' }}</td>
                                <td>
                                    @if($purchase->expiry_date && $purchase->expiry_date->isPast())
                                        <span class="badge bg-danger">Expired</span>
                                    @else
                                        <span class="badge bg-success">Active</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No batches available for this product.</p>
            @endif
        </div>
    </div>

@endsection
