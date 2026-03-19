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

        <h2 class="text-3xl font-bold text-[#1E293B] mb-2 text-center">Forgot Password?</h2>
        <p class="text-gray-500 mb-8 text-center text-sm">Enter your email address and we'll send you a 6-digit reset code.</p>

        @if(session('status'))
            <div class="mb-6 p-3 rounded-xl bg-green-50 text-green-600 text-sm border border-green-100 text-center font-bold">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-3 rounded-xl bg-red-50 text-red-600 text-sm border border-red-100 font-bold">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-bold text-[#1E293B] mb-2">Email Address</label>
                <div class="relative">
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="Enter your email"
                        class="w-full px-4 py-4 bg-[#F8FAFC] border border-gray-100 rounded-2xl outline-none focus:ring-2 focus:ring-green-500/20 focus:border-[#10B981] transition-all">
                </div>
            </div>

            <button type="submit" 
                class="w-full bg-[#10B981] text-white font-bold py-4 rounded-2xl hover:bg-[#059669] shadow-lg shadow-green-100 transition-all active:scale-[0.98]">
                Send Reset Code
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-gray-500">
            Remembered your password? <a href="{{ route('login') }}" class="font-bold text-[#10B981] hover:underline">Sign In</a>
        </p>
    </div>
</div>
@endsection
