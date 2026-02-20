@extends('layouts.app')

@section('title', 'Expense Details')
@section('page_title', 'Expense Details')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Expense #{{ $expense->id }}</h5>
    </div>
    <div class="card-body">
        <p><strong>Expense Type: </strong>{{ $expense->expenseType->name }}</p>
        <p><strong>Amount: </strong>{{ $expense->amount }}</p>
        <p><strong>Date: </strong>{{ $expense->date }}</p>
        <p><strong>Description: </strong>{{ $expense->description }}</p>
    </div>
    <div class="card-footer">
        <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>
@endsection
