<footer class="bg-gray-900 text-gray-300 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-white font-bold text-lg mb-4">SALU Exam Portal</h3>
                <p class="text-sm">Shah Abdul Latif University Examination Management System</p>
            </div>

            <div>
                <h4 class="text-white font-semibold mb-4">Quick Links</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-white transition">Home</a></li>
                    <li><a href="{{ route('programs') }}" class="hover:text-white transition">Programs</a></li>
                    <li><a href="{{ route('colleges.public') }}" class="hover:text-white transition">Colleges</a></li>
                    <li><a href="{{ route('faq') }}" class="hover:text-white transition">FAQ</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-semibold mb-4">Student Portal</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('login') }}" class="hover:text-white transition">Login</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-white transition">Register</a></li>
                    <li><a href="{{ route('password.request') }}" class="hover:text-white transition">Forgot Password</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-semibold mb-4">Contact Us</h4>
                <ul class="space-y-2 text-sm">
                    <li>Shah Abdul Latif University</li>
                    <li>Khairpur, Sindh, Pakistan</li>
                    <li>Phone: 022-2771331</li>
                    <li>Email: info@saluexamportal.edu.pk</li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-800 mt-8 pt-8 text-sm text-center">
            <p>&copy; {{ date('Y') }} SALU Exam Portal. All rights reserved.</p>
        </div>
    </div>
</footer>
