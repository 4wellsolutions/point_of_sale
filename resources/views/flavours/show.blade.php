@extends('layouts.app')

@section('title', 'Flavour Details')

@section('page_title', 'Flavour Details')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center py-2">
            <h3 class="card-title me-auto">{{ $flavour->name }}</h3>
            <div>
                <a href="{{ route('flavours.edit', $flavour) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('flavours.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <p><strong>Name:</strong> {{ $flavour->name }}</p>
            <p><strong>Description:</strong> {{ $flavour->description ?? 'N/A' }}</p>
            <p><strong>Created At:</strong> {{ $flavour->created_at->format('d M Y, H:i') }}</p>
            <p><strong>Updated At:</strong> {{ $flavour->updated_at->format('d M Y, H:i') }}</p>
        </div>
    </div>
@endsection
