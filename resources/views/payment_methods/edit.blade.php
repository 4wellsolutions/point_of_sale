@extends('layouts.app')

@section('title', 'Edit Payment Method')

@section('page_title', 'Edit Payment Method')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('payment_methods.update', $paymentMethod->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="method_name" class="form-label">Payment Method Name <span class="text-danger">*</span></label>
                    <input type="text" name="method_name" class="form-control" required value="{{ old('method_name', $paymentMethod->method_name) }}">
                </div>
                <button class="btn btn-success" type="submit">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="{{ route('payment_methods.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </form>
        </div>
    </div>
@endsection
