@extends('layouts.app')

@section('title', 'Categories')

@section('page_title', 'Categories')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center py-2">
            <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Category
            </a>
        </div>
        <div class="card-body">
            @if($categories->count())
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->description ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('categories.show', $category) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" type="submit">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <div class="d-flex justify-content-center my-3">
                    {{ $categories->links('pagination::bootstrap-5') }}
                </div>
            @else
                <p>No categories found.</p>
            @endif
        </div>
    </div>
@endsection
