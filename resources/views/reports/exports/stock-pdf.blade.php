@extends('exports.layout')
@section('report-title', 'Stock Report')
@section('report-body')
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>SKU</th>
                <th>Category</th>
                <th class="text-right">Stock Qty</th>
                <th class="text-right">Reorder Level</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $i => $r)
                @php
                    $qty = $r->total_stock ?? 0;
                    $status = $qty <= 0 ? 'Out of Stock' : ($qty <= $r->reorder_level ? 'Low Stock' : 'In Stock');
                    $color = $qty <= 0 ? '#dc2626' : ($qty <= $r->reorder_level ? '#d97706' : '#059669');
                @endphp
                <tr class="{{ $i % 2 == 0 ? 'row-alt' : '' }}">
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $r->name }}</strong></td>
                    <td>{{ $r->sku }}</td>
                    <td>{{ $r->category->name ?? '—' }}</td>
                    <td class="text-right">{{ number_format($qty) }}</td>
                    <td class="text-right">{{ number_format($r->reorder_level) }}</td>
                    <td><span style="color:{{ $color }};font-weight:700;">{{ $status }}</span></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection