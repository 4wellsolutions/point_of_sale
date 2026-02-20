@extends('layouts.app')

@section('title', 'Add Type')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('types.index') }}">Types</a></li>
    <li class="breadcrumb-item active">Add</li>
@endsection

@section('content')
    <!-- Add Type Form -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center py-2">
            <h3 class="card-title me-auto">Add New Type</h3>
            <a href="{{ route('types.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Types
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('types.store') }}" method="POST">
                @csrf

                <!-- Type Name -->
                <div class="mb-3">
                    <label for="name" class="form-label">Type Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Enter Type Name" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">Submit Type</button>
                <a href="{{ route('types.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
