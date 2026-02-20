@extends('layouts.app')

@section('title', 'Flavours')

@section('page_title', 'Flavours')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center py-2">
            <a href="{{ route('flavours.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Flavour
            </a>
        </div>
        <div class="card-body">
            @if($flavours->count())
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
                        @foreach($flavours as $flavour)
                            <tr>
                                <td>{{ $flavour->id }}</td>
                                <td>{{ $flavour->name }}</td>
                                <td>{{ $flavour->description ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('flavours.show', $flavour) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('flavours.edit', $flavour) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('flavours.destroy', $flavour) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this flavour?');">
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
                    {{ $flavours->links('pagination::bootstrap-5') }}
                </div>
            @else
                <p>No flavours found.</p>
            @endif
        </div>
    </div>
@endsection
