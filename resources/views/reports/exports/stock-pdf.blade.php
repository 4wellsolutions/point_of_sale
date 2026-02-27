@extends('exports.layout')
@section('content')
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>SKU</th>
                <th>Category</th>
                <th>Batch No</th>
                <th>Location</th>
                <th>Cost</th>
                <th>Price</th>
                <th class="text-right">Stock Qty</th>
                <th class="text-right">Reorder Level</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $i => $r)
                @php
                    $qty = $r->quantity ?? 0;
                    $status = $qty <= 0 ? 'Out of Stock' : ($qty <= $r->reorder_level ? 'Low Stock' : 'In Stock');
                    $color = $qty <= 0 ? '#dc2626' : ($qty <= $r->reorder_level ? '#d97706' : '#059669');
                @endphp
                <tr class="{{ $i % 2 == 0 ? 'row-alt' : '' }}">
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $r->product_name ?? '—' }}</strong></td>
                    <td>{{ $r->sku }}</td>
                    <td>{{ $r->product->category->name ?? '—' }}</td>
                    <td>{{ $r->batch->batch_no ?? '—' }}</td>
                    <td>{{ $r->location->name ?? '—' }}</td>
                    <td class="text-right">{{ format_number($r->purchase_price) }}</td>
                    <td class="text-right">{{ format_number($r->product->sale_price ?? 0) }}</td>
                    <td class="text-right">{{ format_number($qty) }}</td>
                    <td class="text-right">{{ format_number($r->reorder_level) }}</td>
                    <td><span style="color:{{ $color }};font-weight:700;">{{ $status }}</span></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection