@extends('layouts.auth')

@section('content')
<div class="min-h-screen w-full flex items-center justify-center p-4 bg-[#F8FAFC]">
    <div class="bg-white w-full max-w-lg rounded-[32px] overflow-hidden shadow-2xl p-12">
        <div class="text-center mb-10">
            <div class="bg-[#10B981] p-3 rounded-2xl inline-block mb-6 shadow-lg shadow-green-100">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-[#1E293B] mb-2">Welcome!</h2>
            <p class="text-gray-500">To get started, please tell us how you plan to use BookEase.</p>
        </div>

        <form action="{{ route('google.role-selection') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 gap-4">
                <label class="relative cursor-pointer group">
                    <input type="radio" name="role" value="customer" class="peer sr-only" required checked>
                    <div class="p-6 bg-[#F8FAFC] border-2 border-transparent rounded-2xl peer-checked:border-[#10B981] peer-checked:bg-white transition-all group-hover:bg-gray-50 shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-[#1E293B]">I'm a Customer</h3>
                                <p class="text-xs text-gray-400">I want to book services and appointments.</p>
                            </div>
                        </div>
                    </div>
                    <div class="absolute top-4 right-4 text-[#10B981] opacity-0 peer-checked:opacity-100">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    </div>
                </label>

                <label class="relative cursor-pointer group">
                    <input type="radio" name="role" value="provider" class="peer sr-only">
                    <div class="p-6 bg-[#F8FAFC] border-2 border-transparent rounded-2xl peer-checked:border-[#10B981] peer-checked:bg-white transition-all group-hover:bg-gray-50 shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-500 flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-[#1E293B]">I'm a Professional</h3>
                                <p class="text-xs text-gray-400">I want to offer services and manage bookings.</p>
                            </div>
                        </div>
                    </div>
                    <div class="absolute top-4 right-4 text-[#10B981] opacity-0 peer-checked:opacity-100">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    </div>
                </label>
            </div>

            <button type="submit" 
                class="w-full bg-[#10B981] text-white font-bold py-4 rounded-2xl hover:bg-[#059669] shadow-lg shadow-green-100 transition-all active:scale-[0.98]">
                Proceed to Dashboard
            </button>
        </form>
    </div>
</div>
@endsection
