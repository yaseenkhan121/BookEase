<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookEase | Appointment Booking System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .step-arrow::after { content: '→'; position: absolute; right: -20px; top: 50%; transform: translateY(-50%); color: #cbd5e1; font-size: 1.5rem; }
        @media (max-width: 768px) { .step-arrow::after { display: none; } }
    </style>
</head>
<body class="bg-[#F8FAFC] text-[#1E293B]">

    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center gap-2">
                    <div class="bg-[#10B981] p-2 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <span class="text-2xl font-bold text-[#064E3B]">BookEase</span>
                </div>
                <div class="hidden md:flex items-center space-x-8 font-medium text-gray-500">
                    <a href="#" class="hover:text-[#10B981]">Home</a>
                    <a href="#features" class="hover:text-[#10B981]">Features</a>
                    <a href="#" class="hover:text-[#10B981]">Services</a>
                    <a href="#" class="hover:text-[#10B981]">Pricing</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-gray-600 font-semibold hover:text-gray-900">Sign In</a>
                    <a href="{{ route('register') }}" class="bg-[#248277] text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-[#1a635b] transition shadow-md">Sign Up</a>
                </div>
            </div>
        </div>
    </nav>

    <header class="relative pt-16 pb-24 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-12">
                <div class="flex-1 text-left">
                    <h1 class="text-5xl md:text-6xl font-extrabold leading-tight text-[#1E293B] mb-6">
                        Book Appointments <br>
                        <span class="text-[#10B981]">Easily</span>
                    </h1>
                    <p class="text-lg text-gray-500 mb-10 max-w-lg leading-relaxed">
                        Streamline your scheduling with our intelligent appointment booking platform. Connect with top professionals and manage your time effortlessly.
                    </p>
                    <div class="flex flex-wrap gap-4 mb-12">
                        <a href="{{ route('register') }}" class="bg-[#248277] text-white px-8 py-4 rounded-xl font-bold shadow-lg shadow-green-100 hover:scale-105 transition">Get Started</a>
                        <a href="{{ route('register', ['role' => 'provider']) }}" class="bg-white text-[#475569] border border-gray-200 px-8 py-4 rounded-xl font-bold hover:bg-gray-50 transition">Become a Provider</a>
                    </div>
                    <div class="flex items-center gap-8">
                        <div class="flex -space-x-3">
                            <div class="w-10 h-10 rounded-full bg-[#10B981] border-2 border-white flex items-center justify-center text-xs text-white">A</div>
                            <div class="w-10 h-10 rounded-full bg-[#059669] border-2 border-white flex items-center justify-center text-xs text-white">B</div>
                            <div class="w-10 h-10 rounded-full bg-[#047857] border-2 border-white flex items-center justify-center text-xs text-white">C</div>
                            <div class="w-10 h-10 rounded-full bg-[#065F46] border-2 border-white flex items-center justify-center text-xs text-white">D</div>
                        </div>
                        <div>
                            <p class="font-bold text-[#1E293B]">10,000+ Users</p>
                            <p class="text-xs text-gray-400">Trust our platform</p>
                        </div>
                        <div class="border-l border-gray-200 pl-8">
                            <p class="font-bold text-[#1E293B]">4.9/5 Rating</p>
                            <p class="text-yellow-400">★★★★★</p>
                        </div>
                    </div>
                </div>

                <div class="flex-1 relative">
                    <div class="bg-white p-8 rounded-[40px] shadow-2xl border border-gray-100 relative">
                        <div class="aspect-[4/3] bg-[#EFFFFB] border-2 border-[#10B981] rounded-3xl flex items-center justify-center">
                            <span class="text-[#248277] font-bold text-xl">Calendar Booking Illustration</span>
                        </div>
                        <div class="absolute -bottom-6 -right-6 bg-white p-4 rounded-2xl shadow-xl border border-gray-50 flex items-center gap-4 animate-bounce">
                            <div class="bg-[#10B981] p-2 rounded-full text-white">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold">Appointment Confirmed</p>
                                <p class="text-xs text-gray-400">Today at 2:00 PM</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section id="features" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <span class="bg-[#EFFFFB] text-[#10B981] px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest">Features</span>
            <h2 class="text-4xl font-extrabold text-[#1E293B] mt-4 mb-6">Everything You Need</h2>
            <p class="text-gray-500 max-w-2xl mx-auto mb-16">Powerful features designed to make appointment booking seamless and efficient for everyone.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="p-8 bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl transition-all text-left">
                    <div class="bg-[#10B981] w-12 h-12 rounded-xl flex items-center justify-center text-white mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Easy Booking</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Book appointments in just a few clicks. Simple, fast, and intuitive interface.</p>
                </div>
                <div class="p-8 bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl transition-all text-left">
                    <div class="bg-[#10B981] w-12 h-12 rounded-xl flex items-center justify-center text-white mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Real-time Availability</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">See live availability and book slots that work for your schedule instantly.</p>
                </div>
                <div class="p-8 bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl transition-all text-left">
                    <div class="bg-[#10B981] w-12 h-12 rounded-xl flex items-center justify-center text-white mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Instant Notifications</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Get instant confirmations and reminders via email and SMS notifications.</p>
                </div>
                <div class="p-8 bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl transition-all text-left">
                    <div class="bg-[#10B981] w-12 h-12 rounded-xl flex items-center justify-center text-white mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Secure Scheduling</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Your data is protected with enterprise-grade security and encryption.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 bg-[#EFFFFB]/30">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <span class="bg-white text-[#10B981] border border-[#10B981] px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest">How It Works</span>
            <h2 class="text-4xl font-extrabold text-[#1E293B] mt-4 mb-6">Simple 3-Step Process</h2>
            <p class="text-gray-500 max-w-2xl mx-auto mb-16">Getting started is easy. Follow these simple steps to book your appointment.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 relative">
                <div class="relative step-arrow">
                    <div class="bg-white p-10 rounded-3xl shadow-sm relative z-10">
                        <div class="absolute -top-4 -left-4 bg-[#10B981] text-white w-10 h-10 rounded-full flex items-center justify-center font-bold">1</div>
                        <div class="bg-[#EFFFFB] w-16 h-16 rounded-2xl flex items-center justify-center mb-6 mx-auto">
                            <svg class="w-8 h-8 text-[#1E293B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-extrabold mb-4 text-[#1E293B]">Choose Provider</h3>
                        <p class="text-gray-500 text-sm">Browse through our verified professionals and select the one that best fits your needs.</p>
                    </div>
                </div>
                <div class="relative step-arrow">
                    <div class="bg-white p-10 rounded-3xl shadow-sm relative z-10">
                        <div class="absolute -top-4 -left-4 bg-[#10B981] text-white w-10 h-10 rounded-full flex items-center justify-center font-bold">2</div>
                        <div class="bg-[#EFFFFB] w-16 h-16 rounded-2xl flex items-center justify-center mb-6 mx-auto">
                            <svg class="w-8 h-8 text-[#1E293B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-extrabold mb-4 text-[#1E293B]">Select Time Slot</h3>
                        <p class="text-gray-500 text-sm">Pick a convenient time from available slots that match your schedule perfectly.</p>
                    </div>
                </div>
                <div class="relative">
                    <div class="bg-white p-10 rounded-3xl shadow-sm relative z-10">
                        <div class="absolute -top-4 -left-4 bg-[#10B981] text-white w-10 h-10 rounded-full flex items-center justify-center font-bold">3</div>
                        <div class="bg-[#EFFFFB] w-16 h-16 rounded-2xl flex items-center justify-center mb-6 mx-auto">
                            <svg class="w-8 h-8 text-[#1E293B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-extrabold mb-4 text-[#1E293B]">Confirm Booking</h3>
                        <p class="text-gray-500 text-sm">Review your details and confirm. You'll receive instant confirmation and reminders.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-white py-12 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 text-center text-gray-400 text-sm">
            <p>&copy; 2026 BookEase. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>