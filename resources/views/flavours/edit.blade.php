@extends('layouts.app')

@section('title', 'Edit Flavour')

@section('page_title', 'Edit Flavour')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('flavours.update', $flavour) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Flavour Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name', $flavour->name) }}">
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4">{{ old('description', $flavour->description) }}</textarea>
                </div>
                <button class="btn btn-success" type="submit">
                    <i class="fas fa-save"></i> Update Flavour
                </button>
                <a href="{{ route('flavours.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </form>
        </div>
    </div>
@endsection
