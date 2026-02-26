@extends('exports.layout')
@php
    $title = 'Ledger Statement';
@endphp

@section('content')

    {{-- Party info + filter summary --}}
    @if(!empty($filters))
        @php
            $partyType = $filters['ledgerable_type'] ?? null;
            $partyId = $filters['ledgerable_id'] ?? null;
            $partyModel = null;
            if ($partyType === 'vendor')
                $partyModel = \App\Models\Vendor::find($partyId);
            if ($partyType === 'customer')
                $partyModel = \App\Models\Customer::find($partyId);
            $symbol = setting('currency_symbol', 'Rs.');
        @endphp

        <div class="filters-bar" style="margin-bottom:14px;">
            @if($partyModel)
                <strong>{{ ucfirst($partyType) }}:</strong> {{ $partyModel->name }}
                @if($partyModel->phone ?? null) &nbsp;|&nbsp; <strong>Phone:</strong> {{ $partyModel->phone }} @endif
                @if($partyModel->address ?? null) &nbsp;|&nbsp; <strong>Address:</strong> {{ $partyModel->address }} @endif
                &nbsp;&nbsp;
            @endif
            <strong>Type:</strong> {{ isset($filters['transaction_type']) ? ucfirst($filters['transaction_type']) : 'All' }}
            &nbsp;|&nbsp;
            <strong>Period:</strong>
            {{ isset($filters['start_date']) ? \Carbon\Carbon::parse($filters['start_date'])->format('d M Y') : 'Beginning' }}
            &nbsp;–&nbsp;
            {{ isset($filters['end_date']) ? \Carbon\Carbon::parse($filters['end_date'])->format('d M Y') : 'Today' }}
        </div>
    @endif

    {{-- Ledger table --}}
    <table>
        <thead>
            <tr>
                <th style="width:12%">Date</th>
                <th style="width:48%">Description</th>
                <th style="width:13%" class="text-right">Debit</th>
                <th style="width:13%" class="text-right">Credit</th>
                <th style="width:14%" class="text-right">Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ledgers as $ledger)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($ledger->date)->format('d M Y') }}</td>
                    <td>
                        {{ $ledger->description }}
                        @if(isset($ledger->transaction) && $ledger->transaction && $ledger->transaction->paymentMethod)
                            &nbsp;<em style="color:#64748b;">({{ $ledger->transaction->paymentMethod->method_name }})</em>
                        @endif
                    </td>
                    <td class="text-right">{{ $symbol }} {{ format_number($ledger->debit) }}</td>
                    <td class="text-right">{{ $symbol }} {{ format_number($ledger->credit) }}</td>
                    <td class="text-right fw-bold">{{ $symbol }} {{ format_number($ledger->balance) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background:#1e293b; color:#fff;">
                <td colspan="2" class="fw-bold">Totals</td>
                <td class="text-right fw-bold">{{ $symbol }} {{ format_number($ledgers->sum('debit'), 2) }}</td>
                <td class="text-right fw-bold">{{ $symbol }} {{ format_number($ledgers->sum('credit'), 2) }}</td>
                <td class="text-right fw-bold">{{ $symbol }} {{ format_number($ledgers->last()->balance ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Summary --}}
    <div class="summary" style="margin-top:16px;">
        <div class="summary-header">Summary</div>
        <div class="summary-body">
            <p><span>Total Debit:</span> <span>{{ $symbol }} {{ format_number($ledgers->sum('debit'), 2) }}</span></p>
            <p><span>Total Credit:</span> <span>{{ $symbol }} {{ format_number($ledgers->sum('credit'), 2) }}</span></p>
            <p><span>Closing Balance:</span><span>{{ $symbol }}
                    {{ format_number($ledgers->last()->balance ?? 0, 2) }}</span></p>
        </div>
    </div>

@endsection