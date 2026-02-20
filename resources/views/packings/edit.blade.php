@extends('layouts.app')

@section('title', 'Edit Packing')

@section('page_title', 'Edit Packing')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('packings.update', $packing) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="type" class="form-label">Packing Type <span class="text-danger">*</span></label>
                    <input type="text" name="type" class="form-control" required value="{{ old('type', $packing->type) }}">
                </div>
                <div class="mb-3">
                    <label for="unit_size" class="form-label">Unit Size <span class="text-danger">*</span></label>
                    <input type="text" name="unit_size" class="form-control" required value="{{ old('unit_size', $packing->unit_size) }}">
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4">{{ old('description', $packing->description) }}</textarea>
                </div>
                <button class="btn btn-success" type="submit">
                    <i class="fas fa-save"></i> Update Packing
                </button>
                <a href="{{ route('packings.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </form>
        </div>
    </div>
@endsection
