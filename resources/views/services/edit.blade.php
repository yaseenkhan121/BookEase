@extends('layouts.app')

@section('content')
<div class="container-fluid p-0 bg-slate-50" style="min-height: calc(100vh - 70px); margin-top: -24px;">
    <div class="p-4 p-md-5">
        
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="display-6 fw-extrabold text-slate-900 mb-1">Edit Service</h2>
                <p class="text-slate-500 font-medium">Update your service details and availability</p>
            </div>
            <a href="{{ route('services.index') }}" class="btn btn-outline-secondary rounded-14 px-4">
                <i class="ph ph-arrow-left mr-2"></i> Back to Catalog
            </a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger border-0 mb-4 rounded-12 shadow-sm">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card card-premium p-4 p-md-5 border-slate-200">
            <form action="{{ route('provider.services.update', $service) }}" method="POST" data-ajax="true">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-8">
                        @if(auth()->user()->isAdmin())
                            <div class="form-group mb-4">
                                <label class="tiny-font font-bold text-slate-500 text-uppercase mb-2">Assign to Provider</label>
                                <select name="provider_id" class="form-control custom-input @error('provider_id') is-invalid @enderror">
                                    <option value="">Select Provider</option>
                                    @foreach($providers as $provider)
                                        <option value="{{ $provider->id }}" {{ old('provider_id', $service->provider_id) == $provider->id ? 'selected' : '' }}>
                                            {{ $provider->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="form-group mb-4">
                            <label class="tiny-font font-bold text-slate-500 text-uppercase mb-2">Service Name</label>
                            <input type="text" name="name" class="form-control custom-input @error('name') is-invalid @enderror" 
                                   placeholder="e.g. Premium Health Consultation" value="{{ old('name', $service->name) }}" required>
                        </div>

                        <div class="form-group mb-4">
                            <label class="tiny-font font-bold text-slate-500 text-uppercase mb-2">Description</label>
                            <textarea name="description" class="form-control custom-input @error('description') is-invalid @enderror" 
                                      rows="5" placeholder="Describe what this service includes..." required>{{ old('description', $service->description) }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group mb-4">
                            <label class="tiny-font font-bold text-slate-500 text-uppercase mb-2">Price (PKR)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-slate-50 border-slate-200">PKR</span>
                                <input type="number" step="0.01" name="price" class="form-control custom-input @error('price') is-invalid @enderror" 
                                       placeholder="0.00" value="{{ old('price', $service->price) }}" required>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="tiny-font font-bold text-slate-500 text-uppercase mb-2">Duration (Minutes)</label>
                            <select name="duration" class="form-control custom-input @error('duration') is-invalid @enderror" required>
                                <option value="15" {{ old('duration', $service->duration) == 15 ? 'selected' : '' }}>15 Mins</option>
                                <option value="30" {{ old('duration', $service->duration) == 30 ? 'selected' : '' }}>30 Mins</option>
                                <option value="45" {{ old('duration', $service->duration) == 45 ? 'selected' : '' }}>45 Mins</option>
                                <option value="60" {{ old('duration', $service->duration) == 60 ? 'selected' : '' }}>1 Hour</option>
                                <option value="90" {{ old('duration', $service->duration) == 90 ? 'selected' : '' }}>1.5 Hours</option>
                                <option value="120" {{ old('duration', $service->duration) == 120 ? 'selected' : '' }}>2 Hours</option>
                            </select>
                        </div>

                        <div class="form-group mb-4">
                            <label class="tiny-font font-bold text-slate-500 text-uppercase mb-2">Status</label>
                            <select name="status" class="form-control custom-input" required>
                                <option value="active" {{ old('status', $service->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $service->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="mt-5">
                            <button type="submit" class="btn btn-modern-primary w-100 py-3 shadow-lg" data-loading-text="Updating...">
                                <i class="ph ph-check-circle mr-2"></i> Update Service
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
