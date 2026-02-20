@extends('layouts.app')

@section('title', 'Users')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Users</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-users-cog me-2"></i>All Users</h5>
            <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>Add
                User</a>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                            <td>
                                <strong>{{ $user->name }}</strong>
                                @if($user->id === auth()->id())
                                    <span class="badge bg-info ms-1">You</span>
                                @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role)
                                    <span class="badge {{ $user->role->is_admin ? 'bg-danger' : 'bg-primary' }}">
                                        {{ $user->role->name }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">No Role</span>
                                @endif
                            </td>
                            <td><small class="text-muted">{{ $user->created_at->format('d M Y') }}</small></td>
                            <td class="text-center action-btns">
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning" title="Edit"><i
                                        class="fas fa-edit"></i></a>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i
                                                class="fas fa-trash"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="card-footer">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection