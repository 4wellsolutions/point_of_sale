@extends('exports.layout')
@section('content')
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Category</th>
                <th>Flavour</th>
                <th>Packing</th>
                <th class="text-center">GST %</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="fw-bold">{{ $product->name }}</td>
                    <td>{{ $product->category->name ?? '—' }}</td>
                    <td>{{ $product->flavour->name ?? '—' }}</td>
                    <td>{{ $product->packing->name ?? '—' }}</td>
                    <td class="text-center">{{ $product->gst ?? 0 }}%</td>
                    <td class="text-center">
                        <span class="badge {{ $product->status == 'active' ? 'badge-success' : 'badge-danger' }}">
                            {{ ucfirst($product->status) }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <p><strong>Total Products:</strong> {{ $products->count() }}</p>
        <p><strong>Active:</strong> {{ $products->where('status', 'active')->count() }} | <strong>Inactive:</strong>
            {{ $products->where('status', 'inactive')->count() }}</p>
    </div>
@endsection