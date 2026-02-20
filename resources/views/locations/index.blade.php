@extends('layouts.app')

@section('title', 'Locations')

@section('page_title', 'Locations')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Locations List</h5>
            <a href="{{ route('locations.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Location
            </a>
        </div>
        <div class="card-body">
            @if($locations->isEmpty())
                <p>No locations found.</p>
            @else
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
                        @foreach($locations as $location)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $location->name }}</td>
                                <td>{{ $location->description }}</td>
                                <td>
                                    <a href="{{ route('locations.show', $location->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('locations.edit', $location->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('locations.destroy', $location->id) }}" method="POST"
                                        style="display:inline-block;"
                                        onsubmit="return confirm('Are you sure you want to delete this location?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" type="submit">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection