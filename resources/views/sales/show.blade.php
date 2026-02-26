@extends('layouts.app')

@section('title', 'Sale Details')
@section('page_title', 'Sale Details')

@section('content')

    {{-- ===== Header Banner ===== --}}
    <div class="detail-header mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="detail-icon-wrapper">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div>
                    <h2 class="mb-1" style="font-weight:700; font-size:1.5rem;">Sale {{ $sale->invoice_no }}</h2>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="detail-id-badge">{{ $sale->customer->name }}</span>
                        <span class="badge bg-info">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('sales.pdf', $sale) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-file-pdf me-1"></i> PDF
                </a>
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                    data-bs-target="#multiPrintModal">
                    <i class="fas fa-print me-1"></i> Multi-Print
                </button>
                <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    {{-- ===== Financial Summary Tiles ===== --}}
    <div class="card mb-4">
        <div class="card-body p-0">
            <div class="row g-0">
                <div class="col-sm-4">
                    <div class="stat-tile">
                        <div class="stat-tile-icon" style="background:rgba(59,130,246,.1); color:#3b82f6;">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <div>
                            <div class="stat-tile-value">
                                {{ setting('currency_symbol', '$') }}{{ format_number($sale->total_amount) }}
                            </div>
                            <div class="stat-tile-label">Total Amount</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="stat-tile">
                        <div class="stat-tile-icon" style="background:rgba(245,158,11,.1); color:#f59e0b;">
                            <i class="fas fa-percent"></i>
                        </div>
                        <div>
                            <div class="stat-tile-value">
                                {{ setting('currency_symbol', '$') }}{{ format_number($sale->discount_amount) }}
                            </div>
                            <div class="stat-tile-label">Discount</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="stat-tile">
                        <div class="stat-tile-icon" style="background:rgba(16,185,129,.1); color:#10b981;">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div>
                            <div class="stat-tile-value">
                                {{ setting('currency_symbol', '$') }}{{ format_number($sale->net_amount) }}
                            </div>
                            <div class="stat-tile-label">Net Amount</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Sale Details ===== --}}
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="fas fa-info-circle" style="color:var(--primary);"></i>
            <h3 class="card-title mb-0">Sale Information</h3>
        </div>
        <div class="card-body p-0">
            <div class="row g-0">
                <div class="col-sm-6">
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-user"></i> Customer</span>
                        <span class="detail-value">{{ $sale->customer->name }}</span>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-file-invoice"></i> Invoice No</span>
                        <span class="detail-value font-monospace">{{ $sale->invoice_no }}</span>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-calendar"></i> Sale Date</span>
                        <span class="detail-value">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</span>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-user-tie"></i> Processed By</span>
                        <span class="detail-value">{{ $sale->user->name ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($sale->notes)
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-sticky-note" style="color:var(--primary);"></i>
                <h3 class="card-title mb-0">Notes</h3>
            </div>
            <div class="card-body">
                <div class="notes-block">{{ $sale->notes }}</div>
            </div>
        </div>
    @endif

    {{-- ===== Sale Items ===== --}}
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="fas fa-list" style="color:var(--primary);"></i>
            <h3 class="card-title mb-0">Sale Items</h3>
            <span class="badge bg-info ms-auto">{{ $sale->saleItems->count() }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width:50px">Image</th>
                            <th>Product</th>
                            <th>Batch No</th>
                            <th>Location</th>
                            <th>Expiry</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Sale Price</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->saleItems as $item)
                            <tr>
                                <td>
                                    @if($item->product->image_url)
                                        <img src="{{ asset($item->product->image_url) }}" alt="{{ $item->product->name }}"
                                            class="product-thumb">
                                    @else
                                        <img src="{{ asset('images/no-image.png') }}" alt="No Image" class="product-thumb">
                                    @endif
                                </td>
                                <td class="fw-medium">{{ $item->product->name }}</td>
                                <td><span class="font-monospace">{{ $item->batch_no ?? '—' }}</span></td>
                                <td>{{ $item->location->name }}</td>
                                <td>
                                    @if($item->expiry_date)
                                        {{ \Carbon\Carbon::parse($item->expiry_date)->format('d M Y') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end">{{ format_number($item->sale_price) }}</td>
                                <td class="text-end fw-medium">{{ format_number($item->total_amount) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="7" class="text-end">Total:</th>
                            <th class="text-end">{{ format_number($sale->total_amount) }}</th>
                        </tr>
                        <tr>
                            <th colspan="7" class="text-end">Discount:</th>
                            <th class="text-end">{{ format_number($sale->discount_amount) }}</th>
                        </tr>
                        <tr>
                            <th colspan="7" class="text-end">Net Amount:</th>
                            <th class="text-end">{{ format_number($sale->net_amount) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- ===== Payment Methods ===== --}}
    @if($sale->transactions->count() > 0)
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-credit-card" style="color:var(--primary);"></i>
                <h3 class="card-title mb-0">Payment Methods</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Payment Method</th>
                                <th class="text-end">Amount ({{ setting('currency_symbol', '$') }})</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->transactions as $transaction)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}</td>
                                    <td>{{ $transaction->paymentMethod->method_name }}</td>
                                    <td class="text-end">{{ format_number($transaction->amount) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2">Total Payment:</th>
                                <th class="text-end">{{ format_number($sale->transactions->sum('amount'), 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="2">Remaining Balance:</th>
                                <th class="text-end">
                                    {{ format_number($sale->net_amount - $sale->transactions->sum('amount'), 2) }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif

@endsection

{{-- ═══════════════════════════════════════════
MULTI-PRINT MODAL
═══════════════════════════════════════════ --}}
<div class="modal fade" id="multiPrintModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-print me-2"></i>Multi-Print Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Select how many copies to print per A4 sheet:</p>
                <div class="row g-3" id="copyOptions">
                    @foreach([1 => ['fa-file-alt', '1 Copy Full Page'], 2 => ['fa-columns', '2 Copies Side by Side'], 3 => ['fa-th-large', '3 Copies'], 4 => ['fa-th', '4 Copies (2×2)']] as $num => [$icon, $label])
                        <div class="col-6">
                            <div class="border rounded-3 p-3 text-center copy-option cursor-pointer {{ $num == 1 ? 'border-primary bg-primary bg-opacity-10' : '' }}"
                                style="cursor:pointer;" data-copies="{{ $num }}">
                                <i class="fas {{ $icon }} fa-2x mb-2 {{ $num == 1 ? 'text-primary' : 'text-muted' }}"></i>
                                <div class="small fw-semibold">{{ $label }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <input type="hidden" id="selectedCopies" value="1">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="doPrint">
                    <i class="fas fa-print me-1"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (function () {
            // Copy option selection
            document.querySelectorAll('.copy-option').forEach(function (el) {
                el.addEventListener('click', function () {
                    document.querySelectorAll('.copy-option').forEach(function (o) {
                        o.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
                        o.querySelector('i').classList.remove('text-primary');
                        o.querySelector('i').classList.add('text-muted');
                    });
                    el.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
                    el.querySelector('i').classList.remove('text-muted');
                    el.querySelector('i').classList.add('text-primary');
                    document.getElementById('selectedCopies').value = el.dataset.copies;
                });
            });

            document.getElementById('doPrint').addEventListener('click', function () {
                var copies = parseInt(document.getElementById('selectedCopies').value, 10);
                var pdfUrl = '{{ route('sales.pdf', $sale) }}';

                // Build a print page that tiles the PDF URL in iframes
                var cols = copies >= 4 ? 2 : (copies === 2 || copies === 3 ? 2 : 1);
                var rows = Math.ceil(copies / cols);
                var cellW = Math.floor(100 / cols);
                var cellH = Math.floor(100 / rows);

                var html = '<!DOCTYPE html><html><head><title>Print</title><style>';
                html += 'html,body{margin:0;padding:0;width:100%;height:100%;}';
                html += '.wrap{display:grid;grid-template-columns:repeat(' + cols + ',1fr);grid-template-rows:repeat(' + rows + ',1fr);width:100%;height:100vh;gap:4px;}';
                html += 'iframe{width:100%;height:100%;border:none;}';
                html += '@media print{html,body{width:210mm;height:297mm;}.wrap{width:210mm;height:297mm;}}';
                html += '</style></head><body><div class="wrap">';

                for (var i = 0; i < copies; i++) {
                    html += '<iframe src="' + pdfUrl + '"></iframe>';
                }
                html += '</div><script>'
                    + 'var iframes=document.querySelectorAll("iframe"),loaded=0;'
                    + 'iframes.forEach(function(f){f.onload=function(){loaded++;if(loaded===iframes.length){window.print();}}});'
                    + '<\/script></body></html>';

                var win = window.open('', '_blank');
                win.document.write(html);
                win.document.close();

                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('multiPrintModal')).hide();
            });
        })();
    </script>
@endpush