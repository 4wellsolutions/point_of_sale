@extends('layouts.app')

@section('title', 'Add Expense')

@section('page_title', 'Add Expense')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('expenses.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="expense_type_id" class="form-label">Expense Type <span class="text-danger">*</span></label>
                    <select name="expense_type_id" class="form-control @error('expense_type_id') is-invalid @enderror" required>
                        <option value="">Select Expense Type</option>
                        @foreach($expenseTypes as $expenseType)
                            <option value="{{ $expenseType->id }}" {{ old('expense_type_id') == $expenseType->id ? 'selected' : '' }}>
                                {{ $expenseType->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('expense_type_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="amount" class="form-control @error('amount') is-invalid @enderror" required value="{{ old('amount') }}">
                    @error('amount')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" required 
                           value="{{ old('date', \Carbon\Carbon::now()->format('Y-m-d')) }}">
                    @error('date')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <button class="btn btn-success" type="submit">
                    <i class="fas fa-save"></i> Save Expense
                </button>
                <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </form>
        </div>
    </div>
@endsection
