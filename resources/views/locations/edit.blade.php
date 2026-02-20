@extends('layouts.app')

@section('title', 'Edit Location')

@section('page_title', 'Edit Location')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('locations.update', $location->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Location Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" required value="{{ old('name', $location->name) }}">
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $location->description) }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-save"></i> Update Location
                </button>
                <a href="{{ route('locations.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </form>
        </div>
    </div>
@endsection
