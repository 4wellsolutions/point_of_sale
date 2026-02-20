@extends('layouts.app')

@section('title', 'Settings')
@section('page_title', 'Application Settings')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Settings</li>
@endsection

@section('content')

    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        {{-- App / Branding --}}
        <div class="card settings-card mb-4">
            <div class="card-header settings-header">
                <i class="fas fa-palette me-2"></i>Application Branding
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="app_name" class="form-label fw-semibold">Application Name <span
                                class="text-danger">*</span></label>
                        <input type="text" name="app_name" id="app_name"
                            class="form-control @error('app_name') is-invalid @enderror"
                            value="{{ old('app_name', $settings['app_name'] ?? 'POS System') }}" required>
                        <small class="text-muted">Displayed on sidebar, browser title, reports, and footer.</small>
                        @error('app_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="currency_symbol" class="form-label fw-semibold">Currency Symbol <span
                                class="text-danger">*</span></label>
                        <input type="text" name="currency_symbol" id="currency_symbol"
                            class="form-control @error('currency_symbol') is-invalid @enderror"
                            value="{{ old('currency_symbol', $settings['currency_symbol'] ?? '$') }}" required>
                        <small class="text-muted">e.g. $, €, £, ₹, ₨</small>
                        @error('currency_symbol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="currency_code" class="form-label fw-semibold">Currency Code</label>
                        <input type="text" name="currency_code" id="currency_code"
                            class="form-control @error('currency_code') is-invalid @enderror"
                            value="{{ old('currency_code', $settings['currency_code'] ?? 'USD') }}">
                        <small class="text-muted">e.g. USD, EUR, PKR</small>
                        @error('currency_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Business Information --}}
        <div class="card settings-card mb-4">
            <div class="card-header settings-header">
                <i class="fas fa-building me-2"></i>Business Information
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="business_name" class="form-label fw-semibold">Business Name</label>
                        <input type="text" name="business_name" id="business_name"
                            class="form-control @error('business_name') is-invalid @enderror"
                            value="{{ old('business_name', $settings['business_name'] ?? '') }}">
                        @error('business_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="tax_number" class="form-label fw-semibold">Tax / Registration No.</label>
                        <input type="text" name="tax_number" id="tax_number"
                            class="form-control @error('tax_number') is-invalid @enderror"
                            value="{{ old('tax_number', $settings['tax_number'] ?? '') }}">
                        @error('tax_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-12">
                        <label for="business_address" class="form-label fw-semibold">Business Address</label>
                        <textarea name="business_address" id="business_address" rows="2"
                            class="form-control @error('business_address') is-invalid @enderror">{{ old('business_address', $settings['business_address'] ?? '') }}</textarea>
                        @error('business_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="business_phone" class="form-label fw-semibold">Contact Number</label>
                        <input type="text" name="business_phone" id="business_phone"
                            class="form-control @error('business_phone') is-invalid @enderror"
                            value="{{ old('business_phone', $settings['business_phone'] ?? '') }}">
                        @error('business_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="business_email" class="form-label fw-semibold">Business Email</label>
                        <input type="email" name="business_email" id="business_email"
                            class="form-control @error('business_email') is-invalid @enderror"
                            value="{{ old('business_email', $settings['business_email'] ?? '') }}">
                        @error('business_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="d-flex gap-2 mb-4">
            <button type="submit" class="btn btn-success btn-lg px-4">
                <i class="fas fa-save me-2"></i>Save Settings
            </button>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg px-4">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </form>

@endsection

@push('styles')
    <style>
        .settings-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .settings-header {
            background: linear-gradient(135deg, #2c3e50 0%, #3a536b 100%);
            color: #fff;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 12px 20px;
            border: none;
        }

        .settings-card .card-body {
            padding: 20px;
        }

        .form-label {
            font-size: 0.875rem;
            margin-bottom: 4px;
        }
    </style>
@endpush