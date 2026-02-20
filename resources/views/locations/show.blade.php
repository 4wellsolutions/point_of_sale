@extends('layouts.app')

@section('title', 'View Location')

@section('page_title', 'View Location')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5>{{ $location->name }}</h5>
        </div>
        <div class="card-body">
            <p><strong>Name:</strong> {{ $location->name }}</p>
            <p><strong>Description:</strong> {{ $location->description ?? 'N/A' }}</p>
            <p><strong>Created At:</strong> {{ $location->created_at->format('d M Y, h:i A') }}</p>
            <p><strong>Updated At:</strong> {{ $location->updated_at->format('d M Y, h:i A') }}</p>
            <a href="{{ route('locations.edit', $location->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('locations.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
@endsection
