<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventory Transactions Report</title>
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
        <h2>Inventory Transactions Report</h2>
        <p>Generated on: {{ \Carbon\Carbon::now()->format('H:i d-M-Y') }}</p>
    </div>

    @if(!empty($filters))
        <div class="filters">
            <p><strong>Product:</strong> 
                {{ isset($filters['product_id']) ? \App\Models\Product::find($filters['product_id'])->name : 'All' }}
            </p>
            <p><strong>Transaction Type:</strong> 
                {{ isset($filters['transaction_type']) ? ucfirst($filters['transaction_type']) : 'All' }}
            </p>
            <p><strong>User:</strong> 
                {{ isset($filters['user_id']) ? \App\Models\User::find($filters['user_id'])->name : 'All' }}
            </p>
            <p><strong>Date Range:</strong> 
                {{ isset($filters['from_date']) ? \Carbon\Carbon::parse($filters['from_date'])->format('d-M-Y') : 'N/A' }} 
                to 
                {{ isset($filters['to_date']) ? \Carbon\Carbon::parse($filters['to_date'])->format('d-M-Y') : 'N/A' }}
            </p>
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 12%;">Date</th>
                <th style="width: 25%;">Product</th>
                <th style="width: 15%;">Transaction Type</th>
                <th style="width: 10%;" class="right">Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d-m-Y') }}</td>
                    <td>{{ $transaction->product->name }}</td>
                    <td>{{ class_basename($transaction->transactionable_type) }}</td>
                    <td class="right">{{ number_format($transaction->quantity) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3"><strong>Total Transactions:</strong></td>
                <td class="right"><strong>{{ number_format($transactions->sum('quantity')) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 20px;">
        <p><strong>Total Quantity Change:</strong> {{ number_format($transactions->sum('qty_change')) }}</p>
        <p><strong>Total Transactions:</strong> {{ count($transactions) }}</p>
    </div>

</body>
</html>
