@extends('layouts.app')

@section('title', 'New Stock Adjustment')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('stock-loss-damage.index') }}">Stock Adjustments</a></li>
    <li class="breadcrumb-item active">New Adjustment</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>New Stock Adjustment</h5>
        </div>
        <div class="card-body">
            @if($errors->has('error'))
                <div class="alert alert-danger">{{ $errors->first('error') }}</div>
            @endif

            <form action="{{ route('stock-loss-damage.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="product_id" class="form-label">Product <span class="text-danger">*</span></label>
                        <select name="product_id" id="product_id" class="form-control @error('product_id') is-invalid @enderror" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="location_id" class="form-label">Location <span class="text-danger">*</span></label>
                        <select name="location_id" id="location_id" class="form-control @error('location_id') is-invalid @enderror" required>
                            <option value="">Select Location</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('location_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="batch_id" class="form-label">Batch <span class="text-danger">*</span></label>
                        <select name="batch_id" id="batch_id" class="form-control @error('batch_id') is-invalid @enderror" required>
                            <option value="">Select Product First</option>
                        </select>
                        @error('batch_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        <small class="text-muted" id="batchStockInfo"></small>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-control @error('category') is-invalid @enderror" required>
                            <option value="adjustment" {{ old('category') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                            <option value="damage" {{ old('category') == 'damage' ? 'selected' : '' }}>Damage</option>
                            <option value="loss" {{ old('category') == 'loss' ? 'selected' : '' }}>Loss</option>
                        </select>
                        @error('category')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                            <option value="decrease" {{ old('type', 'decrease') == 'decrease' ? 'selected' : '' }}>Decrease (Stock Out)</option>
                            <option value="increase" {{ old('type') == 'increase' ? 'selected' : '' }}>Increase (Stock In)</option>
                        </select>
                        @error('type')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                            required min="1" value="{{ old('quantity') }}">
                        @error('quantity')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                            required value="{{ old('date', \Carbon\Carbon::now()->format('Y-m-d')) }}">
                        @error('date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                    <textarea name="reason" class="form-control @error('reason') is-invalid @enderror"
                        rows="3" required>{{ old('reason') }}</textarea>
                    @error('reason')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <button class="btn btn-success" type="submit">
                    <i class="fas fa-save me-1"></i> Save Adjustment
                </button>
                <a href="{{ route('stock-loss-damage.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('product_id').addEventListener('change', loadBatches);
    document.getElementById('location_id').addEventListener('change', loadBatches);

    function loadBatches() {
        const productId = document.getElementById('product_id').value;
        const locationId = document.getElementById('location_id').value;
        const batchSelect = document.getElementById('batch_id');
        const stockInfo = document.getElementById('batchStockInfo');

        batchSelect.innerHTML = '<option value="">Loading...</option>';
        stockInfo.textContent = '';

        if (!productId) {
            batchSelect.innerHTML = '<option value="">Select Product First</option>';
            return;
        }

        let url = '{{ route("stock-loss-damage.batches") }}?product_id=' + productId;
        if (locationId) url += '&location_id=' + locationId;

        fetch(url)
            .then(r => r.json())
            .then(batches => {
                batchSelect.innerHTML = '<option value="">Select Batch</option>';
                batches.forEach(b => {
                    const opt = document.createElement('option');
                    opt.value = b.id;
                    opt.textContent = b.batch_no + ' (Stock: ' + b.stock + ')';
                    batchSelect.appendChild(opt);
                });
            })
            .catch(() => {
                batchSelect.innerHTML = '<option value="">Error loading batches</option>';
            });
    }

    document.getElementById('batch_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const stockInfo = document.getElementById('batchStockInfo');
        if (selectedOption && selectedOption.value) {
            stockInfo.textContent = 'Selected: ' + selectedOption.textContent;
        } else {
            stockInfo.textContent = '';
        }
    });
</script>
@endpush
