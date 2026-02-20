@extends('layouts.app')

@section('title', 'Types')

@section('breadcrumb')
    <li class="breadcrumb-item active">Types</li>
@endsection

@section('content')
    <!-- Types List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center py-2">
            <h3 class="card-title me-auto">Types</h3>
            <a href="{{ route('types.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Type</a>
        </div>
        <div class="card-body">
            @if($types->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Associated With</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($types as $type)
                                <tr>
                                    <td>{{ ($types->currentPage() - 1) * $types->perPage() + $loop->iteration }}</td>
                                    <td>{{ $type->name }}</td>
                                    <td>
                                        @if($type->customers()->exists() || $type->vendors()->exists())
                                            <span class="badge bg-warning text-dark">In Use</span>
                                        @else
                                            <span class="badge bg-success">Not in Use</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('types.edit', $type->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('types.destroy', $type->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this Customer?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                <div class="d-flex justify-content-center my-3">
                    {{ $types->links('pagination::bootstrap-5') }}
                </div>
            @else
                <p class="text-center">No Types found. <a href="{{ route('types.create') }}">Add a new Type</a>.</p>
            @endif
        </div>
    </div>
@endsection
