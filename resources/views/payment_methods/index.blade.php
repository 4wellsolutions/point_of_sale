@extends('layouts.app')

@section('title', 'Payment Methods')

@section('page_title', 'Payment Methods')

@section('content')
    <div class="card">
        <div class="card-body">
            <a href="{{ route('payment_methods.create') }}" class="btn btn-primary mb-3">
                <i class="fas fa-plus"></i> Add Payment Method
            </a>

            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Method Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($paymentMethods as $method)
                        <tr>
                            <td>{{ $method->id }}</td>
                            <td>{{ $method->method_name }}</td>
                            <td>
                                <a href="{{ route('payment_methods.edit', $method->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('payment_methods.destroy', $method->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
