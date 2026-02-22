@extends('layouts.app')
@section('title', 'Activity Log')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">Settings</a></li>
    <li class="breadcrumb-item active">Activity Log</li>
@endsection

@section('content')

    {{-- Filters --}}
    <form class="card card-body mb-3 py-2" method="GET">
        <div class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label mb-1 small">User</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">All Users</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label mb-1 small">Event</label>
                <select name="event" class="form-select form-select-sm">
                    <option value="">All Events</option>
                    <option value="created" {{ request('event') === 'created' ? 'selected' : '' }}>Created</option>
                    <option value="updated" {{ request('event') === 'updated' ? 'selected' : '' }}>Updated</option>
                    <option value="deleted" {{ request('event') === 'deleted' ? 'selected' : '' }}>Deleted</option>
                    <option value="restored" {{ request('event') === 'restored' ? 'selected' : '' }}>Restored</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label mb-1 small">Model</label>
                <input type="text" name="model" class="form-control form-control-sm" placeholder="e.g. Sale, Customer"
                    value="{{ request('model') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label mb-1 small">Date From</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label mb-1 small">Date To</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-primary btn-sm flex-fill">Filter</button>
                <a href="{{ route('activity-log.index') }}" class="btn btn-secondary btn-sm">Reset</a>
            </div>
        </div>
    </form>

    {{-- Log Table --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-history me-2"></i>Activity Log</h5>
            <span class="badge bg-primary">{{ $logs->total() }} records</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width:150px">Date / Time</th>
                            <th style="width:120px">User</th>
                            <th style="width:80px">Event</th>
                            <th style="width:130px">Model</th>
                            <th style="width:60px">ID</th>
                            <th>Old Values</th>
                            <th>New Values</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="text-nowrap small">{{ $log->created_at->format('d M Y H:i') }}</td>
                                <td class="small">{{ $log->user?->name ?? '<em class="text-muted">System</em>' }}</td>
                                <td>
                                    @php
                                        $badge = match ($log->event) {
                                            'created' => 'success',
                                            'updated' => 'warning',
                                            'deleted' => 'danger',
                                            'restored' => 'info',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">{{ ucfirst($log->event) }}</span>
                                </td>
                                <td class="small text-muted">{{ class_basename($log->auditable_type) }}</td>
                                <td class="small">{{ $log->auditable_id }}</td>
                                <td>
                                    @if($log->old_values)
                                        <ul class="list-unstyled mb-0 small">
                                            @foreach($log->old_values as $k => $v)
                                                <li><span class="fw-semibold">{{ $k }}:</span> <span
                                                        class="text-danger">{{ is_array($v) ? json_encode($v) : $v }}</span></li>
                                            @endforeach
                                        </ul>
                                    @else <span class="text-muted">—</span> @endif
                                </td>
                                <td>
                                    @if($log->new_values)
                                        <ul class="list-unstyled mb-0 small">
                                            @foreach($log->new_values as $k => $v)
                                                <li><span class="fw-semibold">{{ $k }}:</span> <span
                                                        class="text-success">{{ is_array($v) ? json_encode($v) : $v }}</span></li>
                                            @endforeach
                                        </ul>
                                    @else <span class="text-muted">—</span> @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-history fa-2x mb-2 d-block"></i>No activity records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
            <div class="card-footer">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection