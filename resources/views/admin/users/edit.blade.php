@extends('layouts.app')

@section('content')
<div class="container-fluid p-0" style="min-height: calc(100vh - 70px); margin-top: -24px; background-color: var(--bg-body);">
    <div class="p-4 p-md-5">

        {{-- Breadcrumb --}}
        <div class="mb-4">
            <a href="{{ route('admin.users.index') }}" class="text-muted text-decoration-none small">
                <i class="ph ph-arrow-left mr-1"></i> Back to Users
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-premium p-4 p-md-5">
                    <div class="d-flex align-items-center mb-4">
                        <img src="{{ $user->avatar_url }}" class="rounded-circle mr-3" width="56" height="56" style="object-fit: cover; border: 2px solid var(--border-color);">
                        <div>
                            <h4 class="fw-bold mb-0">Edit User</h4>
                            <p class="text-muted small mb-0">Updating: {{ $user->name }} ({{ $user->email }})</p>
                        </div>
                    </div>

                    @if(session('error'))
                    <div class="alert border-0 mb-4" style="background: #FEF2F2; border-left: 4px solid #EF4444 !important; border-radius: 12px;">
                        <i class="ph ph-warning-circle mr-1 text-danger"></i> <span class="fw-bold">{{ session('error') }}</span>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="alert border-0 mb-4" style="background: #FEF2F2; border-left: 4px solid #EF4444 !important; border-radius: 12px;">
                        <ul class="mb-0 pl-3">
                            @foreach($errors->all() as $error)
                                <li class="small text-danger fw-bold">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        {{-- Name --}}
                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Full Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $user->name) }}" required
                                   style="border-radius: 12px; padding: 12px 16px;">
                        </div>

                        {{-- Email --}}
                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Email Address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $user->email) }}" required
                                   style="border-radius: 12px; padding: 12px 16px;">
                        </div>

                        {{-- Phone --}}
                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Phone Number</label>
                            <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" 
                                   value="{{ old('phone_number', $user->phone_number) }}" placeholder="Optional"
                                   style="border-radius: 12px; padding: 12px 16px;">
                        </div>

                        {{-- Role --}}
                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Role</label>
                            <select name="role" class="form-control @error('role') is-invalid @enderror"
                                    style="border-radius: 12px; padding: 12px 16px;">
                                <option value="customer" {{ old('role', $user->role) === 'customer' ? 'selected' : '' }}>Customer</option>
                                <option value="provider" {{ old('role', $user->role) === 'provider' ? 'selected' : '' }}>Provider</option>
                            </select>
                            <small class="text-muted mt-1 d-block">
                                <i class="ph ph-info mr-1"></i> Changing to Provider will auto-create a provider profile if one doesn't exist.
                            </small>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3" style="border-top: 1px solid var(--border-color);">
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary font-weight-bold" style="border-radius: 12px; padding: 10px 24px;">
                                Cancel
                            </a>
                            <button type="submit" class="btn text-white font-weight-bold" style="background: var(--primary); border-radius: 12px; padding: 10px 32px;">
                                <i class="ph ph-floppy-disk mr-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
