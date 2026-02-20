@extends('layouts.app')

@section('title', 'Packings')

@section('page_title', 'Packings')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center py-2">
            <a href="{{ route('packings.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Packing
            </a>
        </div>
        <div class="card-body">
            @if($packings->count())
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Unit Size</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($packings as $packing)
                            <tr>
                                <td>{{ $packing->id }}</td>
                                <td>{{ $packing->type }}</td>
                                <td>{{ $packing->unit_size }}</td>
                                <td>{{ $packing->description ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('packings.show', $packing) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('packings.edit', $packing) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('packings.destroy', $packing) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this packing?');">
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
                    {{ $packings->links('pagination::bootstrap-5') }}
                </div>
            @else
                <p>No packings found.</p>
            @endif
        </div>
    </div>
@endsection
