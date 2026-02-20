@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><i class="fas fa-user-circle mr-2"></i>My Profile</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Profile</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            <div class="row">
                <!-- Profile Info Card -->
                <div class="col-md-4">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile text-center">
                            <div class="profile-avatar mx-auto mb-3"
                                style="width:100px;height:100px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;">
                                <span
                                    style="font-size:40px;color:#fff;font-weight:bold;">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </div>
                            <h3 class="profile-username">{{ $user->name }}</h3>
                            <p class="text-muted mb-1">{{ $user->email }}</p>
                            @if($user->role)
                                <span class="badge badge-primary">{{ $user->role->name }}</span>
                            @endif

                            <ul class="list-group list-group-unbordered mt-3">
                                <li class="list-group-item">
                                    <b><i class="fas fa-envelope mr-2"></i>Email</b>
                                    <span class="float-right">{{ $user->email }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fas fa-phone mr-2"></i>Phone</b>
                                    <span class="float-right">{{ $user->phone ?? '—' }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fas fa-calendar mr-2"></i>Joined</b>
                                    <span class="float-right">{{ $user->created_at->format('d M Y') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Edit Forms -->
                <div class="col-md-8">
                    <!-- Update Profile -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-user-edit mr-2"></i>Update Profile</h3>
                        </div>
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group">
                                    <label><i class="fas fa-user mr-1"></i>Full Name</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $user->name) }}" required>
                                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-envelope mr-1"></i>Email Address</label>
                                    <input type="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $user->email) }}" required>
                                    @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-phone mr-1"></i>Phone</label>
                                    <input type="text" name="phone"
                                        class="form-control @error('phone') is-invalid @enderror"
                                        value="{{ old('phone', $user->phone) }}">
                                    @error('phone') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Save
                                    Changes</button>
                            </div>
                        </form>
                    </div>

                    <!-- Change Password -->
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-lock mr-2"></i>Change Password</h3>
                        </div>
                        <form action="{{ route('profile.password') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group">
                                    <label><i class="fas fa-key mr-1"></i>Current Password</label>
                                    <input type="password" name="current_password"
                                        class="form-control @error('current_password') is-invalid @enderror" required>
                                    @error('current_password') <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-lock mr-1"></i>New Password</label>
                                    <input type="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror" required>
                                    @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-check-circle mr-1"></i>Confirm New Password</label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning"><i class="fas fa-key mr-1"></i>Change
                                    Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection