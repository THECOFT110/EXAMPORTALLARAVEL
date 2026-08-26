<nav class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-600 to-primary-800 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-xl">S</span>
                    </div>
                    <span class="text-xl font-bold text-gray-900">SALU Exam Portal</span>
                </a>
            </div>

            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-primary-600 transition">Home</a>
                <a href="{{ route('programs') }}" class="text-gray-700 hover:text-primary-600 transition">Programs</a>
                <a href="{{ route('colleges.public') }}" class="text-gray-700 hover:text-primary-600 transition">Colleges</a>
                <a href="{{ route('contact') }}" class="text-gray-700 hover:text-primary-600 transition">Contact</a>
            </div>

            <div class="flex items-center space-x-4">
                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-primary-600">
                            <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                                <span class="text-primary-600 font-semibold text-sm">{{ substr(auth()->user()->full_name, 0, 1) }}</span>
                            </div>
                            <span class="hidden md:block">{{ auth()->user()->full_name }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" 
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 border border-gray-200">
                            @if(auth()->user()->role === 'STUDENT')
                                <a href="{{ route('student.dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50">Dashboard</a>
                                <a href="{{ route('student.profile') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50">Profile</a>
                            @else
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50">Admin Dashboard</a>
                            @endif
                            <hr class="my-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-50">Logout</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-primary-600 transition">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
