@extends('layouts.app')

@section('content')
<div class="container-fluid p-0" style="margin-top: -24px; background-color: var(--bg-body);">
    <div class="p-4 p-md-5">

        {{-- Breadcrumb & Header --}}
        <div class="mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <a href="{{ route('admin.users.index') }}" class="btn btn-light border-0 px-3 rounded-pill text-muted small fw-bold shadow-sm">
                <i class="ph ph-arrow-left mr-1"></i> Back to Users
            </a>
            <div class="d-flex gap-2">
                @if($user->role === 'provider')
                    <span class="badge bg-primary text-white px-3 py-2 rounded-pill small fw-bold">SERVICE PROVIDER</span>
                @else
                    <span class="badge bg-info text-white px-3 py-2 rounded-pill small fw-bold">CUSTOMER ACCOUNT</span>
                @endif
            </div>
        </div>

        <div class="row">
            {{-- User Profile Sidebar --}}
            <div class="col-lg-4 mb-4">
                <div class="card card-premium p-4 text-center border-0 shadow-sm" style="border-radius: 24px;">
                    <div class="mb-4 position-relative">
                        <div class="mx-auto user-avatar-wrapper shadow-md" style="width: 110px; height: 110px; border: 4px solid #fff; background: var(--slate-50);">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div class="mt-3">
                            <h3 class="fw-bold text-dark mb-1" style="font-size: 1.6rem; letter-spacing: -0.02em;">{{ $user->name }}</h3>
                            <p class="text-muted small fw-medium mb-0">{{ $user->email }}</p>
                        </div>
                    </div>

                    <div class="px-2">
                        <div class="d-flex align-items-center mb-3 p-3 rounded-xl bg-light" style="border-radius: 16px;">
                            <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center mr-3" style="width: 36px; height: 36px; color: var(--primary);">
                                <i class="ph ph-phone"></i>
                            </div>
                            <div class="text-left">
                                <p class="text-muted tiny-font mb-0 font-weight-bold">Phone Number</p>
                                <p class="mb-0 fw-bold text-dark small">{{ $user->phone_number ?: 'Not provided' }}</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3 p-3 rounded-xl bg-light" style="border-radius: 16px;">
                            <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center mr-3" style="width: 36px; height: 36px; color: #3B82F6;">
                                <i class="ph ph-calendar-blank"></i>
                            </div>
                            <div class="text-left">
                                <p class="text-muted tiny-font mb-0 font-weight-bold">Member Since</p>
                                <p class="mb-0 fw-bold text-dark small">{{ $user->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-4 p-3 rounded-xl bg-light" style="border-radius: 16px;">
                            <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center mr-3" style="width: 36px; height: 36px; color: {{ $user->email_verified_at ? '#10B981' : '#EF4444' }};">
                                <i class="ph {{ $user->email_verified_at ? 'ph-check-circle' : 'ph-x-circle' }}"></i>
                            </div>
                            <div class="text-left">
                                <p class="text-muted tiny-font mb-0 font-weight-bold">Verification</p>
                                <p class="mb-0 fw-bold {{ $user->email_verified_at ? 'text-success' : 'text-danger' }} small">
                                    {{ $user->email_verified_at ? 'Verified Account' : 'Pending Verification' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="d-grid gap-2 px-2">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-modern-primary w-100 py-2.5 d-flex align-items-center justify-content-center gap-2" style="border-radius: 12px;">
                            <i class="ph ph-note-pencil"></i> Edit Profile
                        </a>

                        @if(!$user->email_verified_at)
                        <form action="{{ route('admin.users.verify-email', $user) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-light border w-100 py-2 fw-bold text-success" style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="ph ph-seal-check mr-1"></i> Verify Manually
                            </button>
                        </form>
                        @endif

                        <div class="row g-2 mt-1">
                            <div class="col-6">
                                <form action="{{ route('admin.users.reset-password', $user) }}" method="POST" onsubmit="return confirm('Reset password?');">
                                    @csrf
                                    <button type="submit" class="btn btn-light border w-100 py-2 small fw-bold" style="border-radius: 10px; color: #D97706;">
                                        <i class="ph ph-key"></i> Reset
                                    </button>
                                </form>
                            </div>
                            <div class="col-6">
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete user permanently?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-light border w-100 py-2 small fw-bold" style="border-radius: 10px; color: #EF4444;">
                                        <i class="ph ph-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="col-lg-8">
                {{-- Stats Row --}}
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="card card-premium p-4 border-0 shadow-sm h-100 d-flex flex-row align-items-center" style="border-radius: 20px;">
                            <div class="rounded-pill p-3 mr-4 d-flex align-items-center justify-content-center" style="background: rgba(30, 41, 59, 0.05); color: #1E293B; width: 60px; height: 60px;">
                                <i class="ph ph-calendar-check" style="font-size: 1.8rem;"></i>
                            </div>
                            <div>
                                <p class="text-muted tiny-font mb-0 font-weight-bold">Total Bookings</p>
                                <h2 class="mb-0 fw-bold">{{ $bookingsCount }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-premium p-4 border-0 shadow-sm h-100 d-flex flex-row align-items-center" style="border-radius: 20px;">
                            <div class="rounded-pill p-3 mr-4 d-flex align-items-center justify-content-center" style="background: rgba(var(--primary-rgb), 0.05); color: var(--primary); width: 60px; height: 60px;">
                                <i class="ph ph-package" style="font-size: 1.8rem;"></i>
                            </div>
                            <div>
                                <p class="text-muted tiny-font mb-0 font-weight-bold">Active Services</p>
                                <h2 class="mb-0 fw-bold">{{ $servicesCount }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Provider Details (if applicable) --}}
                @if($user->isProvider() && $user->providerProfile)
                <div class="card card-premium p-4 mb-4 border-0 shadow-sm" style="border-radius: 24px;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                            <i class="ph ph-briefcase mr-2 text-primary"></i> Business Information
                        </h5>
                        <form action="{{ route('admin.users.toggle-provider', $user) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm px-3 rounded-pill fw-bold {{ $user->providerProfile->status === 'approved' ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                {{ $user->providerProfile->status === 'approved' ? 'Suspend Provider' : 'Approve Application' }}
                            </button>
                        </form>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted tiny-font font-weight-bold mb-1">BUSINESS NAME</label>
                            <p class="fw-bold text-dark">{{ $user->providerProfile->business_name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted tiny-font font-weight-bold mb-1">CATEGORY</label>
                            <p class="fw-bold text-dark">
                                <span class="badge bg-light border text-muted px-2 py-1">{{ $user->providerProfile->business_category }}</span>
                            </p>
                        </div>
                        <div class="col-12">
                            <label class="text-muted tiny-font font-weight-bold mb-1">SPECIALIZATION</label>
                            <p class="text-dark">{{ $user->providerProfile->specialization ?: 'No specialization listed.' }}</p>
                        </div>
                    </div>

                    @if($providerSetup)
                    <div class="bg-light p-4 rounded-xl" style="border-radius: 20px;">
                        <h6 class="tiny-font font-weight-bold text-muted mb-3">ONBOARDING PROGRESS</h6>
                        @php
                            $pSteps = [
                                ['label' => 'Profile Setup', 'done' => $providerSetup['profile_complete']],
                                ['label' => 'Service Catalog', 'done' => $providerSetup['services_added']],
                                ['label' => 'Weekly Availability', 'done' => $providerSetup['availability_set']],
                            ];
                            $percent = (collect($pSteps)->where('done', true)->count() / count($pSteps)) * 100;
                        @endphp
                        <div class="progress mb-4" style="height: 8px; border-radius: 4px;">
                            <div class="progress-bar bg-primary" style="width: {{ $percent }}%;"></div>
                        </div>
                        <div class="row">
                            @foreach($pSteps as $s)
                            <div class="col-md-4 mb-2 mb-md-0">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ph {{ $s['done'] ? 'ph-check-circle text-success' : 'ph-circle text-muted' }}" style="font-size: 1.2rem;"></i>
                                    <span class="small {{ $s['done'] ? 'fw-bold text-dark' : 'text-muted' }}">{{ $s['label'] }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Metadata --}}
                <div class="card card-premium p-4 border-0 shadow-sm" style="border-radius: 24px;">
                    <h5 class="fw-bold text-dark mb-4 d-flex align-items-center">
                        <i class="ph ph-info mr-2 text-muted"></i> Account Metadata
                    </h5>
                    <div class="row">
                        <div class="col-6 col-md-4 mb-3">
                            <p class="text-muted tiny-font mb-1 font-weight-bold">USER IDENTIFIER</p>
                            <p class="fw-bold text-dark mb-0">#REF-00{{ $user->id }}</p>
                        </div>
                        <div class="col-6 col-md-4 mb-3">
                            <p class="text-muted tiny-font mb-1 font-weight-bold">EMAIL STATUS</p>
                            <p class="mb-0 fw-bold {{ $user->email_verified_at ? 'text-success' : 'text-muted' }}">
                                {{ $user->email_verified_at ? 'Verified' : 'Unverified' }}
                            </p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="text-muted tiny-font mb-1 font-weight-bold">LAST SYSTEM UPDATE</p>
                            <p class="fw-bold text-dark mb-0 small">{{ $user->updated_at->format('M d, Y @ H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
