@extends('layouts.app')

@section('title', 'Edit Category')

@section('page_title', 'Edit Category')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name', $category->name) }}">
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4">{{ old('description', $category->description) }}</textarea>
                </div>
                <button class="btn btn-success" type="submit">
                    <i class="fas fa-save"></i> Update Category
                </button>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </form>
        </div>
    </div>
@endsection
