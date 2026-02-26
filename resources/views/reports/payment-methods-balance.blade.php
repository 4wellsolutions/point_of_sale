@extends('layouts.app')
@section('title', 'Business Balance by Payment Method')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('reports.sales') }}">Reports</a></li>
    <li class="breadcrumb-item active">Business Balance</li>
@endsection

@section('content')
    <div class="row g-3 mb-3">
        {{-- Date filter --}}
        <div class="col-12">
            <form class="card card-body py-2" method="GET" action="{{ route('reports.payment-methods-balance') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label mb-1">Date From</label>
                        <input type="date" name="date_from" class="form-control form-control-sm"
                            value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1">Date To</label>
                        <input type="date" name="date_to" class="form-control form-control-sm"
                            value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button class="btn btn-primary btn-sm">Filter</button>
                        <a href="{{ route('reports.payment-methods-balance') }}" class="btn btn-secondary btn-sm">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Summary Cards --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Total Received</div>
                    <div class="fw-bold fs-4 text-success">Rs {{ format_number($totalReceived, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Total Paid Out</div>
                    <div class="fw-bold fs-4 text-danger">Rs {{ format_number($totalPaid, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Net Balance</div>
                    <div class="fw-bold fs-4 {{ $netBalance >= 0 ? 'text-success' : 'text-danger' }}">
                        Rs {{ format_number(abs($netBalance), 2) }}
                        <small class="fs-6">({{ $netBalance >= 0 ? 'Profit' : 'Loss' }})</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Payment Methods Breakdown --}}
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Balance by Payment Method</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Payment Method</th>
                                <th class="text-end">Received</th>
                                <th class="text-end">Paid Out</th>
                                <th class="text-end">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $row)
                                <tr>
                                    <td><i class="fas fa-wallet me-2 text-primary"></i>{{ $row->name }}</td>
                                    <td class="text-end text-success">Rs {{ format_number($row->received, 2) }}</td>
                                    <td class="text-end text-danger">Rs {{ format_number($row->paid, 2) }}</td>
                                    <td class="text-end fw-bold {{ $row->balance >= 0 ? 'text-success' : 'text-danger' }}">
                                        Rs {{ format_number(abs($row->balance), 2) }}
                                        {{ $row->balance < 0 ? '(-)' : '' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No transactions found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-secondary fw-bold">
                            <tr>
                                <td>Total</td>
                                <td class="text-end text-success">Rs {{ format_number($totalReceived, 2) }}</td>
                                <td class="text-end text-danger">Rs {{ format_number($totalPaid, 2) }}</td>
                                <td class="text-end {{ $netBalance >= 0 ? 'text-success' : 'text-danger' }}">
                                    Rs {{ format_number(abs($netBalance), 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Opening Balances Summary --}}
        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-users me-2"></i>Customer Opening Balances</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td>Receivable (Debit)</td>
                            <td class="text-end fw-bold text-success">Rs {{ format_number($custDebit, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Payable to Customers (Credit)</td>
                            <td class="text-end fw-bold text-danger">Rs {{ format_number($custCredit, 2) }}</td>
                        </tr>
                        <tr class="table-light fw-bold">
                            <td>Net Customer Balance</td>
                            @php $custNet = $custDebit - $custCredit; @endphp
                            <td class="text-end {{ $custNet >= 0 ? 'text-success' : 'text-danger' }}">
                                Rs {{ format_number(abs($custNet), 2) }}
                                {{ $custNet < 0 ? '(-)' : '' }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-truck me-2"></i>Vendor Opening Balances</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td>Payable to Vendors (Credit)</td>
                            <td class="text-end fw-bold text-danger">Rs {{ format_number($vendCredit, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Receivable (Debit)</td>
                            <td class="text-end fw-bold text-success">Rs {{ format_number($vendDebit, 2) }}</td>
                        </tr>
                        <tr class="table-light fw-bold">
                            <td>Net Vendor Balance</td>
                            @php $vendNet = $vendDebit - $vendCredit; @endphp
                            <td class="text-end {{ $vendNet >= 0 ? 'text-success' : 'text-danger' }}">
                                Rs {{ format_number(abs($vendNet), 2) }}
                                {{ $vendNet < 0 ? '(-)' : '' }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection