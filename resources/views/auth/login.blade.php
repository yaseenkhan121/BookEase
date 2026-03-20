@extends('layouts.auth')

@section('content')
<div class="min-h-screen w-full flex items-center justify-center p-4 bg-[#F8FAFC]">
    <div class="bg-white w-full max-w-5xl rounded-[32px] overflow-hidden shadow-2xl flex flex-col md:flex-row min-h-[600px]">
        
        <div class="w-full md:w-1/2 bg-[#F0FDF4] p-12 flex flex-col items-center justify-center text-center">
            <div class="flex items-center gap-2 mb-12">
                <div class="bg-[#10B981] p-2 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <span class="text-2xl font-bold text-[#064E3B]">BookEase</span>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm w-full max-w-xs mb-8 relative">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-full bg-[#10B981]"></div>
                    <div class="space-y-2">
                        <div class="h-2 w-24 bg-gray-100 rounded"></div>
                        <div class="h-2 w-16 bg-gray-50 rounded"></div>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="h-10 w-full bg-[#F8FAFC] rounded-xl border border-gray-100 flex items-center justify-end px-3">
                        <div class="w-4 h-4 rounded-full border-2 border-green-400"></div>
                    </div>
                    <div class="h-10 w-full bg-[#F8FAFC] rounded-xl border border-gray-100"></div>
                </div>
                <div class="absolute -bottom-4 -right-4 bg-white p-3 rounded-xl shadow-lg border border-gray-50 flex items-center gap-3 w-48">
                    <div class="bg-[#10B981] p-1.5 rounded-full text-white">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg>
                    </div>
                    <div class="text-left">
                        <p class="text-[10px] font-bold">Appointment Confirmed</p>
                        <p class="text-[8px] text-gray-400">Today at 2:00 PM</p>
                    </div>
                </div>
            </div>

            <h2 class="text-3xl font-extrabold text-[#1E293B] mb-4">Welcome Back!</h2>
            <p class="text-gray-500 text-sm max-w-xs">Access your appointments and manage your schedule with ease.</p>
        </div>

        <div class="w-full md:w-1/2 p-12 bg-white flex flex-col justify-center">
            <div class="max-w-md mx-auto w-full">
                <h2 class="text-4xl font-bold text-[#1E293B] mb-2">Sign In</h2>
                <p class="text-gray-500 mb-8">Enter your credentials to access your account</p>

                @if(session('success'))
                    <div class="mb-6 p-4 rounded-2xl bg-green-50 text-green-700 text-sm border border-green-100 flex items-start gap-3">
                        <i class="ph ph-check-circle mt-1" style="font-size: 1.2rem;"></i>
                        <div>
                            <p class="font-bold mb-0">Success</p>
                            <p class="mb-0">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('status'))
                    <div class="mb-6 p-4 rounded-2xl bg-blue-50 text-blue-700 text-sm border border-blue-100 flex items-start gap-3">
                        <i class="ph ph-info mt-1" style="font-size: 1.2rem;"></i>
                        <div>
                            <p class="font-bold mb-0">Notice</p>
                            <p class="mb-0">{{ session('status') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 rounded-2xl bg-red-50 text-red-600 text-sm border border-red-100 flex items-start gap-3">
                        <i class="ph ph-warning-circle mt-1" style="font-size: 1.2rem;"></i>
                        <div>
                            <p class="font-bold mb-0">Error</p>
                            <p class="mb-0">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 p-4 rounded-2xl bg-red-50 text-red-600 text-sm border border-red-100 flex items-start gap-3">
                        <i class="ph ph-warning-circle mt-1" style="font-size: 1.2rem;"></i>
                        <div>
                            <p class="font-bold mb-0">Error</p>
                            <p class="mb-0">{{ $errors->first() }}</p>
                        </div>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-[#334155] mb-2 px-1">Email Address</label>
                        <div class="relative">
                            <input type="email" name="email" value="{{ old('email') }}" required placeholder="Enter your email"
                                class="w-full px-4 py-4 bg-[#F8FAFC] border border-gray-100 rounded-2xl outline-none focus:ring-2 focus:ring-green-500/20 focus:border-[#10B981] transition-all">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#334155] mb-2 px-1">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required placeholder="Enter your password"
                                class="w-full px-4 py-4 bg-[#F8FAFC] border border-gray-100 rounded-2xl outline-none focus:ring-2 focus:ring-green-500/20 focus:border-[#10B981] transition-all">
                            <button type="button" onclick="togglePassword('password')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-green-600 transition-colors">
                                <i class="ph ph-eye" id="password-icon" style="font-size: 1.25rem;"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center text-sm font-medium text-gray-500 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-5 h-5 rounded border-gray-300 text-[#10B981] focus:ring-[#10B981] mr-3">
                            Remember me
                        </label>
                        <a href="{{ route('password.request') }}" class="text-sm font-bold text-[#10B981] hover:text-[#059669]">Forgot Password?</a>
                    </div>

                    <button type="submit" 
                        class="w-full bg-[#10B981] text-white font-bold py-4 rounded-2xl hover:bg-[#059669] shadow-lg shadow-green-100 transition-all active:scale-[0.98]">
                        Sign In
                    </button>
                </form>

                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <span class="w-full border-t border-gray-100"></span>
                    </div>
                    <div class="relative flex justify-center text-sm uppercase">
                        <span class="bg-white px-2 text-gray-400 font-medium">Or continue with</span>
                    </div>
                </div>

                <a href="{{ route('google.login') }}" class="w-full flex items-center justify-center gap-3 bg-white border border-gray-100 py-4 rounded-2xl hover:bg-gray-50 transition-all font-bold text-[#1E293B]">
                    <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" class="w-5 h-5">
                    Continue with Google
                </a>


                <p class="mt-8 text-center text-sm text-gray-500">
                    Don't have an account? <a href="/register" class="font-bold text-[#10B981] hover:underline">Sign Up</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection