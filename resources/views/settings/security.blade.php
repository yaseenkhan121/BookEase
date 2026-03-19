@extends('settings.layout')

@section('settings_content')
<div class="mb-5">
    <h4 class="section-title mb-1">Change Password</h4>
    <p class="section-subtitle">Ensure your account is using a long, random password to stay secure.</p>

    <form action="{{ route('settings.security.update') }}" method="POST" id="passwordUpdateForm">
        @csrf
        <div class="row g-4">
            <div class="col-12 col-md-8">
                <label class="form-label">Current Password</label>
                <div class="position-relative">
                    <input type="password" id="current_password" name="current_password" class="form-control" placeholder="Enter your current password" required>
                    <button type="button" id="current_password-toggle" onclick="togglePassword('current_password')" class="btn p-0 position-absolute" style="right: 18px; top: 50%; transform: translateY(-50%); color: var(--text-muted); z-index: 10;">
                        <i class="ph ph-eye" style="font-size: 1.25rem;"></i>
                    </button>
                </div>
            </div>
            
            <div class="col-12 col-md-8">
                <label class="form-label">New Password</label>
                <div class="position-relative">
                    <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Minimum 8 characters" required>
                    <button type="button" id="new_password-toggle" onclick="togglePassword('new_password')" class="btn p-0 position-absolute" style="right: 18px; top: 50%; transform: translateY(-50%); color: var(--text-muted); z-index: 10;">
                        <i class="ph ph-eye" style="font-size: 1.25rem;"></i>
                    </button>
                </div>
            </div>

            <div class="col-12 col-md-8">
                <label class="form-label">Confirm New Password</label>
                <div class="position-relative">
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control" placeholder="Repeat your new password" required>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-end gap-3 mt-5">
            <button type="button" class="btn btn-secondary-modern">Cancel</button>
            <button type="submit" class="btn btn-save-modern" data-loading-text="Saving...">Save Password</button>
        </div>
    </form>
</div>

<div class="mb-5">
    <h4 class="section-title mb-1">Connected Accounts</h4>
    <p class="section-subtitle">Manage your third-party account integrations for calendar synchronization.</p>
    
    <div class="row g-3">
        <div class="col-12">
            <div class="p-3 border rounded-xl d-flex align-items-center justify-content-between" style="border-radius: 16px; background-color: var(--bg-body);">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center mr-3" style="width: 48px; height: 48px;">
                        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" style="width: 24px;">
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Google Calendar</h6>
                        @php
                            $googleEmail = auth()->user()->isProvider() ? auth()->user()->providerProfile->google_calendar_email : auth()->user()->google_calendar_email;
                            $isConnected = auth()->user()->isProvider() ? auth()->user()->providerProfile->google_calendar_token : auth()->user()->google_calendar_token;
                        @endphp
                        @if($googleEmail)
                            <p class="text-muted small mb-0">Connected as {{ $googleEmail }}</p>
                        @else
                            <p class="text-muted small mb-0">Not connected</p>
                        @endif
                    </div>
                </div>
                <div>
                    @if($isConnected)
                        <form action="{{ auth()->user()->isProvider() ? route('provider.calendar.disconnect') : route('customer.calendar.disconnect') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-light border btn-sm fw-bold px-3">Disconnect</button>
                        </form>
                    @else
                        <a href="{{ auth()->user()->isProvider() ? route('provider.calendar.connect') : route('customer.calendar.connect') }}" class="btn btn-modern-primary btn-sm px-3">Connect</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<hr class="my-5 opacity-50">


@if(auth()->user()->role !== \App\Models\User::ROLE_ADMIN)
{{-- Delete Account Section --}}
<div class="py-2">
    <h4 class="fw-bold text-danger mb-1 d-flex align-items-center" style="font-size: 1.25rem;">
        <i class="ph ph-trash mr-2"></i> Delete Account
    </h4>
    <p class="section-subtitle mb-4">Permanently delete your account and all associated data.<br>This action cannot be undone.</p>

    <button type="button" class="btn btn-outline-danger px-4 py-2 fw-bold" style="border-radius: var(--saas-radius-md); border-width: 2px;" data-toggle="modal" data-target="#deleteAccountModal">
        Delete My Account
    </button>
</div>
@endif

{{-- Delete Account Modal --}}
<div class="modal fade" id="deleteAccountModal" tabindex="-1" role="dialog" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; background-color: var(--bg-card);">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold" id="deleteAccountModalLabel">Delete Account</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
            </div>
            <form action="{{ route('settings.account.delete') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body px-4 pt-3 pb-4">
                    <p class="text-muted mb-4 line-height-relaxed">Are you sure you want to permanently delete your account? This action <strong class="text-danger">cannot be undone</strong>. All your details, services, and associated data will be wiped immediately.</p>
                    
                    <div class="form-group mb-0">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required placeholder="Enter password to confirm">
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                    <button type="button" class="btn btn-light px-4 fw-bold" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger px-4 fw-bold shadow-sm" data-loading-text="Deleting...">Confirm Deletion</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
    // Password update now relies on current_password verification instead of legacy OTP.
</script>
@endpush
@endsection
