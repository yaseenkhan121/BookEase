@extends('layouts.auth')

@section('content')
<div class="min-h-screen w-full flex items-center justify-center p-4 bg-[#F8FAFC]">
    <div class="bg-white w-full max-w-5xl rounded-[32px] overflow-hidden shadow-2xl flex flex-col md:flex-row min-h-[600px]">
        
        <div class="w-full md:w-1/2 bg-[#F0FDF4] p-12 flex flex-col items-center justify-center text-center">
            <div class="flex items-center gap-2 mb-8">
                <div class="bg-[#10B981] p-2 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <span class="text-2xl font-bold text-[#064E3B]">BookEase</span>
            </div>
            <h2 class="text-3xl font-extrabold text-[#1E293B] mb-4">Start Your Journey</h2>
            <p class="text-gray-500 text-sm max-w-xs leading-relaxed">Join thousands of professionals and clients. Setting up your profile takes less than a minute.</p>
        </div>

        <div class="w-full md:w-1/2 p-12 bg-white flex flex-col justify-center">
            <div class="max-w-md mx-auto w-full">
                <h2 class="text-4xl font-bold text-[#1E293B] mb-6">Create Account</h2>

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-xl text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('register') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-bold text-[#1E293B] mb-1">Full Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required 
                               class="w-full px-4 py-3 bg-[#F8FAFC] border @error('name') border-red-500 @else border-gray-100 @enderror rounded-2xl outline-none focus:border-[#10B981]">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-[#1E293B] mb-1">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" required 
                               class="w-full px-4 py-3 bg-[#F8FAFC] border @error('email') border-red-500 @else border-gray-100 @enderror rounded-2xl outline-none focus:border-[#10B981]">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-[#1E293B] mb-1">I am a...</label>
                        <select name="role" class="w-full px-4 py-3 bg-[#F8FAFC] border border-gray-100 rounded-2xl outline-none focus:border-[#10B981]">
                            <option value="customer" {{ (old('role') ?? request('role')) == 'customer' ? 'selected' : '' }}>Client (Looking to book)</option>
                            <option value="provider" {{ (old('role') ?? request('role')) == 'provider' ? 'selected' : '' }}>Professional (Service Provider)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-[#1E293B] mb-1">Password</label>
                            <div class="relative">
                                <input type="password" id="password" name="password" required 
                                       class="w-full px-4 py-3 bg-[#F8FAFC] border @error('password') border-red-500 @else border-gray-100 @enderror rounded-2xl outline-none focus:border-[#10B981]">
                                <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-green-600 transition-colors">
                                    <i class="ph ph-eye" style="font-size: 1.1rem;"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-[#1E293B] mb-1">Confirm</label>
                            <div class="relative">
                                <input type="password" id="password_confirmation" name="password_confirmation" required 
                                       class="w-full px-4 py-3 bg-[#F8FAFC] border border-gray-100 rounded-2xl outline-none focus:border-[#10B981]">
                                <button type="button" onclick="togglePassword('password_confirmation')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-green-600 transition-colors">
                                    <i class="ph ph-eye" style="font-size: 1.1rem;"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-[#10B981] text-white font-bold py-4 rounded-2xl hover:bg-[#059669] shadow-lg transition-all mt-4">
                        Create Account
                    </button>
                </form>

                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <span class="w-full border-t border-gray-100"></span>
                    </div>
                    <div class="relative flex justify-center text-sm uppercase">
                        <span class="bg-white px-2 text-gray-400 font-medium">Or Register With</span>
                    </div>
                </div>

                <a href="{{ route('google.login') }}" class="w-full flex items-center justify-center gap-3 bg-white border border-gray-100 py-4 rounded-2xl hover:bg-gray-50 transition-all font-bold text-[#1E293B]">
                    <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" class="w-5 h-5">
                    Continue with Google
                </a>


                <p class="mt-6 text-center text-sm text-gray-500">
                    Already have an account? <a href="{{ route('login') }}" class="font-bold text-[#10B981] hover:underline">Sign In</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection