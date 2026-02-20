@extends('layouts.app')

@section('title', 'Add Product')

@section('page_title', 'Add Product')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('products.store') }}" id="formProduct" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="sku" class="form-label">SKU</label>
                        <input type="text" name="sku" class="form-control" value="{{ old('sku') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="flavour_id" class="form-label">Flavour</label>
                        <select name="flavour_id" class="form-control flavour-select">
                            <option value="">Select Flavour</option>
                            @foreach($flavours as $flavour)
                                <option value="{{ $flavour->id }}" {{ old('flavour_id') == $flavour->id ? 'selected' : '' }}>
                                    {{ $flavour->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="packing_id" class="form-label">Packing</label>
                        <select name="packing_id" class="form-control packing-select">
                            <option value="">Select Packing</option>
                            @foreach($packings as $packing)
                                <option value="{{ $packing->id }}" {{ old('packing_id') == $packing->id ? 'selected' : '' }}>
                                    {{ $packing->type }} ({{ $packing->unit_size }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="category_id" class="form-label">Category</label>
                        <select name="category_id" class="form-control category-select">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="barcode" class="form-label">Barcode</label>
                        <input type="text" name="barcode" class="form-control" value="{{ old('barcode') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="weight" class="form-label">Weight</label>
                        <input type="number" step="0.01" name="weight" class="form-control" value="{{ old('weight') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="volume" class="form-label">Volume</label>
                        <input type="number" step="0.01" name="volume" class="form-control" value="{{ old('volume') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="discontinued" {{ old('status') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="gst" class="form-label">GST (%)</label>
                        <input type="number" step="0.01" name="gst" class="form-control" value="{{ old('gst', 0.00) }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="reorder_level" class="form-label">Reorder Level</label>
                        <input type="number" name="reorder_level" value="0" class="form-control" value="{{ old('reorder_level') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="max_stock_level" class="form-label">Max Stock Level</label>
                        <input type="number" name="max_stock_level" value="0" class="form-control" value="{{ old('max_stock_level') }}">
                    </div>
                </div>

                <!-- Image Upload Field with Preview -->
                <div class="mb-3">
                    <label for="image" class="form-label">Product Image</label>
                    <input 
                        type="file" 
                        name="image" 
                        id="image" 
                        class="form-control @error('image') is-invalid @enderror"
                        accept="image/*"
                    >
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <img id="imagePreview" src="" alt="Image Preview" class="img-fluid" style="max-height: 200px;">
                </div>
                <button class="btn btn-success" type="submit">
                    <i class="fas fa-save"></i> Save Product
                </button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </form>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Apply Select2 to the flavour, packing, and category dropdowns
            $('.flavour-select').select2({
                width: '100%',
                placeholder: "Select Flavour",
                allowClear: true
            });
            $('.packing-select').select2({
                width: '100%',
                placeholder: "Select Packing",
                allowClear: true
            });
            $('.category-select').select2({
                width: '100%',
                placeholder: "Select Category",
                allowClear: true
            });

            // Image upload preview functionality
            $('#image').on('change', function(event) {
                const reader = new FileReader();
                reader.onload = function() {
                    $('#imagePreview').attr('src', reader.result).show();
                };
                reader.readAsDataURL(event.target.files[0]);
            });
        });
    </script>
    @endpush

    @push("styles")
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <style type="text/css">
        .select2-selection{
            height: 38px !important;
        }
        #imagePreview {
            display: none;
        }
        #image:valid + #imagePreview {
            display: block;
        }
    </style>
    @endpush
@endsection
