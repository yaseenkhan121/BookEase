@extends('layouts.auth')

@section('title', 'Verify reset code')

@section('content')
<div class="auth-card animate-fade-in">
    <div class="auth-header text-center mb-5">
        <div class="auth-icon-wrapper mx-auto mb-4">
            <i class="ph-duotone ph-shield-check text-primary h1"></i>
        </div>
        <h2 class="h4 font-bold text-slate-800 dark:text-white">Verify Your Identity</h2>
        <p class="text-slate-500 dark:text-slate-400">Enter the 6-digit code sent to<br><span class="font-bold text-slate-800 dark:text-white">{{ $email }}</span></p>
    </div>

    <form method="POST" action="{{ route('password.otp.verify.post') }}" data-ajax="true">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">

        <div class="form-group mb-4">
            <label class="tiny-font font-bold text-slate-500 text-uppercase mb-2">Reset Code</label>
            <div class="otp-input-wrapper">
                <input type="text" name="otp" 
                       class="form-control custom-input text-center font-bold h2 py-3 @error('otp') is-invalid @enderror" 
                       placeholder="000000" maxlength="6" autofocus required>
                @error('otp')
                    <span class="invalid-feedback text-center mt-2" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <p class="tiny-font text-center mt-3 text-slate-500">Didn't receive a code? <a href="{{ route('password.request') }}" class="text-primary font-bold">Resend OTP</a></p>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm py-3 font-bold mt-2" data-loading-text="Verifying...">
            Verify Code
        </button>
    </form>
</div>

<style>
.otp-input-wrapper .custom-input {
    letter-spacing: 0.5rem;
    font-size: 1.75rem !important;
}
.auth-icon-wrapper {
    width: 64px;
    height: 64px;
    background: rgba(var(--primary-rgb), 0.1);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection
