<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Report' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.5;
            padding: 0 15px;
        }

        /* ========================================
           HEADER — Business Info + Report Title
           ======================================== */
        .report-header {
            padding: 15px 0 12px;
            border-bottom: 3px solid #1e40af;
            margin-bottom: 15px;
        }

        .report-header-top {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .business-info {
            display: table-cell;
            vertical-align: top;
            width: 65%;
        }

        .business-info .business-name {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 2px;
            letter-spacing: 0.3px;
        }

        .business-info .business-details {
            font-size: 9px;
            color: #64748b;
            line-height: 1.6;
        }

        .business-info .business-details span {
            margin-right: 6px;
        }

        .report-meta {
            display: table-cell;
            vertical-align: top;
            text-align: right;
            width: 35%;
        }

        .report-meta .report-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #94a3b8;
            font-weight: 600;
        }

        .report-meta .report-title {
            font-size: 14px;
            font-weight: 700;
            color: #1e40af;
            margin: 2px 0;
        }

        .report-meta .report-date {
            font-size: 9px;
            color: #64748b;
        }

        /* Filters bar */
        .filters-bar {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 6px 10px;
            margin-bottom: 12px;
            font-size: 9px;
            color: #475569;
        }

        .filters-bar strong {
            color: #334155;
        }

        /* ========================================
           TABLE
           ======================================== */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        thead th {
            background: #1e293b;
            color: #ffffff;
            padding: 7px 6px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        thead th:first-child {
            border-radius: 4px 0 0 0;
        }

        thead th:last-child {
            border-radius: 0 4px 0 0;
        }

        tbody td {
            padding: 6px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        tbody tr:hover {
            background: #f1f5f9;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: bold;
        }

        /* ========================================
           SUMMARY BOX
           ======================================== */
        .summary {
            margin-top: 15px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            overflow: hidden;
        }

        .summary-header {
            background: #1e293b;
            color: #fff;
            padding: 6px 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-body {
            padding: 10px 12px;
            background: #f8fafc;
        }

        .summary-body p {
            font-size: 11px;
            margin: 3px 0;
            display: flex;
            justify-content: space-between;
        }

        .summary p {
            font-size: 11px;
            margin: 3px 0;
        }

        /* ========================================
           BADGES
           ======================================== */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: 600;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-info {
            background: #dbeafe;
            color: #1e40af;
        }

        /* ========================================
           FOOTER
           ======================================== */
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }
    </style>
</head>

<body>
    {{-- ── Report Header ─────────────────────── --}}
    <div class="report-header">
        <div class="report-header-top">
            <div class="business-info">
                <div class="business-name">{{ setting('business_name', setting('app_name', 'POS System')) }}</div>
                <div class="business-details">
                    @if(setting('business_address'))
                        <span>📍 {{ setting('business_address') }}</span>
                    @endif
                    @if(setting('business_phone'))
                        <span>📞 {{ setting('business_phone') }}</span>
                    @endif
                    @if(setting('business_email'))
                        <span>✉ {{ setting('business_email') }}</span>
                    @endif
                    @if(setting('tax_number'))
                        <span>Tax #: {{ setting('tax_number') }}</span>
                    @endif
                </div>
            </div>
            <div class="report-meta">
                <div class="report-label">Report</div>
                <div class="report-title">{{ $title ?? 'Report' }}</div>
                <div class="report-date">{{ now()->format('d M Y, h:i A') }}</div>
            </div>
        </div>
    </div>

    @if(isset($filters) && count(array_filter($filters ?? [])) > 0)
        <div class="filters-bar">
            <strong>Filters:</strong> {{ implode(' | ', array_filter($filters)) }}
        </div>
    @endif

    @yield('content')

    <div class="footer">
        {{ setting('app_name', 'POS System') }} — Page {{ '{PAGE_NUM}' }} of {{ '{PAGE_COUNT}' }}
    </div>
</body>

</html>