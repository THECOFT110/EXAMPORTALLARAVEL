<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @php
        $pageTitle = (isset($title) ? $title : 'Shah Abdul Latif University') . ' - Examination Portal';
        $logoUrl = asset('images/salu-logo.png');
    @endphp
    
    <meta name="description" content="SALU Khairpur Exam Portal for Shah Abdul Latif University, Khairpur examination enrollment, fee challans, admit cards, and results." />
    <title>{{ $pageTitle }}</title>
    
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}" />
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen flex flex-col font-sans">
    <main class="flex-grow">
        @yield('content')
    </main>

    <footer class="bg-[#0b133d] text-gray-300 py-8 border-t border-yellow-500/20 text-center text-sm">
        <div class="max-w-7xl mx-auto px-4">
            <p class="mb-1">&copy; {{ date('Y') }} Shah Abdul Latif University, Khairpur. All rights reserved.</p>
            <p class="text-xs text-gray-400">Office of the Controller of Examinations &bull; Directorate of Admissions</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
