@extends('layouts.app')
@section('title', 'Bookings Report')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Bookings Report</li>
@endsection

@section('content')
    <!-- Summary Cards -->
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="kpi-card kpi-sales">
                <div class="kpi-icon"><i class="fas fa-clipboard-list"></i></div>
                <div class="kpi-value">{{ format_number($totalCount) }}</div>
                <div class="kpi-label">Total Bookings</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card kpi-revenue">
                <div class="kpi-icon"><i class="fas fa-coins"></i></div>
                <div class="kpi-value">{{ setting('currency_symbol') }}{{ format_number($totalAmount) }}</div>
                <div class="kpi-label">Total Amount</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card kpi-expenses">
                <div class="kpi-icon"><i class="fas fa-hourglass-half"></i></div>
                <div class="kpi-value">{{ format_number($pendingCount) }}</div>
                <div class="kpi-label">Pending</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: #fff;">
                <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
                <div class="kpi-value">{{ format_number($convertedCount) }}</div>
                <div class="kpi-label">Converted to Sale</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.bookings') }}">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Customer</label>
                        <select name="customer_id" class="form-control">
                            <option value="">All Customers</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converted
                            </option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">From Date</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">To Date</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3 d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filter</button>
                        <a href="{{ route('reports.bookings') }}" class="btn btn-outline-secondary"><i
                                class="fas fa-times me-1"></i>Reset</a>
                        <a href="{{ route('reports.bookings.pdf') }}?{{ http_build_query(request()->query()) }}"
                            target="_blank" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf me-1"></i>PDF</a>
                        <a href="{{ route('reports.bookings.csv') }}?{{ http_build_query(request()->query()) }}"
                            class="btn btn-success btn-sm"><i class="fas fa-file-excel me-1"></i>Excel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-table me-2"></i>Bookings Data</h5>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-center">Items</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Discount</th>
                        <th class="text-end">Net Amount</th>
                        <th>Created By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td>{{ $loop->iteration + ($bookings->currentPage() - 1) * $bookings->perPage() }}</td>
                            <td>
                                <a href="{{ route('bookings.show', $booking->id) }}">
                                    <strong>{{ $booking->invoice_no }}</strong>
                                </a>
                            </td>
                            <td>{{ $booking->customer->name ?? '—' }}</td>
                            <td><small
                                    class="text-muted">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</small>
                            </td>
                            <td>
                                @if($booking->status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($booking->status === 'converted')
                                    <span class="badge bg-success">Converted</span>
                                @elseif($booking->status === 'cancelled')
                                    <span class="badge bg-danger">Cancelled</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($booking->status) }}</span>
                                @endif
                            </td>
                            <td class="text-center"><span class="badge bg-primary">{{ $booking->items->count() }}</span></td>
                            <td class="text-end">
                                {{ setting('currency_symbol') }}{{ format_number($booking->total_amount ?? 0) }}</td>
                            <td class="text-end">
                                {{ setting('currency_symbol') }}{{ format_number($booking->discount_amount ?? 0) }}</td>
                            <td class="text-end">
                                <strong>{{ setting('currency_symbol') }}{{ format_number($booking->net_amount ?? 0) }}</strong>
                            </td>
                            <td><small>{{ $booking->user->name ?? '—' }}</small></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">No bookings found for the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bookings->hasPages())
            <div class="card-footer">{{ $bookings->links() }}</div>
        @endif
    </div>
@endsection