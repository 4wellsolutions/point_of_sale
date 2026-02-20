<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Invoice')</title>
    <style>
        /* General Body Style */
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            margin: 0;
            padding: 0 10px;
            color: #1e293b;
            font-size: 13px;
            line-height: 1.5;
        }

        /* Page Setup for A4 */
        @page {
            size: A4;
            margin: 10mm;
        }

        /* ========================================
           HEADER — Business Letterhead
           ======================================== */
        .letterhead {
            border-bottom: 3px solid #1e40af;
            padding: 12px 0;
            margin-bottom: 15px;
        }

        .letterhead-inner {
            display: table;
            width: 100%;
        }

        .letterhead-left {
            display: table-cell;
            vertical-align: top;
            width: 60%;
        }

        .letterhead-right {
            display: table-cell;
            vertical-align: top;
            text-align: right;
            width: 40%;
        }

        .business-name {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 3px;
        }

        .business-details {
            font-size: 10px;
            color: #64748b;
            line-height: 1.7;
        }

        .invoice-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #94a3b8;
            font-weight: 600;
        }

        .invoice-title {
            font-size: 16px;
            font-weight: 700;
            color: #1e40af;
            margin: 2px 0;
        }

        .invoice-date {
            font-size: 10px;
            color: #64748b;
        }

        /* Card Style */
        .card {
            border: 1px solid #e2e8f0;
            margin-bottom: 15px;
            background-color: #fff;
            border-radius: 6px;
            overflow: hidden;
        }

        .card-header {
            background-color: #1e293b;
            color: #fff;
            padding: 8px 12px;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-body {
            padding: 10px 12px;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 12px;
        }

        th,
        td {
            padding: 7px 8px;
            text-align: left;
            border: 1px solid #e2e8f0;
        }

        th {
            background-color: #f1f5f9;
            font-weight: 600;
            color: #334155;
            font-size: 11px;
        }

        thead th {
            background-color: #1e293b;
            color: #ffffff;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        td {
            vertical-align: middle;
        }

        /* Fixed Width for Information Table */
        .fixed-width-table th,
        .fixed-width-table td {
            width: 20%;
            border: none;
            border-bottom: 1px solid #f1f5f9;
        }

        .fixed-width-table th {
            background: transparent;
            color: #64748b;
            font-size: 11px;
        }

        .fixed-width-table td {
            font-weight: 600;
            color: #1e293b;
        }

        /* Total and Summary Row Styling */
        .summary-row th {
            text-align: right;
            background: #f8fafc;
        }

        .summary-row td {
            text-align: center;
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }

        .notes-section {
            font-size: 12px;
            color: #555;
            padding: 6px 0;
        }
    </style>
</head>

<body>

    {{-- ── Business Letterhead ─────────────────── --}}
    <div class="letterhead">
        <div class="letterhead-inner">
            <div class="letterhead-left">
                <div class="business-name">{{ setting('business_name', setting('app_name', 'POS System')) }}</div>
                <div class="business-details">
                    @if(setting('business_address'))
                        {{ setting('business_address') }}<br>
                    @endif
                    @if(setting('business_phone'))
                        Phone: {{ setting('business_phone') }}
                    @endif
                    @if(setting('business_email'))
                        | Email: {{ setting('business_email') }}
                    @endif
                    @if(setting('tax_number'))
                        <br>Tax #: {{ setting('tax_number') }}
                    @endif
                </div>
            </div>
            <div class="letterhead-right">
                <div class="invoice-label">Invoice</div>
                <div class="invoice-title">@yield('title', 'Invoice')</div>
                <div class="invoice-date">{{ \Carbon\Carbon::now()->format('F j, Y') }}</div>
            </div>
        </div>
    </div>

    @yield('content')

    <div class="footer">
        {{ setting('app_name', 'POS System') }} &mdash; Generated on {{ \Carbon\Carbon::now()->format('F j, Y') }}
    </div>
</body>

</html>