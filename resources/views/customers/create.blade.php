@extends('layouts.app')

@section('title', 'Customers')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
    <li class="breadcrumb-item active">Add</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center py-2">
            <h3 class="card-title me-auto">Add New Customer</h3>
            <a href="{{ route('customers.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Customers
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('customers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <!-- Name Field -->
                    <div class="col-12 col-md-4 col-lg-6 mb-3">
                        <label for="name" class="form-label">Name *</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div class="col-12 col-md-4 col-lg-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Phone Field -->
                    <div class="col-12 col-md-4 col-lg-6 mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
                        @error('phone')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- WhatsApp Field -->
                    <div class="col-12 col-md-4 col-lg-6 mb-3">
                        <label for="whatsapp" class="form-label">WhatsApp</label>
                        <input type="text" class="form-control" id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}">
                        @error('whatsapp')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Address Field -->
                    <div class="col-12 col-md-4 col-lg-6 mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address">{{ old('address') }}</textarea>
                        @error('address')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Type Field -->
                    <div class="col-12 col-md-4 col-lg-6 mb-3">
                        <label for="type_id" class="form-label">Type</label>
                        <select class="form-select" id="type_id" name="type_id">
                            <option value="">Select Type</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}" {{ old('type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('type_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Area Field (Optional) -->
                    <div class="col-12 col-md-4 col-lg-4 mb-3">
                        <label for="area_id" class="form-label">Area <small class="text-muted">(optional)</small></label>
                        <select class="form-select" id="area_id" name="area_id">
                            <option value="">— No Area —</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>
                                    {{ $area->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('area_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Opening Balance -->
                    <div class="col-12 col-md-4 col-lg-4 mb-3">
                        <label for="opening_balance" class="form-label">
                            Opening Balance <small class="text-muted">(optional)</small>
                        </label>
                        <input type="number" step="0.01" min="0" class="form-control" id="opening_balance"
                            name="opening_balance" value="{{ old('opening_balance', 0) }}">
                        @error('opening_balance')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4 col-lg-4 mb-3">
                        <label class="form-label">Balance Type</label>
                        <select class="form-select" name="opening_balance_type">
                            <option value="debit" {{ old('opening_balance_type', 'debit') === 'debit' ? 'selected' : '' }}>
                                Debit (Customer owes us)
                            </option>
                            <option value="credit" {{ old('opening_balance_type', 'debit') === 'credit' ? 'selected' : '' }}>
                                Credit (We owe customer)
                            </option>
                        </select>
                    </div>


                    <!-- Image Field -->
                    <div class="col-12 col-md-4 col-lg-6 mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input class="form-control" type="file" id="image" name="image" accept="image/*">

                        <!-- Image Preview Container -->
                        <div id="imagePreviewContainer" class="mt-3" style="display: none;">
                            <img id="imagePreview" src="#" alt="Image Preview" class="img-fluid"
                                style="max-width: 150px; max-height: 150px; object-fit: contain;">
                        </div>

                        @error('image')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Submit Customer</button>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>

    @push('scripts')
        <style>
            .select2-container .select2-selection--single {
                height: 38px !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 38px !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 38px !important;
            }
        </style>
        <script>
            $(function () {
                $('#area_id').select2({ placeholder: '— No Area —', allowClear: true, width: '100%' });

                $('#image').change(function () {
                    var file = this.files[0];
                    if (file && file.type.startsWith('image/')) {
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            $('#imagePreview').attr('src', e.target.result);
                            $('#imagePreviewContainer').show();
                        };
                        reader.readAsDataURL(file);
                    } else {
                        alert('Please upload a valid image file.');
                        $('#imagePreviewContainer').hide();
                    }
                });
            });
        </script>
    @endpush
@endsection