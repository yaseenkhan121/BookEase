@extends('layouts.app')

@section('title', 'Verify OTP - BookEase')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg border-0" style="border-radius: 24px;">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                            <i class="fas fa-shield-alt text-primary fa-2x"></i>
                        </div>
                        <h3 class="fw-bold">Two-Step Verification</h3>
                        <p class="text-muted">
                            Enter the 6-digit code sent to your email to confirm the changes.
                        </p>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('settings.otp.verify.submit') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="otp_code" class="form-label fw-semibold">Verification Code</label>
                            <input type="text" name="otp_code" id="otp_code" 
                                   class="form-control form-control-lg text-center fw-bold letter-spacing-5" 
                                   placeholder="000000" maxlength="6" required autofocus
                                   style="letter-spacing: 0.5rem; font-size: 1.5rem; border-radius: 12px;">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold" style="border-radius: 12px;">
                                Verify & Update
                            </button>
                        </div>
                    </form>

                    <div class="mt-4 text-center">
                        <p class="text-muted mb-2">Didn't receive the code?</p>
                        <form action="{{ route('settings.otp.resend') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-link text-primary p-0 fw-semibold text-decoration-none">
                                Resend Verification Code
                            </button>
                        </form>
                    </div>

                    <div class="mt-4 text-center border-top pt-3">
                        <a href="{{ route('settings.profile') }}" class="text-muted text-decoration-none small">
                            <i class="fas fa-arrow-left me-1"></i> Cancel and go back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .letter-spacing-5 {
        letter-spacing: 0.5rem;
    }
    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.25);
    }
</style>
@endsection
