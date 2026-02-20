<table class="table table-bordered">
    <thead>
        <tr>
            <th>Date</th>
            <th>Ledger Type</th>
            <th>Name</th>
            <th>Description</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody>
        @forelse($ledgers as $ledger)
            <tr>
                <td>{{ \Carbon\Carbon::parse($ledger->date)->format('d-m-Y') }}</td>
                <td>
                    @if($ledger->ledgerable_type == 'App\Models\Customer')
                        Customer
                    @elseif($ledger->ledgerable_type == 'App\Models\Vendor')
                        Vendor
                    @else
                        {{ class_basename($ledger->ledgerable_type) }}
                    @endif
                </td>
                <td>{{ $ledger->ledgerable->name }}</td>
                <td>{{ $ledger->description }} {{ isset($ledger->transaction) ? '('.$ledger->transaction->paymentMethod->method_name.')' : '' }} </td>
                <td>{{ number_format($ledger->debit, 2) }}</td>
                <td>{{ number_format($ledger->credit, 2) }}</td>
                <td>{{ number_format($ledger->balance, 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7">No ledger entries found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- Pagination Links -->
<div class="d-flex justify-content-center">
    {{ $ledgers->links() }}
</div>
