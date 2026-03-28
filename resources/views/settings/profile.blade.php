@extends('settings.layout')

@section('settings_content')
<div class="mb-4">
    <h4 class="section-title mb-1">Profile Information</h4>
    <p class="section-subtitle">Update your account profile information and address.</p>

    <form action="{{ route('settings.profile.update') }}" method="POST" enctype="multipart/form-data" id="profileInfoForm" data-ajax="true">
        @csrf
        <div class="row g-4">
            {{-- Profile Photo Section --}}
            <div class="col-12 mb-2">
                    <div class="position-relative">
                        <div class="avatar-edit-wrapper" onclick="document.getElementById('profile_image_input').click()">
                            <img src="{{ auth()->user()->avatar_url }}" id="profile-preview" class="avatar-image">
                            <div class="avatar-camera-icon">
                                <i class="ph-fill ph-camera"></i>
                            </div>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h6 class="font-weight-bold mb-1 text-dark">Profile Photo</h6>
                        <p class="small text-muted mb-0">Click to upload a new photo. JPG, PNG or WEBP.</p>
                        <button type="button" class="btn btn-link p-0 text-primary small font-weight-bold mt-1" style="color: #1F7A63 !important;" onclick="document.getElementById('profile_image_input').click()">Change Photo</button>
                    </div>
                <input type="file" name="profile_image" id="profile_image_input" class="d-none" accept="image/*" onchange="previewImage(this)">
            </div>

            {{-- Basic Info --}}
            <div class="col-md-6">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', auth()->user()->phone_number ?? '') }}">
            </div>

            @if(auth()->user()->role === 'provider')
                @php $provider = auth()->user()->providerProfile; @endphp
                <div class="col-md-6">
                    <label class="form-label">Business Name</label>
                    <input type="text" name="business_name" class="form-control" value="{{ old('business_name', $provider->business_name ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Business Category</label>
                    <select name="business_category" class="form-control">
                        <option value="">Select Category</option>
                        @foreach(\App\Models\Provider::CATEGORIES as $cat)
                            <option value="{{ $cat }}" {{ (old('business_category', $provider->business_category ?? '') == $cat) ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Specialization</label>
                    <input type="text" name="specialization" class="form-control" value="{{ old('specialization', $provider->specialization ?? '') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Business Address</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $provider->address ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city', $provider->city ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Country</label>
                    <input type="text" name="country" class="form-control" value="{{ old('country', $provider->country ?? '') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Professional Bio</label>
                    <textarea name="bio" class="form-control" rows="4" style="resize: none;">{{ old('bio', $provider->bio ?? '') }}</textarea>
                </div>
            @endif
        </div>

        <div class="d-flex justify-content-end gap-3 mt-5">
            <button type="button" class="btn btn-secondary-modern" onclick="window.history.back()">Cancel</button>
            <button type="submit" class="btn btn-save-modern" data-loading-text="Saving...">Save Profile</button>
        </div>
    </form>
</div>

@push('styles')
<style>
    .avatar-edit-wrapper {
        width: 110px;
        height: 110px;
        position: relative;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .avatar-image, .avatar-placeholder {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid var(--saas-primary);
        box-shadow: 0 5px 15px rgba(31, 122, 99, 0.15);
        transition: all 0.3s ease;
    }
    .avatar-placeholder {
        background: #f1f5f9;
        color: var(--saas-primary) !important;
        font-size: 2.2rem;
        border-color: var(--saas-border);
    }
    .avatar-edit-wrapper:hover .avatar-image, 
    .avatar-edit-wrapper:hover .avatar-placeholder {
        transform: scale(1.02);
        border-color: #2da884;
        box-shadow: 0 8px 20px rgba(31, 122, 99, 0.25);
    }
    .avatar-camera-icon {
        position: absolute;
        bottom: 5px;
        right: 5px;
        width: 32px;
        height: 32px;
        background: var(--saas-primary-gradient);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid var(--bg-card);
        box-shadow: 0 3px 8px rgba(0,0,0,0.15);
        transition: all 0.3s ease;
        z-index: 2;
    }
    .avatar-edit-wrapper:hover .avatar-camera-icon {
        transform: scale(1.1);
        background: #166551;
    }
    
    body.dark-mode .avatar-placeholder {
        background: #0f172a;
        color: #10b981 !important;
        border-color: #1e293b;
    }
    body.dark-mode .avatar-camera-icon {
        border-color: #0f172a;
    }
</style>
@endpush

@push('scripts')
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                let preview = document.getElementById('profile-preview');
                if(preview.tagName.toLowerCase() === 'div') {
                    let img = document.createElement('img');
                    img.id = 'profile-preview';
                    img.className = 'avatar-image';
                    img.src = e.target.result;
                    preview.parentNode.replaceChild(img, preview);
                } else {
                    preview.src = e.target.result;
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
<script>
    // Disable AJAX when email or phone is being changed (OTP redirect is needed)
    (function() {
        const form = document.getElementById('profileInfoForm');
        const emailInput = form?.querySelector('input[name="email"]');
        const phoneInput = form?.querySelector('input[name="phone"]');
        
        const originalEmail = emailInput?.value || '';
        const originalPhone = phoneInput?.value || '';

        if (form) {
            form.addEventListener('submit', function(e) {
                const emailChanged = emailInput && emailInput.value !== originalEmail;
                const phoneChanged = phoneInput && phoneInput.value !== originalPhone;
                
                if (emailChanged || phoneChanged) {
                    form.removeAttribute('data-ajax');
                }
            }, true);
        }
    })();
</script>
@endpush
@endsection
