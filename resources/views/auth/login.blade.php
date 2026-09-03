@php
    $authSide = 'left';
    $title = 'Portal Login';
    $logoUrl = asset('images/salu-logo.png');
@endphp

@extends('layouts.auth')

@section('title', 'Portal Login - Shah Abdul Latif University')

@section('content')
<div class="salu-card-wrapper">
    <div class="salu-login-card">
        <!-- PROTRUDING SALU CREST LOGO -->
        <div class="salu-crest-wrap">
            <img src="{{ $logoUrl }}" alt="Shah Abdul Latif University Logo" class="salu-crest-img" />
        </div>

        <!-- ROLE TAB BAR -->
        <div class="salu-role-bar">
            <button type="button" class="salu-role-tab active" id="tabStudent" onclick="switchLoginRole('STUDENT')">STUDENT</button>
            <button type="button" class="salu-role-tab" id="tabEmployee" onclick="switchLoginRole('EMPLOYEE')">EMPLOYEE</button>
        </div>

        <!-- CARD HEADER -->
        <h2 class="salu-card-title" id="loginGreeting">Welcome Student!</h2>
        <p class="salu-card-subtitle" id="loginSubtitle">Kindly Login to Continue</p>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 py-1 px-2 mb-2 small" role="alert">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-3 py-1 px-2 mb-2 small" role="alert">
                <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
            </div>
        @endif

        <form method="post" action="{{ route('login') }}" id="signInForm" novalidate>
            @csrf
            
            @if(isset($errors) && $errors->any())
                <div class="alert alert-danger border-0 shadow-sm rounded-3 py-1 px-2 mb-2 small" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- CNIC / EMAIL FIELD -->
            <div class="salu-field-group">
                <label for="cnicLoginInput" class="salu-label">CNIC<span class="text-danger">*</span></label>
                <div class="salu-input-box">
                    <i class="fas fa-id-card salu-input-icon"></i>
                    <input name="email" id="cnicLoginInput" class="salu-input" placeholder="Enter CNIC" autocomplete="username" value="{{ old('email') }}" required autofocus />
                </div>
                @error('email')<span class="salu-field-err">{{ $message }}</span>@enderror
            </div>

            <!-- PASSWORD FIELD -->
            <div class="salu-field-group">
                <label for="pwdLoginInput" class="salu-label">Password<span class="text-danger">*</span></label>
                <div class="salu-input-box">
                    <i class="fas fa-lock salu-input-icon"></i>
                    <input name="password" id="pwdLoginInput" class="salu-input" type="password" placeholder="Enter Password" autocomplete="current-password" required />
                    <button type="button" class="salu-eye-btn" onclick="togglePwdVisibility('pwdLoginInput', this)" tabindex="-1" aria-label="Toggle password visibility">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password')<span class="salu-field-err">{{ $message }}</span>@enderror
                
                <div class="text-end mt-1">
                    <a href="{{ route('password.request') }}" class="salu-forgot-link small">Forgot Password</a>
                </div>
            </div>

            <!-- LOGIN BUTTON -->
            <div class="text-center mt-3 mb-2">
                <button type="submit" class="salu-btn-orange">
                    Login
                </button>
            </div>
        </form>



        <!-- CARD FOOTER -->
        <div class="salu-card-footer mt-2 pt-1">
            <p class="mb-1 text-muted small" style="font-size: 0.75rem;">Copyright &copy; {{ date('Y') }} SALU.</p>
            <a href="{{ route('register') }}" class="salu-register-link" style="font-size: 0.78rem;">
                Register as an Academic Student
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function switchLoginRole(role) {
        const tabStudent = document.getElementById('tabStudent');
        const tabEmployee = document.getElementById('tabEmployee');
        const greeting = document.getElementById('loginGreeting');
        const cnicInput = document.getElementById('cnicLoginInput');

        if (role === 'STUDENT') {
            tabStudent.classList.add('active');
            tabEmployee.classList.remove('active');
            if (greeting) greeting.textContent = 'Welcome Student!';
            if (cnicInput) cnicInput.placeholder = 'Enter CNIC';
        } else {
            tabEmployee.classList.add('active');
            tabStudent.classList.remove('active');
            if (greeting) greeting.textContent = 'Welcome Employee!';
            if (cnicInput) cnicInput.placeholder = 'Enter Employee Email / CNIC';
        }
    }

    function togglePwdVisibility(inputId, btn) {
        const inp = document.getElementById(inputId);
        if (!inp) return;
        const icon = btn.querySelector('i');
        if (inp.type === 'password') {
            inp.type = 'text';
            if (icon) icon.className = 'fas fa-eye-slash';
        } else {
            inp.type = 'password';
            if (icon) icon.className = 'fas fa-eye';
        }
    }

    // Auto-hyphenation for CNIC
    const cnicInp = document.getElementById('cnicLoginInput');
    if (cnicInp) {
        cnicInp.addEventListener('input', function() {
            let val = this.value.trim();
            if (!val.includes('@') && /^\d+$/.test(val.replace(/-/g, ''))) {
                let digits = val.replace(/\D/g, '').substring(0, 13);
                let formatted = '';
                if (digits.length > 0) {
                    formatted = digits.substring(0, 5);
                    if (digits.length > 5) formatted += '-' + digits.substring(5, 12);
                    if (digits.length > 12) formatted += '-' + digits.substring(12, 13);
                }
                this.value = formatted;
            }
        });
    }
</script>
@endpush
