@extends('layouts.public')

@section('title', 'Welcome - SALU Exam Portal')

@section('content')
<!-- Top Public Nav -->
<nav class="bg-[#0b133d] border-b border-yellow-500/30 text-white py-3 px-4 sm:px-8">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-3 text-decoration-none text-white">
            <img src="{{ asset('images/salu-logo.png') }}" alt="SALU Seal" class="w-10 h-10 object-contain rounded-full bg-white p-0.5 border border-yellow-500/50" />
            <div>
                <div class="font-bold text-sm sm:text-base tracking-tight leading-none">Shah Abdul Latif University</div>
                <div class="text-xs text-yellow-400/80 font-medium">Online Examination Portal</div>
            </div>
        </a>
        <div class="flex items-center gap-3">
            @guest
                <a href="{{ route('login') }}" class="text-xs sm:text-sm font-semibold px-4 py-2 rounded-lg bg-yellow-500 hover:bg-yellow-400 text-gray-900 transition">
                    <i class="fas fa-sign-in-alt me-1"></i> Sign In
                </a>
            @else
                <a href="{{ auth()->user()->role === 'STUDENT' ? route('student.dashboard') : route('admin.dashboard') }}" class="text-xs sm:text-sm font-semibold px-4 py-2 rounded-lg bg-yellow-500 hover:bg-yellow-400 text-gray-900 transition">
                    <i class="fas fa-th-large me-1"></i> My Dashboard
                </a>
            @endguest
        </div>
    </div>
</nav>

<!-- Hero Section -->
<div class="bg-gradient-to-br from-[#700c11] via-[#0b133d] to-[#050b24] text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
        <div class="flex justify-center mb-6">
            <div class="w-28 h-28 md:w-36 md:h-36 bg-white/95 p-2 rounded-full shadow-2xl flex items-center justify-center border-4 border-yellow-500/50">
                <img src="{{ asset('images/salu-logo.png') }}" alt="Shah Abdul Latif University Logo" class="w-full h-full object-contain" />
            </div>
        </div>
        <h1 class="text-3xl md:text-5xl font-extrabold mb-4 tracking-tight">Shah Abdul Latif University, Khairpur</h1>
        <p class="text-lg md:text-xl mb-8 text-yellow-300/90 font-medium max-w-2xl mx-auto">Official Examination & Student Admission Portal</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            @guest
                <a href="{{ route('register') }}" class="btn bg-yellow-500 text-gray-900 font-bold hover:bg-yellow-400 text-base px-8 py-3 rounded-xl shadow-lg transition">
                    <i class="fas fa-user-plus me-1"></i> Register for Admission
                </a>
                <a href="{{ route('login') }}" class="btn border-2 border-white/80 text-white font-bold hover:bg-white hover:text-[#0b133d] text-base px-8 py-3 rounded-xl transition">
                    <i class="fas fa-sign-in-alt me-1"></i> Sign In to Portal
                </a>
            @else
                <a href="{{ auth()->user()->role === 'STUDENT' ? route('student.dashboard') : route('admin.dashboard') }}" 
                   class="btn bg-yellow-500 text-gray-900 font-bold hover:bg-yellow-400 text-base px-8 py-3 rounded-xl shadow-lg transition">
                    <i class="fas fa-arrow-right me-1"></i> Go to Dashboard
                </a>
            @endguest
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h2 class="text-3xl font-bold text-center mb-12">Our Services</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="card text-center hover:shadow-lg transition">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2">Online Enrollment</h3>
            <p class="text-gray-600">Easy and fast online enrollment process for all programs</p>
        </div>

        <div class="card text-center hover:shadow-lg transition">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2">Fee Payment</h3>
            <p class="text-gray-600">Secure online fee payment and challan generation</p>
        </div>

        <div class="card text-center hover:shadow-lg transition">
            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2">Results & Admit Cards</h3>
            <p class="text-gray-600">Download admit cards and view results online</p>
        </div>
    </div>
</div>

<!-- Stats Section -->
<div class="bg-gray-100 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-4xl font-bold text-primary-600 mb-2">8+</div>
                <div class="text-gray-600">Affiliated Colleges</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-primary-600 mb-2">50+</div>
                <div class="text-gray-600">Degree Programs</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-primary-600 mb-2">5000+</div>
                <div class="text-gray-600">Students Enrolled</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-primary-600 mb-2">24/7</div>
                <div class="text-gray-600">Online Support</div>
            </div>
        </div>
    </div>
</div>

<!-- Announcements Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h2 class="text-3xl font-bold mb-8">Important Announcements</h2>
    
    <div class="space-y-4">
        <div class="card border-l-4 border-primary-600">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-semibold">Enrollment Window Open</h3>
                    <p class="text-gray-600 mt-1">Online enrollment for Academic Year 2024-2025 is now open. Last date: December 31, 2024</p>
                </div>
            </div>
        </div>

        <div class="card border-l-4 border-green-600">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-semibold">New Programs Added</h3>
                    <p class="text-gray-600 mt-1">BS Computer Science and BS Software Engineering programs are now available at affiliated colleges</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
