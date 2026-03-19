@extends('layouts.auth')

@section('content')
<div class="min-h-screen w-full flex items-center justify-center p-4 bg-[#F8FAFC]">
    <div class="bg-white w-full max-w-md rounded-[32px] overflow-hidden shadow-2xl p-12">
        <div class="flex items-center gap-2 mb-12 justify-center">
            <div class="bg-[#10B981] p-2 rounded-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <span class="text-2xl font-bold text-[#064E3B]">BookEase</span>
        </div>

        <h2 class="text-3xl font-bold text-[#1E293B] mb-2 text-center">New Password</h2>
        <p class="text-gray-500 mb-8 text-center text-sm">Create a secure new password for your account.</p>

        @if($errors->any())
            <div class="mb-6 p-3 rounded-xl bg-red-50 text-red-600 text-sm border border-red-100 font-bold">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.reset.post') }}" method="POST" class="space-y-6" data-ajax="true">
            @csrf
            
            <div>
                <label class="block text-sm font-bold text-[#1E293B] mb-2">Email Address</label>
                <input type="email" name="email" value="{{ $email }}" readonly
                    class="w-full px-4 py-4 bg-[#F1F5F9] border border-gray-100 rounded-2xl outline-none opacity-75 cursor-not-allowed">
            </div>

            <div>
                <label class="block text-sm font-bold text-[#1E293B] mb-2">New Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required placeholder="Min 8 characters"
                        class="w-full px-4 py-4 bg-[#F8FAFC] border border-gray-100 rounded-2xl outline-none focus:ring-2 focus:ring-green-500/20 focus:border-[#10B981] transition-all">
                    <button type="button" onclick="togglePassword('password')" id="password-toggle" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-green-600 transition-colors">
                        <i class="ph ph-eye" style="font-size: 1.25rem;"></i>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-[#1E293B] mb-2">Confirm Password</label>
                <div class="relative">
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Repeat new password"
                        class="w-full px-4 py-4 bg-[#F8FAFC] border border-gray-100 rounded-2xl outline-none focus:ring-2 focus:ring-green-500/20 focus:border-[#10B981] transition-all">
                </div>
            </div>

            <button type="submit" 
                class="w-full bg-[#10B981] text-white font-bold py-4 rounded-2xl hover:bg-[#059669] shadow-lg shadow-green-100 transition-all active:scale-[0.98]"
                data-loading-text="Saving Changes...">
                Update Password
            </button>
        </form>
    </div>
</div>
@endsection
