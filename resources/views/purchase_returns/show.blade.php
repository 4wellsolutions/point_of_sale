@extends('layouts.app')

@section('title', 'Purchase Return Details')

@section('page_title', 'Purchase Return Details')

@section('content')
    <div class="card mb-4">
        <!-- Header -->
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Purchase Return: {{ $purchaseReturn->invoice_no }}</h3>
            <div>
                <a href="{{ route('purchase-returns.edit', $purchaseReturn) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('purchase-returns.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <!-- Body -->
        <div class="card-body">
            <!-- Vendor and Purchase Information -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Vendor Information</h5>
                    <p><strong>Name:</strong> {{ $purchaseReturn->purchase->vendor->name ?? 'N/A' }}</p>
                    <p><strong>Email:</strong> {{ $purchaseReturn->purchase->vendor->email ?? 'N/A' }}</p>
                    <p><strong>Phone:</strong> {{ $purchaseReturn->purchase->vendor->phone ?? 'N/A' }}</p>
                    <p><strong>Address:</strong> {{ $purchaseReturn->purchase->vendor->address ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <h5>Original Purchase</h5>
                    <p><strong>Invoice No:</strong> {{ $purchaseReturn->purchase->invoice_no ?? 'N/A' }}</p>
                    <p><strong>Purchase Date:</strong> {{ $purchaseReturn->purchase->purchase_date ?? 'N/A' }}</p>
                    <p><strong>Total Amount:</strong> ${{ number_format($purchaseReturn->purchase->total_amount, 2) ?? 'N/A' }}</p>
                    <p><strong>Net Amount:</strong> ${{ number_format($purchaseReturn->purchase->net_amount, 2) ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- Return Details -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Return Details</h5>
                    <p><strong>Return Date:</strong> {{ $purchaseReturn->return_date }}</p>
                    <p><strong>Discount Amount:</strong> ${{ number_format($purchaseReturn->discount_amount, 2) }}</p>
                    <p><strong>Total Amount:</strong> ${{ number_format($purchaseReturn->total_amount, 2) }}</p>
                    <p><strong>Net Amount:</strong> ${{ number_format($purchaseReturn->net_amount, 2) }}</p>
                </div>
                <div class="col-md-6">
                    <h5>Financial Summary</h5>
                    <p><strong>Total Amount:</strong> ${{ number_format($purchaseReturn->total_amount, 2) }}</p>
                    <p><strong>Discount Amount:</strong> ${{ number_format($purchaseReturn->discount_amount, 2) }}</p>
                    <p><strong>Net Amount:</strong> ${{ number_format($purchaseReturn->net_amount, 2) }}</p>
                </div>
            </div>

            <!-- Return Items -->
            <div class="mb-4">
                <h5>Returned Items</h5>
                @if($purchaseReturn->returnItems->count() > 0)
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Batch No</th>
                                <th>Location</th>
                                <th>Quantity</th>
                                <th>Unit Price ($)</th>
                                <th>Total ($)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseReturn->returnItems as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->product->name ?? 'N/A' }}</td>
                                    <td>{{ $item->purchaseItem->batch_no ?? 'N/A' }}</td>
                                    <td>{{ $item->purchaseItem->location->name ?? 'N/A' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->unit_price, 2) }}</td>
                                    <td>{{ number_format($item->total_amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No items returned.</p>
                @endif
            </div>

            <!-- Payment Methods -->
            <div class="mb-4">
                <h5>Payment Methods</h5>
                @if($purchaseReturn->transactions->count() > 0)
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Payment Method</th>
                                <th>Amount ($)</th>
                                <th>Transaction Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseReturn->transactions as $index => $transaction)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $transaction->paymentMethod->name ?? 'N/A' }}</td>
                                    <td>{{ number_format($transaction->amount, 2) }}</td>
                                    <td>{{ $transaction->transaction_date->format('d M Y, H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No payment methods used.</p>
                @endif
            </div>

            <!-- Notes -->
            @if($purchaseReturn->notes)
                <div class="mb-4">
                    <h5>Notes</h5>
                    <p>{{ $purchaseReturn->notes }}</p>
                </div>
            @endif

            <!-- Ledger Entries -->
            <div class="mb-4">
                <h5>Ledger Entries</h5>
                @if($purchaseReturn->ledgerEntries)
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Debit ($)</th>
                                <th>Credit ($)</th>
                                <th>Balance ($)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseReturn->ledgerEntries as $ledger)
                                <tr>
                                    <td>{{ $loop->index }}</td>
                                    <td>{{ $ledger->date }}</td>
                                    <td>{{ $ledger->description }}</td>
                                    <td>{{ number_format($ledger->debit, 2) }}</td>
                                    <td>{{ number_format($ledger->credit, 2) }}</td>
                                    <td>{{ number_format($ledger->balance, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No ledger entries available.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
