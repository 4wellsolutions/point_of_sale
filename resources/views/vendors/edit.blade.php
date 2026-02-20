<!-- resources/views/vendors/edit.blade.php -->

@extends('layouts.app')

@section('title', 'Edit Vendor')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('vendors.index') }}">Vendors</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Edit Vendor</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('vendors.update', $vendor->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Name Field -->
                <div class="mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input 
                        type="text" 
                        class="form-control @error('name') is-invalid @enderror" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $vendor->name) }}" 
                        placeholder="Enter Vendor Name" 
                        required
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email Field -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input 
                        type="email" 
                        class="form-control @error('email') is-invalid @enderror" 
                        id="email" 
                        name="email" 
                        value="{{ old('email', $vendor->email) }}" 
                        placeholder="Enter Email"
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Phone Field -->
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input 
                        type="text" 
                        class="form-control @error('phone') is-invalid @enderror" 
                        id="phone" 
                        name="phone" 
                        value="{{ old('phone', $vendor->phone) }}" 
                        placeholder="Enter Phone Number"
                    >
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- WhatsApp Field -->
                <div class="mb-3">
                    <label for="whatsapp" class="form-label">WhatsApp</label>
                    <input 
                        type="text" 
                        class="form-control @error('whatsapp') is-invalid @enderror" 
                        id="whatsapp" 
                        name="whatsapp" 
                        value="{{ old('whatsapp', $vendor->whatsapp) }}" 
                        placeholder="Enter WhatsApp Number"
                    >
                    @error('whatsapp')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Address Field -->
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea 
                        class="form-control @error('address') is-invalid @enderror" 
                        id="address" 
                        name="address" 
                        rows="3" 
                        placeholder="Enter Address">{{ old('address', $vendor->address) }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Type Dropdown -->
                <div class="mb-3">
                    <label for="type_id" class="form-label">Type</label>
                    <select 
                        class="form-select @error('type_id') is-invalid @enderror" 
                        id="type_id" 
                        name="type_id"
                    >
                        <option value="">Select Type</option>
                        @foreach($types as $type)
                            <option 
                                value="{{ $type->id }}" 
                                {{ (old('type_id', $vendor->type_id) == $type->id) ? 'selected' : '' }}
                            >
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('type_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Image Upload Field -->
                <div class="mb-3">
                    <label for="image" class="form-label">Vendor Image</label>
                    <input 
                        class="form-control @error('image') is-invalid @enderror" 
                        type="file" 
                        id="image" 
                        name="image"
                        accept="image/*"
                    >
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <!-- Display Current Image -->
                    @if($vendor->image)
                        <div class="mt-2">
                            <p>Current Image:</p>
                            <img src="{{ asset('storage/' . $vendor->image) }}" alt="{{ $vendor->name }}" width="100" class="img-fluid">
                        </div>
                    @endif

                    <!-- Image Preview (Optional) -->
                    <div class="mt-2" id="imagePreview" style="display: none;">
                        <p>New Image Preview:</p>
                        <img src="#" alt="Image Preview" width="100" class="img-fluid">
                    </div>
                </div>

                <!-- Submit and Cancel Buttons -->
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Vendor
                </button>
                <a href="{{ route('vendors.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Optional: Image Preview Script
        document.getElementById('image').addEventListener('change', function(event) {
            const preview = document.getElementById('imagePreview');
            const previewImage = preview.querySelector('img');
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });
    </script>
@endpush
