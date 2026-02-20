@extends('layouts.app')

@section('title', 'Edit Type')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('types.index') }}">Types</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <!-- Edit Type Form -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center py-2">
            <h3 class="card-title me-auto">Edit Type</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('types.update', $type->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Type Name -->
                <div class="mb-3">
                    <label for="name" class="form-label">Type Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $type->name) }}" placeholder="Enter Type Name" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">Update Type</button>
                <a href="{{ route('types.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
