@extends('layouts.app')

@section('title', 'Add Payment Method')

@section('page_title', 'Add Payment Method')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('payment_methods.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="method_name" class="form-label">Payment Method Name <span class="text-danger">*</span></label>
                    <input type="text" name="method_name" class="form-control" required value="{{ old('method_name') }}">
                </div>
                <button class="btn btn-success" type="submit">
                    <i class="fas fa-save"></i> Save Payment Method
                </button>
                <a href="{{ route('payment_methods.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </form>
        </div>
    </div>
@endsection
