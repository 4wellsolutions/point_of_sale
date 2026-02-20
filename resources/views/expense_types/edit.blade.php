@extends('layouts.app')

@section('title', 'Edit Expense Type')
@section('page_title', 'Edit Expense Type')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('expense-types.update', $expenseType->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Expense Type Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" required 
                       value="{{ old('name', $expenseType->name) }}">
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $expenseType->description) }}</textarea>
                @error('description')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <button class="btn btn-success" type="submit">
                <i class="fas fa-save"></i> Update Expense Type
            </button>
            <a href="{{ route('expense-types.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </form>
    </div>
</div>
@endsection
