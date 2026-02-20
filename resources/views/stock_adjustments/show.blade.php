@extends('layouts.app')

@section('title', 'View Stock Adjustment')

@section('content_header')
    <h1>View Stock Adjustment</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('stock_adjustments.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <strong>ID:</strong> {{ $stockAdjustment->id }}
            </div>
            <div class="mb-3">
                <strong>Purchase:</strong> {{ $stockAdjustment->purchase_id }}
            </div>
            <div class="mb-3">
                <strong>Adjustment Type:</strong> {{ ucfirst($stockAdjustment->adjustment_type) }}
            </div>
            <div class="mb-3">
                <strong>Quantity:</strong> {{ $stockAdjustment->quantity }}
            </div>
            <div class="mb-3">
                <strong>Reason:</strong> {{ $stockAdjustment->reason }}
            </div>
            <div class="mb-3">
                <strong>Date:</strong> {{ \Carbon\Carbon::parse($stockAdjustment->date)->format('Y-m-d') }}
            </div>
            <div class="mb-3">
                <strong>Created At:</strong> {{ $stockAdjustment->created_at->format('Y-m-d H:i') }}
            </div>
            <div class="mb-3">
                <strong>Updated At:</strong> {{ $stockAdjustment->updated_at->format('Y-m-d H:i') }}
            </div>
        </div>
    </div>
@endsection
