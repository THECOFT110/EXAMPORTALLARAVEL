<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @php
        $pageTitle = (isset($title) ? $title : 'Dashboard') . ' - SALU Khairpur Exam Portal';
        $logoUrl = asset('images/salu-logo.png');
    @endphp
    
    <meta name="description" content="SALU Khairpur Exam Portal for Shah Abdul Latif University, Khairpur examination enrollment, fee challans, admit cards, and results." />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="SALU Khairpur Exam Portal" />
    <meta property="og:title" content="{{ $pageTitle }}" />
    <meta property="og:description" content="Official exam portal for Shah Abdul Latif University, Khairpur." />
    <meta property="og:image" content="{{ $logoUrl }}" />
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="{{ $pageTitle }}" />
    <meta name="twitter:description" content="Official exam portal for Shah Abdul Latif University, Khairpur." />
    <meta name="twitter:image" content="{{ $logoUrl }}" />
    
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
<body>
    @auth
        <div class="wrapper">
            <!-- Sidebar -->
            <nav id="sidebar" class="sidebar salu-portal-sidebar">
                <div class="sidebar-header">
                    <a href="{{ route('home') }}" class="text-decoration-none d-flex align-items-center gap-3">
                        <div class="salu-sidebar-logo-wrap">
                            <img src="{{ $logoUrl }}" alt="Shah Abdul Latif University logo" class="brand-logo" />
                        </div>
                        <div class="brand-text-block">
                            <span class="brand-title">SALU Khairpur</span>
                            <span class="brand-subtitle">EXAM PORTAL</span>
                        </div>
                    </a>
                </div>

                <div class="sidebar-nav-container">
                    <div class="sidebar-section-label">MAIN NAVIGATION</div>
                    <ul class="list-unstyled components">
                        @if(auth()->user()->role === 'STUDENT')
                            <li>
                                <a href="{{ route('student.dashboard') }}" class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                                    <i class="fas fa-grid-2 me-2"></i> Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('enrollment.create') }}" class="{{ request()->routeIs('enrollment.create') ? 'active' : '' }}">
                                    <i class="fas fa-file-signature me-2"></i> Enrollment Form
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('enrollment.card') }}" class="{{ request()->routeIs('enrollment.card') ? 'active' : '' }}">
                                    <i class="fas fa-id-card-clip me-2"></i> Enrollment Card
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('exams.fee-challan') }}" class="{{ request()->routeIs('exams.fee-challan') ? 'active' : '' }}">
                                    <i class="fas fa-receipt me-2"></i> Fee Challan
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('exams.form') }}" class="{{ request()->routeIs('exams.form') ? 'active' : '' }}">
                                    <i class="fas fa-file-lines me-2"></i> Examination Form
                                    <span class="badge bg-warning text-dark ms-auto" style="font-size:0.62rem; padding: 2px 6px;">Soon</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('exams.admit-card') }}" class="{{ request()->routeIs('exams.admit-card') ? 'active' : '' }}">
                                    <i class="fas fa-id-card me-2"></i> Admit Card
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('exams.results') }}" class="{{ request()->routeIs('exams.results') ? 'active' : '' }}">
                                    <i class="fas fa-award me-2"></i> Results
                                </a>
                            </li>
                        @elseif(in_array(auth()->user()->role, ['ADMIN', 'SUPERADMIN']))
                            <li>
                                <a href="{{ auth()->user()->role === 'SUPERADMIN' ? route('admin.superadmin-dashboard') : route('admin.dashboard') }}" 
                                   class="{{ request()->routeIs('admin.dashboard') || request()->routeIs('admin.superadmin-dashboard') ? 'active' : '' }}">
                                    <i class="fas fa-chart-pie me-2"></i> Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.enrollments.index') }}" class="{{ request()->routeIs('admin.enrollments.*') ? 'active' : '' }}">
                                    <i class="fas fa-file-lines me-2"></i> Enrollment Forms
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.fees.verification') }}" class="{{ request()->routeIs('admin.fees.verification') ? 'active' : '' }}">
                                    <i class="fas fa-circle-check me-2 text-success"></i> Fee Verifications
                                </a>
                            </li>
                            @if(auth()->user()->role === 'SUPERADMIN')
                                <li>
                                    <a href="{{ route('admin.exams.index') }}" class="{{ request()->routeIs('admin.exams.*') ? 'active' : '' }}">
                                        <i class="fas fa-file-signature me-2"></i> Examination Forms
                                    </a>
                                </li>
                            @endif
                            <li>
                                <a href="{{ route('admin.students.index') }}" class="{{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                                    <i class="fas fa-user-group me-2"></i> Students
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.fees.index') }}" class="{{ request()->routeIs('admin.fees.*') ? 'active' : '' }}">
                                    <i class="fas fa-money-bill-wave me-2"></i> Fee Management
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.seats.index') }}" class="{{ request()->routeIs('admin.seats.*') ? 'active' : '' }}">
                                    <i class="fas fa-chair me-2"></i> Seat Allocation
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.results.index') }}" class="{{ request()->routeIs('admin.results.*') ? 'active' : '' }}">
                                    <i class="fas fa-graduation-cap me-2"></i> Results
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                                    <i class="fas fa-chart-line me-2"></i> Reports
                                </a>
                            </li>
                            @if(auth()->user()->role === 'SUPERADMIN')
                                <li>
                                    <a href="{{ route('admin.colleges.index') }}" class="{{ request()->routeIs('admin.colleges.*') ? 'active' : '' }}">
                                        <i class="fas fa-building-columns me-2"></i> College Accounts
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                        <i class="fas fa-user-shield me-2"></i> Users
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                                        <i class="fas fa-sliders me-2"></i> Settings
                                    </a>
                                </li>
                            @endif
                        @elseif(auth()->user()->role === 'COLLEGE_ADMIN')
                            <li>
                                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                    <i class="fas fa-chart-pie me-2"></i> College Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.enrollments.index') }}" class="{{ request()->routeIs('admin.enrollments.*') ? 'active' : '' }}">
                                    <i class="fas fa-file-lines me-2"></i> Enrollment Applications
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.fees.verification') }}" class="{{ request()->routeIs('admin.fees.verification') ? 'active' : '' }}">
                                    <i class="fas fa-circle-check me-2 text-success"></i> Fee Verifications
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.exams.index') }}" class="{{ request()->routeIs('admin.exams.*') ? 'active' : '' }}">
                                    <i class="fas fa-file-signature me-2"></i> Examination Forms
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.students.index') }}" class="{{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                                    <i class="fas fa-user-group me-2"></i> Students
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                                    <i class="fas fa-chart-line me-2"></i> Reports
                                </a>
                            </li>
                        @endif
                    </ul>

                    <div class="sidebar-section-label mt-3">ACCOUNT</div>
                    <ul class="list-unstyled components">
                        <li>
                            <a href="{{ route('profile') }}" class="{{ request()->routeIs('profile') ? 'active' : '' }}">
                                <i class="fas fa-user-gear me-2"></i> Profile &amp; Security
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- SIDEBAR USER PROFILE FOOTER -->
                <div class="sidebar-user-footer">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                            <div class="salu-avatar-circle">
                                {{ strtoupper(substr(auth()->user()->full_name ?? 'U', 0, 2)) }}
                            </div>
                            <div class="salu-user-meta overflow-hidden">
                                <span class="salu-user-name text-truncate d-block">{{ auth()->user()->full_name }}</span>
                                <span class="salu-user-role-chip">{{ auth()->user()->role }}</span>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                            @csrf
                            <button type="submit" class="salu-logout-btn" title="Sign Out">
                                <i class="fas fa-arrow-right-from-bracket"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <div id="content" class="salu-page-content">
                <!-- TOP HERO NAVBAR WITH SOFT GRADIENT -->
                <nav class="navbar navbar-expand-lg salu-top-navbar">
                    <div class="container-fluid px-3 px-md-4">
                        <button type="button" id="sidebarCollapse" class="salu-nav-toggle-btn" aria-label="Toggle Sidebar">
                            <i class="fas fa-bars-staggered"></i>
                        </button>

                        <div class="salu-page-breadcrumb ms-2 ms-md-3">
                            <h1 class="salu-page-title h6 mb-0">{{ $title ?? 'Dashboard' }}</h1>
                            <span class="salu-session-chip d-none d-sm-inline-block">
                                <i class="fas fa-calendar-alt me-1 text-warning"></i> Session 2025-2026
                            </span>
                        </div>

                        <div class="ms-auto d-flex align-items-center gap-3">
                            <div class="salu-header-user-pill d-none d-md-flex align-items-center gap-2">
                                <div class="salu-header-avatar">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <div>
                                    <span class="d-block fw-bold text-dark lh-1" style="font-size:0.82rem;">{{ auth()->user()->full_name }}</span>
                                    <span class="text-muted small" style="font-size:0.7rem;">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                            <a href="{{ route('profile') }}" class="salu-icon-btn" title="Profile Settings">
                                <i class="fas fa-cog"></i>
                            </a>
                        </div>
                    </div>
                </nav>

                <div class="container-fluid px-3 px-md-4 py-4">
                    @if(session('success'))
                        <div class="alert alert-success border-0 shadow-sm rounded-4 d-flex align-items-center p-3 mb-4" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);" role="alert">
                            <i class="fas fa-circle-check fa-lg me-2 text-success"></i>
                            <div class="flex-grow-1 text-success-emphasis fw-semibold">{{ session('success') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger border-0 shadow-sm rounded-4 d-flex align-items-center p-3 mb-4" style="background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);" role="alert">
                            <i class="fas fa-triangle-exclamation fa-lg me-2 text-danger"></i>
                            <div class="flex-grow-1 text-danger-emphasis fw-semibold">{{ session('error') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>

                <footer class="footer mt-auto py-3 salu-portal-footer">
                    <div class="container-fluid px-4 text-center">
                        <span class="text-muted small">
                            &copy; {{ date('Y') }} <strong>Shah Abdul Latif University, Khairpur</strong>. Examination Management &amp; Student Portal.
                        </span>
                    </div>
                </footer>
            </div>
        </div>
    @else
        <div class="container">
            <main role="main" class="pb-3">
                @yield('content')
            </main>
        </div>
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @auth
        <script>
            document.getElementById('sidebarCollapse')?.addEventListener('click', function () {
                document.getElementById('sidebar')?.classList.toggle('active');
            });

            document.addEventListener('DOMContentLoaded', function () {
                var currentPath = window.location.pathname.toLowerCase();
                var navLinks = document.querySelectorAll('#sidebar .components a');
                navLinks.forEach(function (link) {
                    var href = link.getAttribute('href')?.toLowerCase();
                    if (href && (currentPath === href || (href !== '/' && href !== '/admin/dashboard' && href !== '/admin/superadmin-dashboard' && currentPath.startsWith(href)))) {
                        link.classList.add('active');
                        link.parentElement?.classList.add('active');
                    } else if (href && currentPath === href) {
                        link.classList.add('active');
                        link.parentElement?.classList.add('active');
                    }
                });
            });
        </script>
    @endauth

    <!-- SALU GLOBAL TOAST CONTAINER -->
    <div id="salu-toast-container" class="salu-toast-container" aria-live="polite" aria-atomic="true"></div>

    <script>
        function showToast(message, type = 'error', title = null) {
            let container = document.getElementById('salu-toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'salu-toast-container';
                container.className = 'salu-toast-container';
                document.body.appendChild(container);
            }
            const toast = document.createElement('div');
            toast.className = `salu-toast salu-toast-${type}`;
            const icon = type === 'success' ? 'fa-check-circle' : type === 'warning' ? 'fa-exclamation-triangle' : 'fa-circle-xmark';
            const defaultTitle = type === 'success' ? 'Success' : type === 'warning' ? 'Notice' : 'Action Required';
            
            toast.innerHTML = `
                <div class="salu-toast-icon-wrap">
                    <i class="fas ${icon}"></i>
                </div>
                <div class="salu-toast-content">
                    <strong class="salu-toast-title">${title || defaultTitle}</strong>
                    <div class="salu-toast-msg">${message}</div>
                </div>
                <button type="button" class="salu-toast-close" onclick="this.closest('.salu-toast').remove()">&times;</button>
            `;
            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('salu-toast-fadeout');
                setTimeout(() => { toast.remove(); }, 300);
            }, 4500);
        }

        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                showToast(@json(session('success')), 'success', 'Success');
            @endif
            @if(session('error'))
                showToast(@json(session('error')), 'error', 'Error');
            @endif
        });
    </script>

    @auth
    <!-- ══ STRICT 15-MINUTE INACTIVITY LOGOUT ══ -->
    <div id="inactivityModal" style="display:none; position:fixed; inset:0; z-index:99999; background:rgba(0,0,0,.65); backdrop-filter:blur(6px); align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:16px; padding:36px 32px; max-width:420px; width:90%; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,.3); animation:slideDown .3s ease-out;">
            <div style="width:70px;height:70px;border-radius:50%;background:linear-gradient(135deg,#fef3c7,#fbbf24);display:flex;align-items:center;justify-content:center;margin:0 auto 18px;font-size:2rem;">
                <i class="fas fa-hourglass-half" style="color:#92400e;"></i>
            </div>
            <h4 style="font-weight:800;color:#1a2540;margin-bottom:8px;">Session Expiring Soon</h4>
            <p style="color:#6b7280;font-size:.92rem;line-height:1.6;">
                You will be logged out in <strong id="inactivityCountdown" style="color:#dc2626;font-size:1.1rem;">120</strong> seconds due to inactivity.
            </p>
            <p style="color:#9ca3af;font-size:.82rem;">Move your mouse or press any key to stay logged in.</p>
            <button type="button" onclick="resetInactivityTimer()" style="margin-top:12px;padding:10px 32px;background:linear-gradient(135deg,#1e3a9e,#2541c8);color:#fff;border:none;border-radius:10px;font-weight:700;font-size:.95rem;cursor:pointer;box-shadow:0 4px 14px rgba(30,58,158,.3);">
                <i class="fas fa-hand-pointer me-2"></i>I'm Still Here
            </button>
        </div>
    </div>

    <script>
    (function() {
        const INACTIVITY_LIMIT_MS = 15 * 60 * 1000;   // 15 minutes
        const WARNING_AT_MS       = 13 * 60 * 1000;   // Show warning at 13 minutes
        const LOGOUT_URL          = '{{ route("logout") }}';
        let lastActivity = Date.now();
        let warningShown = false;
        let countdownInterval = null;

        // Track ALL user activity
        const activityEvents = ['mousemove', 'mousedown', 'keydown', 'keypress', 'scroll', 'touchstart', 'touchmove', 'click', 'wheel'];

        function onActivity() {
            lastActivity = Date.now();
            if (warningShown) {
                hideWarning();
            }
        }

        activityEvents.forEach(evt => {
            document.addEventListener(evt, onActivity, { passive: true });
        });

        function showWarning() {
            warningShown = true;
            const modal = document.getElementById('inactivityModal');
            if (modal) modal.style.display = 'flex';

            // Start countdown
            if (countdownInterval) clearInterval(countdownInterval);
            countdownInterval = setInterval(() => {
                const remaining = Math.max(0, Math.ceil((INACTIVITY_LIMIT_MS - (Date.now() - lastActivity)) / 1000));
                const el = document.getElementById('inactivityCountdown');
                if (el) el.textContent = remaining;
                if (remaining <= 0) {
                    clearInterval(countdownInterval);
                    forceLogout();
                }
            }, 1000);
        }

        function hideWarning() {
            warningShown = false;
            const modal = document.getElementById('inactivityModal');
            if (modal) modal.style.display = 'none';
            if (countdownInterval) { clearInterval(countdownInterval); countdownInterval = null; }
        }

        function forceLogout() {
            // Clear any drafts
            try { localStorage.removeItem('salu_enroll_draft_v2'); } catch(e) {}
            
            // Create a form and submit it
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = LOGOUT_URL;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
            document.body.appendChild(form);
            form.submit();
        }

        // Check every 30 seconds
        setInterval(() => {
            const idle = Date.now() - lastActivity;
            if (idle >= INACTIVITY_LIMIT_MS) {
                forceLogout();
            } else if (idle >= WARNING_AT_MS && !warningShown) {
                showWarning();
            }
        }, 30000);

        // Also check on visibility change (user switches tabs)
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                const idle = Date.now() - lastActivity;
                if (idle >= INACTIVITY_LIMIT_MS) {
                    forceLogout();
                } else if (idle >= WARNING_AT_MS && !warningShown) {
                    showWarning();
                }
            }
        });

        // Expose for the "I'm Still Here" button
        window.resetInactivityTimer = function() {
            lastActivity = Date.now();
            hideWarning();
            // Ping the server to reset the session
            fetch('{{ route("login") }}', { method: 'HEAD', credentials: 'same-origin' }).catch(() => {});
        };
    })();
    </script>
    @endauth

    @stack('scripts')
</body>
</html>
