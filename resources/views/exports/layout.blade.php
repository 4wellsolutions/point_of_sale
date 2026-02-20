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
            font-size: 11px;
            color: #1e293b;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 18px;
            color: #4f46e5;
            margin-bottom: 4px;
        }

        .header p {
            font-size: 10px;
            color: #64748b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        thead th {
            background: #f1f5f9;
            border-bottom: 2px solid #e2e8f0;
            padding: 8px 6px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #475569;
        }

        tbody td {
            padding: 6px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
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

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }

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

        .summary {
            margin-top: 15px;
            padding: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }

        .summary p {
            font-size: 11px;
            margin: 2px 0;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $title ?? 'Report' }}</h1>
        <p>Generated on {{ now()->format('d M Y, h:i A') }}</p>
        @if(isset($filters) && count(array_filter($filters ?? [])) > 0)
            <p>Filters: {{ implode(' | ', array_filter($filters)) }}</p>
        @endif
    </div>

    @yield('content')

    <div class="footer">
        POS System — Page {{ '{PAGE_NUM}' }} of {{ '{PAGE_COUNT}' }}
    </div>
</body>

</html>