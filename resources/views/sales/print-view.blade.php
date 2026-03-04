<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $sale->invoice_no }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #111;
            background: #fff;
            padding: 10px;
            width: 210mm;
        }

        h2 {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .biz {
            text-align: center;
            border-bottom: 2px solid #1e40af;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .biz-name {
            font-size: 16px;
            font-weight: 700;
        }

        .biz-sub {
            font-size: 10px;
            color: #555;
        }

        .info-table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 3px 5px;
        }

        .info-table td:first-child {
            font-weight: 600;
            width: 38%;
            color: #444;
        }

        .items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .items th {
            background: #1e40af;
            color: #fff;
            padding: 4px 5px;
            text-align: left;
            font-size: 10px;
        }

        .items td {
            padding: 3px 5px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }

        .items tfoot th {
            background: #f3f4f6;
            color: #111;
        }

        .totals {
            width: 100%;
            border-collapse: collapse;
        }

        .totals td {
            padding: 2px 5px;
            font-size: 11px;
        }

        .totals td:first-child {
            width: 70%;
            text-align: right;
            color: #444;
        }

        .totals td:last-child {
            text-align: right;
            font-weight: 600;
        }

        .divider {
            border: none;
            border-top: 1px dashed #cbd5e1;
            margin: 8px 0;
        }
    </style>
</head>

<body>

    {{-- Business Header --}}
    <div class="biz">
        <div class="biz-name">{{ setting('business_name', 'WholeSale') }}</div>
        <div class="biz-sub">
            @if(setting('business_phone')) Phone: {{ setting('business_phone') }} @endif
            @if(setting('business_email')) | Email: {{ setting('business_email') }} @endif
            @if(setting('business_address')) | {{ setting('business_address') }} @endif
        </div>
    </div>

    {{-- Invoice Header --}}
    <h2 style="text-align:center;margin-bottom:8px;">SALE INVOICE</h2>

    {{-- Sale Info --}}
    <table class="info-table">
        <tr>
            <td>Customer:</td>
            <td>{{ $sale->customer->name ?? '—' }}</td>
        </tr>
        <tr>
            <td>Invoice No:</td>
            <td>#{{ $sale->invoice_no }}</td>
        </tr>
        <tr>
            <td>Date:</td>
            <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</td>
        </tr>
        @if($sale->notes)
            <tr>
                <td>Notes:</td>
                <td>{{ $sale->notes }}</td>
        </tr>@endif
    </table>

    <hr class="divider">

    {{-- Items Table --}}
    <table class="items">
        <thead>
            <tr>
                <th>Product</th>
                <th>Batch</th>
                <th>Loc</th>
                <th style="text-align:right">Qty</th>
                <th style="text-align:right">Price</th>
                <th style="text-align:right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->saleItems as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->batch?->batch_no ?? '—' }}</td>
                    <td>{{ $item->location->name ?? '—' }}</td>
                    <td style="text-align:right">{{ $item->quantity }}</td>
                    <td style="text-align:right">{{ format_number($item->sale_price) }}</td>
                    <td style="text-align:right">{{ format_number($item->total_amount) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <hr class="divider">

    {{-- Totals --}}
    <table class="totals">
        <tr>
            <td>Sub Total:</td>
            <td>{{ setting('currency_symbol', '') }}{{ format_number($sale->total_amount) }}</td>
        </tr>
        <tr>
            <td>Discount:</td>
            <td>{{ setting('currency_symbol', '') }}{{ format_number($sale->discount_amount) }}</td>
        </tr>
        <tr style="font-size:13px;">
            <td><strong>Net Amount:</strong></td>
            <td><strong>{{ setting('currency_symbol', '') }}{{ format_number($sale->net_amount) }}</strong></td>
        </tr>
    </table>

    @if($sale->transactions->count() > 0)
        <hr class="divider">
        <table class="items">
            <thead>
                <tr>
                    <th>Payment Method</th>
                    <th style="text-align:right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->transactions as $t)
                    <tr>
                        <td>{{ $t->paymentMethod->method_name }}</td>
                        <td style="text-align:right">{{ format_number($t->amount) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</body>

</html>