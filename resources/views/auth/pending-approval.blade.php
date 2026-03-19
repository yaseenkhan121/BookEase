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

        <div class="mb-6">
            <div class="w-20 h-20 bg-yellow-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="ph ph-clock text-yellow-500" style="font-size: 2.5rem;"></i>
            </div>
            <h2 class="text-3xl font-bold text-[#1E293B] mb-2">Account Under Review</h2>
            <p class="text-gray-500 text-sm text-balance">
                Thank you for joining BookEase! Your provider application is currently being reviewed by our administration team.
            </p>
        </div>

        <div class="p-6 bg-[#F8FAFC] rounded-2xl border border-gray-100 mb-8 text-left">
            <h6 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Status Overview</h6>
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-5 h-5 rounded-full bg-green-500 flex items-center justify-center text-white">
                        <i class="ph ph-check text-[12px]"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Email Verified</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-5 h-5 rounded-full bg-yellow-400 flex items-center justify-center text-white">
                        <i class="ph ph-spinner text-[12px] animate-spin"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Admin Approval (Pending)</span>
                </div>
            </div>
        </div>

        <p class="text-xs text-gray-400 mb-8">
            We'll notify you via email as soon as your account is approved. Usually, this process takes less than 24 hours.
        </p>

        <div class="space-y-3">
            <a href="{{ route('home') }}" class="block w-full py-4 bg-[#10B981] text-white font-bold rounded-2xl hover:bg-[#059669] transition-all active:scale-[0.98]">
                Back to Website
            </a>
            
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full py-3 text-sm font-bold text-gray-400 hover:text-gray-600 transition-colors">
                    Sign Out
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
