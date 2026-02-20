@extends('layouts.app')

@section('title', 'Expense Type Details')
@section('page_title', 'Expense Type Details')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>{{ $expenseType->name }}</h5>
    </div>
    <div class="card-body">
        <p><strong>Description: </strong>{{ $expenseType->description }}</p>
        <p><strong>Created At: </strong>{{ $expenseType->created_at->format('Y-m-d') }}</p>
    </div>
    <div class="card-footer">
        <a href="{{ route('expense-types.edit', $expenseType->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="{{ route('expense-types.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>
@endsection
