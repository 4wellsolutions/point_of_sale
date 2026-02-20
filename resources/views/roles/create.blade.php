@extends('layouts.app')

@section('title', 'Create Role')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
    <li class="breadcrumb-item active">Create Role</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user-shield me-2"></i>Create New Role</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('roles.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" placeholder="e.g. Cashier, Manager, Viewer"
                                required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label d-block">Module Access</label>
                            <p class="text-muted small mb-3">Select which modules this role can access:</p>

                            <div class="row">
                                @foreach($modules as $module)
                                    <div class="col-md-4 col-sm-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="modules[]"
                                                value="{{ $module->id }}" id="module_{{ $module->id }}"
                                                {{ in_array($module->id, old('modules', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="module_{{ $module->id }}">
                                                <i class="{{ $module->icon }} me-1 text-muted"></i>
                                                {{ $module->label }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @error('modules')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="selectAll">Select
                                    All</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAll">Deselect
                                    All</button>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Create
                                    Role</button>
                                <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('selectAll').addEventListener('click', function() {
            document.querySelectorAll('input[name="modules[]"]').forEach(cb => cb.checked = true);
        });
        document.getElementById('deselectAll').addEventListener('click', function() {
            document.querySelectorAll('input[name="modules[]"]').forEach(cb => cb.checked = false);
        });
    </script>
@endpush
