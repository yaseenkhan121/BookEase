@extends('layouts.app')

@section('title', 'Manage Profile')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-5">
                    <div class="d-flex align-items-center mb-5 gap-3">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-4 text-primary">
                            <i class="ph ph-user-circle-plus style-2" style="font-size: 2rem;"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-1">Business Profile</h3>
                            <p class="text-muted mb-0">Manage your industry, location, and specialization to attract more customers.</p>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success border-0 rounded-4 mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('provider.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row g-4">
                            <!-- Profile Header -->
                            <div class="col-12 mb-4">
                                <div class="d-flex align-items-center gap-4">
                                    <div class="position-relative">
                                        <img src="{{ $provider->avatar_url }}" alt="Profile" id="previewImg" class="rounded-circle border border-4 shadow-sm" style="width: 120px; height: 120px; object-fit: cover; border-color: var(--bg-card) !important;">
                                        <label for="profile_image" class="btn btn-sm btn-dark position-absolute bottom-0 end-0 rounded-circle p-2" style="cursor: pointer;">
                                            <i class="ph ph-camera"></i>
                                            <input type="file" name="profile_image" id="profile_image" hidden onchange="previewImage(this)">
                                        </label>
                                    </div>
                                    <div>
                                        <h4 class="fw-bold mb-1">{{ $provider->owner_name }}</h4>
                                        <span class="badge {{ $provider->status === 'approved' ? 'bg-success' : 'bg-warning' }} rounded-pill px-3">
                                            {{ ucfirst($provider->status) }} Account
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Industrial Information -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Business Name</label>
                                <input type="text" name="business_name" class="form-control rounded-3 py-2" value="{{ old('business_name', $provider->business_name) }}" required placeholder="e.g. HealthCare Clinic">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Industry Category</label>
                                <select name="business_category" class="form-select rounded-3 py-2" required>
                                    <option value="" disabled>Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}" {{ old('business_category', $provider->business_category) === $category ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Specialization / Tagline</label>
                                <input type="text" name="specialization" class="form-control rounded-3 py-2" value="{{ old('specialization', $provider->specialization) }}" placeholder="e.g. Skin Specialist / Luxury Hair Salon / Business Consultant" required>
                            </div>

                            <!-- Contact & Location -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Contact Phone</label>
                                <input type="text" name="phone" class="form-control rounded-3 py-2" value="{{ old('phone', $provider->phone) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">City</label>
                                <input type="text" name="city" class="form-control rounded-3 py-2" value="{{ old('city', $provider->city) }}" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Full Address</label>
                                <input type="text" name="address" class="form-control rounded-3 py-2" value="{{ old('address', $provider->address) }}" required>
                            </div>

                            <div class="col-md-6" hidden>
                                <label class="form-label fw-semibold">Country</label>
                                <input type="text" name="country" class="form-control rounded-3 py-2" value="{{ old('country', $provider->country ?? 'Pakistan') }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">About / Bio</label>
                                <textarea name="bio" class="form-control rounded-3 py-2" rows="5" placeholder="Describe your services and why customers should choose you...">{{ old('bio', $provider->bio) }}</textarea>
                            </div>

                            <div class="col-12 mt-5">
                                <button type="submit" class="btn btn-primary px-5 py-3 rounded-3 fw-bold shadow-sm">
                                    Update Profile Info
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImg').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
