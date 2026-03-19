@extends('layouts.auth')

@section('content')
<div class="min-h-screen w-full flex items-center justify-center p-4 bg-[#F8FAFC]">
    <div class="bg-white w-full max-w-md rounded-[32px] overflow-hidden shadow-2xl p-12 text-center">
        <div class="flex items-center gap-2 mb-8 justify-center">
            <div class="bg-[#10B981] p-2 rounded-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <span class="text-2xl font-bold text-[#064E3B]">BookEase</span>
        </div>

        <h2 class="text-3xl font-bold text-[#1E293B] mb-2">Verify Your Account</h2>
        <p class="text-gray-500 mb-8 text-sm text-balance">Please verify your email address by clicking the link we just emailed to you. If you didn't receive the email, we will gladly send you another.</p>

        @if(session('status') == 'verification-link-sent')
            <div class="mb-6 p-4 rounded-2xl bg-green-50 text-green-600 text-sm border border-green-100 flex items-center gap-3">
                <i class="ph ph-check-circle" style="font-size: 1.2rem;"></i>
                <p class="mb-0 font-medium">A new verification link has been sent!</p>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-red-50 text-red-600 text-sm border border-red-100 flex items-center gap-3">
                <i class="ph ph-warning-circle" style="font-size: 1.2rem;"></i>
                <p class="mb-0 font-medium">{{ $errors->first() }}</p>
            </div>
        @endif

        <div class="space-y-6">
            <!-- Email Method -->
            <div class="p-6 bg-[#F8FAFC] border border-gray-100 rounded-[24px] text-left">
                <div class="flex items-center gap-4 mb-6">
                    <div class="bg-white p-3 rounded-xl text-[#10B981] shadow-sm">
                        <i class="ph ph-envelope-simple" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-[#1E293B]">Email Verification</p>
                        <p class="text-xs text-gray-400">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                
                <form action="{{ route('verification.send') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-4 bg-[#10B981] text-white font-bold rounded-2xl hover:bg-[#059669] shadow-lg shadow-green-100 transition-all active:scale-[0.98]">
                        Resend Verification Email
                    </button>
                </form>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full py-2 text-sm font-bold text-gray-400 hover:text-gray-600 transition-colors">
                    Sign out and try another email
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
