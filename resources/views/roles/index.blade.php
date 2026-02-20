@extends('layouts.app')

@section('title', 'Roles')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Roles</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-user-shield me-2"></i>All Roles</h5>
            <a href="{{ route('roles.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>Add
                Role</a>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Role Name</th>
                        <th>Type</th>
                        <th>Users</th>
                        <th>Modules</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $role->name }}</strong></td>
                            <td>
                                @if($role->is_admin)
                                    <span class="badge bg-danger">Admin</span>
                                @else
                                    <span class="badge bg-primary">Standard</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $role->users_count }}
                                    user{{ $role->users_count != 1 ? 's' : '' }}</span>
                            </td>
                            <td>
                                @if($role->is_admin)
                                    <span class="badge bg-success">All Modules</span>
                                @else
                                    @forelse($role->modules as $module)
                                        <span class="badge bg-outline-secondary border text-dark me-1 mb-1" style="font-weight:normal;">
                                            <i class="{{ $module->icon }} me-1" style="font-size:10px;"></i>{{ $module->label }}
                                        </span>
                                    @empty
                                        <span class="text-muted">No modules assigned</span>
                                    @endforelse
                                @endif
                            </td>
                            <td class="text-center action-btns">
                                @if(!$role->is_admin)
                                    <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-warning" title="Edit"><i
                                            class="fas fa-edit"></i></a>
                                    @if($role->users_count === 0)
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this role?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i
                                                    class="fas fa-trash"></i></button>
                                        </form>
                                    @endif
                                @else
                                    <span class="text-muted"><i class="fas fa-lock"></i></span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No roles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection