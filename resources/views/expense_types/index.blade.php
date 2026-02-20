@extends('layouts.app')

@section('title', 'Expense Types')
@section('page_title', 'Expense Types')

@section('content')
<div class="card">
    <div class="card-header">
        <a href="{{ route('expense-types.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Expense Type
        </a>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenseTypes as $expenseType)
                <tr>
                    <td>{{ $expenseType->id }}</td>
                    <td>{{ $expenseType->name }}</td>
                    <td>{{ $expenseType->description }}</td>
                    <td>{{ $expenseType->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('expense-types.show', $expenseType->id) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> Show
                        </a>
                        <a href="{{ route('expense-types.edit', $expenseType->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('expense-types.destroy', $expenseType->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')" type="submit">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No Expense Types found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
