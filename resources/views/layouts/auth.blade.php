<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @php
        $side = $authSide ?? 'left';
        $pageTitle = (isset($title) ? $title : 'Authentication') . ' - Shah Abdul Latif University';
        $logoUrl = asset('images/salu-logo.png');
        $gateUrl = asset('images/salu-gate-clean.jpg');
    @endphp
    
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
<body class="salu-auth-body">
    <!-- MAIN VIEWPORT CONTAINER -->
    <div class="salu-auth-viewport salu-auth-{{ $side }}">
        @if($side === 'right')
            <!-- LEFT: UNIVERSITY CAMPUS BUILDING / GATE PHOTO -->
            <div class="salu-backdrop-gate salu-gate-left" style="background-image: url('{{ $gateUrl }}');"></div>

            <!-- RIGHT: SINDHI AJRAK BLUE BACKDROP WITH S-CURVE -->
            <svg class="salu-curve-backdrop-svg salu-curve-right" viewBox="0 0 1000 1000" preserveAspectRatio="none" aria-hidden="true">
                <defs>
                    <pattern id="saluAjrakBlueTileRight" width="36" height="36" patternUnits="userSpaceOnUse">
                        <rect width="36" height="36" fill="#07103a" />
                        <path d="M18 0 L36 18 L18 36 L0 18 Z" fill="#0d1e5e" stroke="#162e84" stroke-width="0.7" />
                        <path d="M18 4 L32 18 L18 32 L4 18 Z" fill="#081545" stroke="#1f3da8" stroke-width="0.5" />
                        <path d="M18 10 L21 18 L18 26 L15 18 Z" fill="#1b399e" />
                        <path d="M10 18 L18 21 L26 18 L18 15 Z" fill="#1b399e" />
                        <circle cx="18" cy="18" r="1.8" fill="#f59e0b" opacity="0.8" />
                        <circle cx="0" cy="0" r="1.2" fill="#2d52cc" opacity="0.6" />
                        <circle cx="36" cy="0" r="1.2" fill="#2d52cc" opacity="0.6" />
                        <circle cx="0" cy="36" r="1.2" fill="#2d52cc" opacity="0.6" />
                        <circle cx="36" cy="36" r="1.2" fill="#2d52cc" opacity="0.6" />
                    </pattern>
                    <filter id="ajrakOrangeGlowRight" x="-30%" y="-30%" width="160%" height="160%">
                        <feDropShadow dx="-3" dy="0" stdDeviation="6" flood-color="#ff7a00" flood-opacity="0.8" />
                    </filter>
                </defs>

                <!-- RIGHT SINDHI AJRAK BLUE SHAPE (FROM CURVE TO RIGHT EDGE) -->
                <path d="M 445,0 L 1000,0 L 1000,1000 L 365,1000 C 405,910 435,810 460,700 C 505,480 465,280 445,0 Z" fill="url(#saluAjrakBlueTileRight)" />

                <!-- ORANGE S-CURVE BORDER STROKE -->
                <path d="M 445,0 C 465,280 505,480 460,700 C 435,810 405,910 365,1000" fill="none" stroke="#ff7a00" stroke-width="4.5" filter="url(#ajrakOrangeGlowRight)" />
            </svg>

            <!-- CONTENT LAYER: FORM ALIGNED ON THE RIGHT IN THE BLUE AJRAK AREA -->
            <main role="main" class="salu-auth-content salu-content-right">
                @yield('content')
            </main>
        @else
            <!-- RIGHT: UNIVERSITY CAMPUS GATE PHOTO -->
            <div class="salu-backdrop-gate salu-gate-right" style="background-image: url('{{ $gateUrl }}');"></div>

            <!-- LEFT: SINDHI AJRAK BLUE BACKDROP WITH S-CURVE -->
            <svg class="salu-curve-backdrop-svg salu-curve-left" viewBox="0 0 1000 1000" preserveAspectRatio="none" aria-hidden="true">
                <defs>
                    <pattern id="saluAjrakBlueTileLeft" width="36" height="36" patternUnits="userSpaceOnUse">
                        <rect width="36" height="36" fill="#07103a" />
                        <path d="M18 0 L36 18 L18 36 L0 18 Z" fill="#0d1e5e" stroke="#162e84" stroke-width="0.7" />
                        <path d="M18 4 L32 18 L18 32 L4 18 Z" fill="#081545" stroke="#1f3da8" stroke-width="0.5" />
                        <path d="M18 10 L21 18 L18 26 L15 18 Z" fill="#1b399e" />
                        <path d="M10 18 L18 21 L26 18 L18 15 Z" fill="#1b399e" />
                        <circle cx="18" cy="18" r="1.8" fill="#f59e0b" opacity="0.8" />
                        <circle cx="0" cy="0" r="1.2" fill="#2d52cc" opacity="0.6" />
                        <circle cx="36" cy="0" r="1.2" fill="#2d52cc" opacity="0.6" />
                        <circle cx="0" cy="36" r="1.2" fill="#2d52cc" opacity="0.6" />
                        <circle cx="36" cy="36" r="1.2" fill="#2d52cc" opacity="0.6" />
                    </pattern>
                    <filter id="ajrakOrangeGlowLeft" x="-30%" y="-30%" width="160%" height="160%">
                        <feDropShadow dx="3" dy="0" stdDeviation="6" flood-color="#ff7a00" flood-opacity="0.8" />
                    </filter>
                </defs>

                <!-- LEFT SINDHI AJRAK BLUE SHAPE -->
                <path d="M 0,0 L 555,0 C 535,280 495,480 540,700 C 565,810 595,910 635,1000 L 0,1000 Z" fill="url(#saluAjrakBlueTileLeft)" />

                <!-- ORANGE S-CURVE BORDER STROKE -->
                <path d="M 555,0 C 535,280 495,480 540,700 C 565,810 595,910 635,1000" fill="none" stroke="#ff7a00" stroke-width="4.5" filter="url(#ajrakOrangeGlowLeft)" />
            </svg>

            <!-- CONTENT LAYER: FORM ALIGNED ON THE LEFT IN THE BLUE AJRAK AREA -->
            <main role="main" class="salu-auth-content salu-content-left">
                @yield('content')
            </main>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
