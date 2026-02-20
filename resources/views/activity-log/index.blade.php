@extends('layouts.app')
@section('title', 'Activity Log')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-history mr-2"></i>Activity Log</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Activity Log</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <!-- Filters Card -->
        <div class="card card-outline card-primary collapsed-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter mr-1"></i>Filters</h3>
                <div class="card-tools">
                    @if(request()->hasAny(['user_id', 'event', 'auditable_type', 'date_from', 'date_to']))
                        <a href="{{ route('activity-log.index') }}" class="btn btn-tool text-danger" title="Clear Filters">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    @endif
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('activity-log.index') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><i class="fas fa-user mr-1"></i>User</label>
                                <select name="user_id" class="form-control select2" style="width:100%">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label><i class="fas fa-bolt mr-1"></i>Action</label>
                                <select name="event" class="form-control">
                                    <option value="">All Actions</option>
                                    <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Created</option>
                                    <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Updated</option>
                                    <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                                    <option value="login" {{ request('event') == 'login' ? 'selected' : '' }}>Login</option>
                                    <option value="logout" {{ request('event') == 'logout' ? 'selected' : '' }}>Logout</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label><i class="fas fa-cube mr-1"></i>Model</label>
                                <select name="auditable_type" class="form-control">
                                    <option value="">All Models</option>
                                    @foreach($modelTypes as $type)
                                        <option value="{{ $type['value'] }}" {{ request('auditable_type') == $type['value'] ? 'selected' : '' }}>
                                            {{ $type['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label><i class="fas fa-calendar mr-1"></i>From</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label><i class="fas fa-calendar mr-1"></i>To</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list mr-1"></i>
                    {{ $audits->total() }} {{ Str::plural('record', $audits->total()) }}
                </h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th style="width:160px">Date & Time</th>
                            <th style="width:120px">User</th>
                            <th style="width:90px">Action</th>
                            <th style="width:120px">Model</th>
                            <th style="width:60px">ID</th>
                            <th>Changes</th>
                            <th style="width:110px">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($audits as $audit)
                        <tr>
                            <td>
                                <small>
                                    <i class="far fa-clock mr-1"></i>
                                    {{ $audit->created_at->format('d M Y') }}<br>
                                    <span class="text-muted ml-3">{{ $audit->created_at->format('h:i:s A') }}</span>
                                </small>
                            </td>
                            <td>
                                @if($audit->user)
                                    <span class="badge badge-light">
                                        <i class="fas fa-user mr-1"></i>{{ $audit->user->name }}
                                    </span>
                                @else
                                    <span class="badge badge-secondary">System</span>
                                @endif
                            </td>
                            <td>
                                @if($audit->event === 'created')
                                    <span class="badge badge-success"><i class="fas fa-plus mr-1"></i>Created</span>
                                @elseif($audit->event === 'updated')
                                    <span class="badge badge-info"><i class="fas fa-edit mr-1"></i>Updated</span>
                                @elseif($audit->event === 'deleted')
                                    <span class="badge badge-danger"><i class="fas fa-trash mr-1"></i>Deleted</span>
                                @elseif($audit->event === 'login')
                                    <span class="badge" style="background:#10b981;color:#fff"><i class="fas fa-sign-in-alt mr-1"></i>Login</span>
                                @elseif($audit->event === 'logout')
                                    <span class="badge" style="background:#f59e0b;color:#fff"><i class="fas fa-sign-out-alt mr-1"></i>Logout</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($audit->event) }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-primary">{{ class_basename($audit->auditable_type) }}</span>
                            </td>
                            <td>
                                <code>#{{ $audit->auditable_id }}</code>
                            </td>
                            <td>
                                @if($audit->event === 'created')
                                    @php $newVals = $audit->new_values; @endphp
                                    @if(!empty($newVals))
                                        <div class="changes-scroll">
                                            @foreach(array_slice($newVals, 0, 4) as $key => $val)
                                                <small>
                                                    <strong>{{ Str::title(str_replace('_', ' ', $key)) }}:</strong>
                                                    <span class="text-success">{{ Str::limit(is_array($val) ? json_encode($val) : (string)$val, 50) }}</span>
                                                </small><br>
                                            @endforeach
                                            @if(count($newVals) > 4)
                                                <small class="text-muted">+{{ count($newVals) - 4 }} more fields</small>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                @elseif($audit->event === 'updated')
                                    @php $oldVals = $audit->old_values; $newVals = $audit->new_values; @endphp
                                    @if(!empty($oldVals))
                                        <div class="changes-scroll">
                                            @foreach(array_slice($oldVals, 0, 4, true) as $key => $old)
                                                <small>
                                                    <strong>{{ Str::title(str_replace('_', ' ', $key)) }}:</strong>
                                                    <span class="text-danger">{{ Str::limit(is_array($old) ? json_encode($old) : (string)$old, 30) }}</span>
                                                    <i class="fas fa-arrow-right mx-1 text-muted" style="font-size:10px"></i>
                                                    <span class="text-success">{{ Str::limit(is_array($newVals[$key] ?? '') ? json_encode($newVals[$key] ?? '') : (string)($newVals[$key] ?? ''), 30) }}</span>
                                                </small><br>
                                            @endforeach
                                            @if(count($oldVals) > 4)
                                                <small class="text-muted">+{{ count($oldVals) - 4 }} more changes</small>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                @elseif($audit->event === 'deleted')
                                    @php $oldVals = $audit->old_values; @endphp
                                    @if(!empty($oldVals))
                                        <div class="changes-scroll">
                                            @foreach(array_slice($oldVals, 0, 3) as $key => $val)
                                                <small>
                                                    <strong>{{ Str::title(str_replace('_', ' ', $key)) }}:</strong>
                                                    <span class="text-danger">{{ Str::limit(is_array($val) ? json_encode($val) : (string)$val, 50) }}</span>
                                                </small><br>
                                            @endforeach
                                            @if(count($oldVals) > 3)
                                                <small class="text-muted">+{{ count($oldVals) - 3 }} more fields</small>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $audit->ip_address ?? '—' }}</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted">No activity logs found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($audits->hasPages())
            <div class="card-footer clearfix">
                {{ $audits->links() }}
            </div>
            @endif
        </div>
    </div>
</section>

<style>
.changes-scroll {
    max-height: 80px;
    overflow-y: auto;
}
</style>
@endsection
