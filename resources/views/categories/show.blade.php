@extends('layouts.app')

@section('title', 'Category Details')

@section('page_title', 'Category Details')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">{{ $category->name }}</h3>
            <div>
                <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <p><strong>Name:</strong> {{ $category->name }}</p>
            <p><strong>Description:</strong> {{ $category->description ?? 'N/A' }}</p>
            <p><strong>Created At:</strong> {{ $category->created_at->format('d M Y, H:i') }}</p>
            <p><strong>Updated At:</strong> {{ $category->updated_at->format('d M Y, H:i') }}</p>
        </div>
    </div>
@endsection
