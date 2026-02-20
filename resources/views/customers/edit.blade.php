@extends('layouts.app')

@section('title', 'Edit Customer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center py-2">
            <h3 class="card-title me-auto">Edit Customer</h3>
            <a href="{{ route('customers.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Customers
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('customers.update', $customer->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <!-- Name Field -->
                    <div class="col-12 col-md-4 col-lg-6 mb-3">
                        <label for="name" class="form-label">Name *</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $customer->name) }}" required>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div class="col-12 col-md-4 col-lg-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $customer->email) }}">
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Phone Field -->
                    <div class="col-12 col-md-4 col-lg-6 mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}">
                        @error('phone')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- WhatsApp Field -->
                    <div class="col-12 col-md-4 col-lg-6 mb-3">
                        <label for="whatsapp" class="form-label">WhatsApp</label>
                        <input type="text" class="form-control" id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $customer->whatsapp) }}">
                        @error('whatsapp')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Address Field -->
                    <div class="col-12 col-md-4 col-lg-6 mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address">{{ old('address', $customer->address) }}</textarea>
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
                                <option value="{{ $type->id }}" {{ old('type_id', $customer->type_id) == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('type_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Image Field -->
                    <div class="col-12 col-md-4 col-lg-6 mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input class="form-control" type="file" id="image" name="image" accept="image/*">
                        
                        <!-- Image Preview Container -->
                        <div id="imagePreviewContainer" class="mt-3" style="display: none;">
                            <img id="imagePreview" src="#" alt="Image Preview" class="img-fluid" style="max-width: 150px; max-height: 150px; object-fit: contain;">
                        </div>
                        
                        @error('image')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <!-- Show current image -->
                        @if($customer->image)
                            <div class="mt-2">
                                <img src="{{ asset('storage/'.$customer->image) }}" alt="Current Image" class="img-fluid" style="max-width: 150px; max-height: 150px; object-fit: contain;">
                            </div>
                        @endif
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Customer</button>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Show image preview when a file is selected
            $('#image').change(function() {
                var file = this.files[0];
                
                // Check if the selected file is an image
                if (file && file.type.startsWith('image/')) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview').attr('src', e.target.result);
                        $('#imagePreviewContainer').show();  // Show the image preview container
                    };
                    reader.readAsDataURL(file);
                } else {
                    alert('Please upload a valid image file.');
                    $('#imagePreviewContainer').hide();
                }
            });
        </script>
    @endpush
@endsection
