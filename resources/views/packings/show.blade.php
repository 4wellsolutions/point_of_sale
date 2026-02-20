@extends('layouts.app')

@section('title', 'Packing Details')

@section('page_title', 'Packing Details')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">{{ $packing->type }} ({{ $packing->unit_size }})</h3>
            <div>
                <a href="{{ route('packings.edit', $packing) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('packings.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <p><strong>Type:</strong> {{ $packing->type }}</p>
            <p><strong>Unit Size:</strong> {{ $packing->unit_size }}</p>
            <p><strong>Description:</strong> {{ $packing->description ?? 'N/A' }}</p>
            <p><strong>Created At:</strong> {{ $packing->created_at->format('d M Y, H:i') }}</p>
            <p><strong>Updated At:</strong> {{ $packing->updated_at->format('d M Y, H:i') }}</p>
        </div>
    </div>
@endsection
