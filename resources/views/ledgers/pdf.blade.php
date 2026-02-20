<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ledger Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .filters {
            margin-bottom: 20px;
        }
        .filters p {
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 6px;
            text-align: left;
            word-wrap: break-word;
        }
        th {
            background-color: #f2f2f2;
        }
        .right {
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Ledger Report</h2>
        <p>Generated on: {{ \Carbon\Carbon::now()->format('H:i d-M-Y') }}</p>
    </div>

    @if(!empty($filters))
        <div class="filters">
            <p><strong>Entity:</strong> 
                @if(isset($filters['ledgerable_id']))
                    @php
                        $modal = $filters['ledgerable_type'];
                        if($modal == "vendor"){
                            $ledgerable = \App\Models\Vendor::find($filters['ledgerable_id']);
                        }elseif($modal == "customer"){
                            $ledgerable = \App\Models\Customer::find($filters['ledgerable_id']);
                        }
                    @endphp
                @endif
                {{$ledgerable->name}}
            </p>
            <p><strong>Transaction Type:</strong> 
                {{ isset($filters['transaction_type']) ? ucfirst($filters['transaction_type']) : 'All' }}
            </p>
            <p><strong>Date Range:</strong> 
                {{ isset($filters['start_date']) ? \Carbon\Carbon::parse($filters['start_date'])->format('d-M-Y') : 'N/A' }} 
                to 
                {{ isset($filters['end_date']) ? \Carbon\Carbon::parse($filters['end_date'])->format('d-M-Y') : 'N/A' }}
            </p>
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 10%;">Date</th>
                <th style="width: 40%;">Description</th>
                <th style="width: 10%;">Debit</th>
                <th style="width: 10%;">Credit</th>
                <th style="width: 10%;">Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ledgers as $ledger)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($ledger->date)->format('d-m-Y') }}</td>
                    <td>{{ $ledger->description }} {{ isset($ledger->transaction) ? '('.$ledger->transaction->paymentMethod->method_name.')' : '' }}</td>
                    <td class="right">{{ number_format($ledger->debit, 2) }}</td>
                    <td class="right">{{ number_format($ledger->credit, 2) }}</td>
                    <td class="right">{{ number_format($ledger->balance, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2"><strong>Total:</strong></td>
                <td class="right"><strong>{{ number_format($ledgers->sum('debit'), 2) }}</strong></td>
                <td class="right"><strong>{{ number_format($ledgers->sum('credit'), 2) }}</strong></td>
                <td class="right"><strong>{{ number_format($ledgers->last()->balance ?? 0, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 20px;">
        <p><strong>Total Debit:</strong> {{ number_format($ledgers->sum('debit'), 2) }}</p>
        <p><strong>Total Credit:</strong> {{ number_format($ledgers->sum('credit'), 2) }}</p>
        <p><strong>Last Balance:</strong> {{ number_format($ledgers->last()->balance ?? 0, 2) }}</p>
    </div>
</body>
</html>
