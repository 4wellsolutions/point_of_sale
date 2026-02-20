@extends('layouts.app')

@section('title', 'Sales by Product')

@section('page_title', 'Sales by Product')

@section('content')
    <div class="card">
        <div class="card-body">
            <canvas id="salesChart"></canvas>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesData = {
            labels: {!! json_encode($sales->pluck('product.name')) !!},
            datasets: [{
                label: 'Total Units Sold',
                data: {!! json_encode($sales->pluck('total_sold')) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        };
        const salesChart = new Chart(ctx, {
            type: 'bar',
            data: salesData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        precision:0
                    }
                }
            }
        });
    </script>
@endpush
