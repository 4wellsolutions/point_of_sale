@extends('layouts.app')
@section('title', 'Areas')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Areas</li>
@endsection

@section('content')
    <div class="row g-3">
        {{-- Add New Area --}}
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Add New Area</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('areas.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Area Name *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" placeholder="e.g. North Zone" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-plus me-1"></i>Add
                            Area</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Areas List --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>All Areas</h5>
                    <span class="badge bg-primary">{{ $areas->total() }}</span>
                </div>
                <div class="card-body p-0">
                    @if(session('success'))
                        <div class="alert alert-success m-3">{{ session('success') }}</div>
                    @endif
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Customers</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($areas as $area)
                                <tr>
                                    <td>{{ $loop->iteration + ($areas->currentPage() - 1) * $areas->perPage() }}</td>
                                    <td>
                                        <form action="{{ route('areas.update', $area) }}" method="POST"
                                            class="d-flex gap-2 align-items-center">
                                            @csrf @method('PUT')
                                            <input type="text" name="name" value="{{ $area->name }}"
                                                class="form-control form-control-sm" style="max-width:200px" required>
                                            <button type="submit" class="btn btn-sm btn-outline-primary" title="Save">
                                                <i class="fas fa-save"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td><span class="badge bg-secondary">{{ $area->customers_count }}</span></td>
                                    <td class="text-center">
                                        <form action="{{ route('areas.destroy', $area) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Delete this area?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No areas yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if($areas->hasPages())
                        <div class="p-3">{{ $areas->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection