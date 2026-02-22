@extends('layouts.app')

@section('title', 'Sales')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Sales</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-cash-register me-2"></i>All Sales</h5>
            <div class="d-flex gap-2 align-items-center export-buttons">
                <button type="button" class="btn btn-sm btn-outline-primary d-none" id="multiPrintBtn"
                    data-bs-toggle="modal" data-bs-target="#multiPrintModal">
                    <i class="fas fa-print me-1"></i>Multi-Print (<span id="selectedCount">0</span>)
                </button>
                <a href="{{ route('sales.export.pdf', request()->query()) }}" class="btn btn-sm btn-outline-secondary"
                    title="Export PDF" target="_blank">
                    <i class="fas fa-file-pdf me-1"></i>PDF
                </a>
                <a href="{{ route('sales.export.csv', request()->query()) }}" class="btn btn-sm btn-outline-secondary"
                    title="Export CSV">
                    <i class="fas fa-file-csv me-1"></i>CSV
                </a>
                <a href="{{ route('sales.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i>New Sale
                </a>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar mx-3 mt-3">
            <form action="{{ route('sales.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Customer</label>
                        <select name="customer_id" class="form-select form-select-sm">
                            <option value="">All Customers</option>
                            @foreach($customers ?? [] as $customer)
                                <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Invoice No.</label>
                        <input type="text" name="invoice_no" class="form-control form-control-sm"
                            value="{{ request('invoice_no') }}" placeholder="Invoice #">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">From Date</label>
                        <input type="date" name="from_date" class="form-control form-control-sm"
                            value="{{ request('from_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">To Date</label>
                        <input type="date" name="to_date" class="form-control form-control-sm"
                            value="{{ request('to_date') }}">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary"><i
                                class="fas fa-search me-1"></i>Filter</button>
                        <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-secondary"><i
                                class="fas fa-times"></i></a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            @if($sales->count() > 0)
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th style="width:36px">
                                    <input type="checkbox" id="selectAll" title="Select all">
                                </th>
                                <th>#</th>
                                <th>Invoice No</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Discount</th>
                                <th class="text-end">Net Amount</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales as $sale)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="sale-cb" value="{{ route('sales.pdf', $sale->id) }}">
                                    </td>
                                    <td>{{ $loop->iteration + ($sales->currentPage() - 1) * $sales->perPage() }}</td>
                                    <td><span class="badge bg-secondary">{{ $sale->invoice_no }}</span></td>
                                    <td><strong>{{ $sale->customer->name ?? 'Walk-in' }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</td>
                                    <td class="text-end">
                                        {{ setting('currency_symbol', '$') }}{{ number_format($sale->total_amount, 2) }}</td>
                                    <td class="text-end">
                                        {{ setting('currency_symbol', '$') }}{{ number_format($sale->discount_amount, 2) }}</td>
                                    <td class="text-end fw-bold">
                                        {{ setting('currency_symbol', '$') }}{{ number_format($sale->net_amount, 2) }}</td>
                                    <td class="text-center action-btns">
                                        <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('sales.pdf', $sale->id) }}" class="btn btn-sm btn-secondary" title="PDF"
                                            target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger" onclick="confirmDelete({{ $sale->id }})"
                                            title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center p-3">
                    {{ $sales->withQueryString()->links() }}
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-cash-register"></i>
                    <p>No sales found</p>
                    <a href="{{ route('sales.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>New Sale
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">Are you sure you want to delete this sale?</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash me-1"></i>Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- MULTI-PRINT MODAL --}}
<div class="modal fade" id="multiPrintModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-print me-2"></i>Multi-Print Invoices</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-1">How many invoices per A4 sheet?</p>
                <p class="text-muted small mb-3">Fits multiple invoices on one page to save paper.</p>
                <div class="row g-3">
                    @foreach([1 => ['fa-file-alt','1 per page'], 2 => ['fa-columns','2 per page (side by side)'], 3 => ['fa-th-large','3 per page'], 4 => ['fa-th','4 per page (2×2)']] as $num => [$icon, $label])
                    <div class="col-6">
                        <div class="border rounded-3 p-3 text-center copy-opt {{ $num == 1 ? 'border-primary bg-primary bg-opacity-10' : '' }}"
                             style="cursor:pointer;" data-copies="{{ $num }}">
                            <i class="fas {{ $icon }} fa-2x mb-2 {{ $num == 1 ? 'text-primary' : 'text-muted' }}"></i>
                            <div class="small fw-semibold">{{ $label }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <input type="hidden" id="selCopies" value="1">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="doPrint">
                    <i class="fas fa-print me-1"></i> Print Selected
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function confirmDelete(id) {
            document.getElementById('deleteForm').action = '/sales/' + id;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        // ── Checkbox + Multi-Print logic ──────────────────────────────
        var selectedUrls = [];

        function updateBtn() {
            selectedUrls = Array.from(document.querySelectorAll('.sale-cb:checked')).map(cb => cb.value);
            var btn = document.getElementById('multiPrintBtn');
            document.getElementById('selectedCount').textContent = selectedUrls.length;
            btn.classList.toggle('d-none', selectedUrls.length === 0);
        }

        document.getElementById('selectAll').addEventListener('change', function () {
            document.querySelectorAll('.sale-cb').forEach(cb => cb.checked = this.checked);
            updateBtn();
        });

        document.querySelectorAll('.sale-cb').forEach(cb => cb.addEventListener('change', updateBtn));

        // Copy option selection
        document.querySelectorAll('.copy-opt').forEach(function (el) {
            el.addEventListener('click', function () {
                document.querySelectorAll('.copy-opt').forEach(function (o) {
                    o.classList.remove('border-primary','bg-primary','bg-opacity-10');
                    o.querySelector('i').classList.replace('text-primary','text-muted');
                });
                el.classList.add('border-primary','bg-primary','bg-opacity-10');
                el.querySelector('i').classList.replace('text-muted','text-primary');
                document.getElementById('selCopies').value = el.dataset.copies;
            });
        });

        document.getElementById('doPrint').addEventListener('click', function () {
            var selected  = Array.from(document.querySelectorAll('.sale-cb:checked'));
            var copies    = parseInt(document.getElementById('selCopies').value, 10);
            if (!selected.length) return;

            var btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Loading…';

            var cols        = copies >= 4 ? 2 : (copies === 3 ? 3 : (copies === 2 ? 2 : 1));
            var orientation = (copies === 2 || copies === 3) ? 'landscape' : 'portrait';
            var pw = orientation === 'landscape' ? '297mm' : '210mm';
            var ph = orientation === 'landscape' ? '210mm' : '297mm';

            // Fetch HTML invoice views (same origin — no CORS issues)
            var printUrls = selected.map(function(cb) {
                return cb.value.replace(/\/pdf$/, '/print-view');
            });

            Promise.all(printUrls.map(function(url) {
                return fetch(url).then(function(r) { return r.text(); });
            })).then(function(htmls) {
                // Extract <body> content from each invoice
                var bodies = htmls.map(function(h) {
                    var m = h.match(/<body[^>]*>([\s\S]*?)<\/body>/i);
                    return m ? m[1] : h;
                });

                var out = '<!DOCTYPE html><html><head><meta charset="utf-8"><style>';
                out += '@page{size:' + pw + ' ' + ph + ';margin:6mm;}';
                out += '*{margin:0;padding:0;box-sizing:border-box;}';
                out += 'body{font-family:Arial,sans-serif;font-size:9px;color:#111;background:#fff;}';
                out += '.grid{display:grid;grid-template-columns:repeat(' + cols + ',1fr);gap:4mm;width:100%;}';
                out += '.inv{border-right:1px dashed #bbb;padding-right:4mm;break-inside:avoid;}';
                out += '.inv:last-child{border-right:none;padding-right:0;}';
                // Inherit print-view styles
                out += '.biz{text-align:center;border-bottom:2px solid #1e40af;padding-bottom:5px;margin-bottom:8px;}';
                out += '.biz-name{font-size:13px;font-weight:700;}';
                out += '.biz-sub{font-size:8px;color:#555;}';
                out += 'h2{font-size:11px;font-weight:700;text-align:center;margin-bottom:6px;}';
                out += '.info-table{width:100%;border-collapse:collapse;margin-bottom:6px;}';
                out += '.info-table td{padding:2px 3px;}';
                out += '.info-table td:first-child{font-weight:600;width:38%;color:#333;}';
                out += '.items{width:100%;border-collapse:collapse;margin-bottom:6px;}';
                out += '.items th{background:#1e40af;color:#fff;padding:2px 3px;font-size:8px;text-align:left;}';
                out += '.items td{padding:2px 3px;border-bottom:1px solid #e5e7eb;font-size:8px;}';
                out += '.totals{width:100%;border-collapse:collapse;}';
                out += '.totals td{padding:1px 3px;}';
                out += '.totals td:first-child{text-align:right;color:#444;width:65%;}';
                out += '.totals td:last-child{text-align:right;font-weight:700;}';
                out += '.divider{border:none;border-top:1px dashed #ccc;margin:5px 0;}';
                out += '</style></head><body>';
                out += '<div class="grid">';
                bodies.forEach(function(b) { out += '<div class="inv">' + b + '</div>'; });
                out += '</div>';
                out += '<scr'+'ipt>window.print();<\/scr'+'ipt>';
                out += '</body></html>';

                var w = window.open('', '_blank');
                w.document.write(out);
                w.document.close();
            }).catch(function(e) {
                alert('Could not load invoices: ' + e.message);
            }).finally(function() {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-print me-1"></i> Print Selected';
            });

            bootstrap.Modal.getInstance(document.getElementById('multiPrintModal')).hide();
        });
    </script>
@endpush