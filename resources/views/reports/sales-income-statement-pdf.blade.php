@extends('layouts.pdf')
@section('title', 'Income Statement — ' . $sale->invoice_no)

@section('content')

    {{-- Invoice Info --}}
    <div class="card">
        <div class="card-header">Invoice Information</div>
        <div class="card-body">
            <table class="fixed-width-table">
                <tr>
                    <th>Invoice No</th>
                    <td>{{ $sale->invoice_no }}</td>
                    <th>Customer</th>
                    <td>{{ $sale->customer->name ?? '—' }}</td>
                    <th>Sale Date</th>
                    <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Item-wise Profit Table --}}
    <div class="card">
        <div class="card-header">Item-wise Profit Breakdown</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th style="text-align:right;">Qty</th>
                    <th style="text-align:right;">Sale Price</th>
                    <th style="text-align:right;">Cost Price</th>
                    <th style="text-align:right;">Revenue</th>
                    <th style="text-align:right;">Cost</th>
                    <th style="text-align:right;">Discount</th>
                    <th style="text-align:right;">Net</th>
                    <th style="text-align:right;">Profit</th>
                    <th style="text-align:right;">Margin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->product->name ?? '—' }}</td>
                        <td style="text-align:right;">{{ $item->quantity }}</td>
                        <td style="text-align:right;">{{ format_number($item->sale_price) }}</td>
                        <td style="text-align:right;">{{ format_number($item->purchase_price) }}</td>
                        <td style="text-align:right;">{{ format_number($item->_revenue) }}</td>
                        <td style="text-align:right;">{{ format_number($item->_cost) }}</td>
                        <td style="text-align:right;">{{ format_number($item->_discount) }}</td>
                        <td style="text-align:right;font-weight:bold;">{{ format_number($item->_net) }}</td>
                        <td style="text-align:right;font-weight:bold;color:{{ $item->_profit >= 0 ? '#10b981' : '#ef4444' }}">
                            {{ format_number($item->_profit) }}
                        </td>
                        <td style="text-align:right;">{{ format_number($item->_margin) }}%</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="summary-row">
                    <th colspan="5" style="text-align:right;">Subtotals:</th>
                    <td style="text-align:right;font-weight:bold;">{{ format_number($totals->revenue) }}</td>
                    <td style="text-align:right;font-weight:bold;">{{ format_number($totals->cogs) }}</td>
                    <td style="text-align:right;font-weight:bold;">{{ format_number($totals->item_discount) }}</td>
                    <td style="text-align:right;font-weight:bold;">
                        {{ format_number($totals->net_revenue + $totals->invoice_disc) }}</td>
                    <td style="text-align:right;font-weight:bold;">
                        {{ format_number($totals->gross_profit + $totals->invoice_disc) }}</td>
                    <td></td>
                </tr>
                @if($totals->invoice_disc > 0)
                    <tr class="summary-row">
                        <th colspan="8" style="text-align:right;">Invoice Discount:</th>
                        <td colspan="2" style="text-align:right;font-weight:bold;">- {{ format_number($totals->invoice_disc) }}
                        </td>
                        <td></td>
                    </tr>
                @endif
                <tr class="summary-row" style="font-size:13px;">
                    <th colspan="8" style="text-align:right;">NET PROFIT:</th>
                    <td style="text-align:right;font-weight:bold;">
                        {{ setting('currency_symbol') }}{{ format_number($totals->net_revenue) }}</td>
                    <td
                        style="text-align:right;font-weight:bold;color:{{ $totals->gross_profit >= 0 ? '#10b981' : '#ef4444' }}">
                        {{ setting('currency_symbol') }}{{ format_number($totals->gross_profit) }}
                    </td>
                    <td style="text-align:right;font-weight:bold;">{{ format_number($totals->margin) }}%</td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Summary Box --}}
    <div class="card">
        <div class="card-header">Financial Summary</div>
        <div class="card-body">
            <table class="fixed-width-table">
                <tr>
                    <th>Total Revenue</th>
                    <td>{{ setting('currency_symbol') }}{{ format_number($totals->revenue) }}</td>
                    <th>Cost of Goods Sold</th>
                    <td>{{ setting('currency_symbol') }}{{ format_number($totals->cogs) }}</td>
                </tr>
                <tr>
                    <th>Item Discounts</th>
                    <td>{{ setting('currency_symbol') }}{{ format_number($totals->item_discount) }}</td>
                    <th>Invoice Discount</th>
                    <td>{{ setting('currency_symbol') }}{{ format_number($totals->invoice_disc) }}</td>
                </tr>
                <tr>
                    <th>Net Revenue</th>
                    <td style="font-weight:bold;">{{ setting('currency_symbol') }}{{ format_number($totals->net_revenue) }}
                    </td>
                    <th>Gross Profit</th>
                    <td style="font-weight:bold;color:{{ $totals->gross_profit >= 0 ? '#10b981' : '#ef4444' }}">
                        {{ setting('currency_symbol') }}{{ format_number($totals->gross_profit) }}
                        ({{ format_number($totals->margin) }}%)
                    </td>
                </tr>
            </table>
        </div>
    </div>

@endsection