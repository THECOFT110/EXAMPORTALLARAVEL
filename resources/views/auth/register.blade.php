@php
    $authSide = 'right';
    $title = 'Student Registration';
    $logoUrl = asset('images/salu-logo.png');
@endphp

@extends('layouts.auth')

@section('title', 'Student Registration - Shah Abdul Latif University')

@section('content')
<div class="salu-card-wrapper salu-card-wrapper-right">
    <div class="salu-login-card salu-signup-card">
        <!-- PROTRUDING SALU CREST LOGO -->
        <div class="salu-crest-wrap">
            <img src="{{ $logoUrl }}" alt="Shah Abdul Latif University Logo" class="salu-crest-img" />
        </div>

        <!-- ROLE TAB BAR -->
        <div class="salu-role-bar">
            <button type="button" class="salu-role-tab active">STUDENT</button>
            <button type="button" class="salu-role-tab">REGISTRATION</button>
        </div>

        <!-- CARD HEADER -->
        <h2 class="salu-card-title">Welcome Student!</h2>
        <p class="salu-card-subtitle">Kindly Register to Create Your Portal Account</p>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 py-2 px-3 mb-3 small" role="alert">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-3 py-2 px-3 mb-3 small" role="alert">
                <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
            </div>
        @endif

        <form method="post" action="{{ route('register') }}" id="signUpForm" novalidate>
            @csrf
            
            @if(isset($errors) && $errors->any())
                <div class="alert alert-danger border-0 shadow-sm rounded-3 py-2 px-3 mb-3 small" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- ROW 1: FULL NAME & FATHER NAME -->
            <div class="row g-3 mb-2">
                <div class="col-md-6">
                    <div class="salu-field-group">
                        <label for="fullNameInput" class="salu-label">Full Name<span class="text-danger">*</span></label>
                        <div class="salu-input-box">
                            <i class="fas fa-user salu-input-icon"></i>
                            <input name="full_name" id="fullNameInput" class="salu-input" placeholder="Enter Full Name" value="{{ old('full_name') }}" required />
                        </div>
                        @error('full_name')<span class="salu-field-err">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="salu-field-group">
                        <label for="fatherNameInput" class="salu-label">Father's Name<span class="text-danger">*</span></label>
                        <div class="salu-input-box">
                            <i class="fas fa-user-tie salu-input-icon"></i>
                            <input name="father_name" id="fatherNameInput" class="salu-input" placeholder="Enter Father's Name" value="{{ old('father_name') }}" required />
                        </div>
                        @error('father_name')<span class="salu-field-err">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            <!-- ROW 2: CNIC & PHONE -->
            <div class="row g-3 mb-2">
                <div class="col-md-6">
                    <div class="salu-field-group">
                        <label for="cnicSignUpInput" class="salu-label">CNIC<span class="text-danger">*</span></label>
                        <div class="salu-input-box">
                            <i class="fas fa-id-card salu-input-icon"></i>
                            <input name="cnic" id="cnicSignUpInput" class="salu-input" inputmode="numeric" placeholder="00000-0000000-0" maxlength="15" value="{{ old('cnic') }}" required />
                        </div>
                        @error('cnic')<span class="salu-field-err">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="salu-field-group">
                        <label for="phoneSignUpInput" class="salu-label">Mobile Number<span class="text-danger">*</span></label>
                        <div class="salu-input-box">
                            <i class="fas fa-phone salu-input-icon"></i>
                            <input name="phone" id="phoneSignUpInput" class="salu-input" inputmode="numeric" type="tel" placeholder="0300-0000000" maxlength="12" value="{{ old('phone') }}" required />
                        </div>
                        @error('phone')<span class="salu-field-err">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            <!-- ROW 3: EMAIL ADDRESS -->
            <div class="salu-field-group mb-2">
                <label for="emailInput" class="salu-label">Email Address<span class="text-danger">*</span></label>
                <div class="salu-input-box">
                    <i class="fas fa-envelope salu-input-icon"></i>
                    <input name="email" id="emailInput" class="salu-input" type="email" placeholder="name@example.com" value="{{ old('email') }}" required />
                </div>
                @error('email')<span class="salu-field-err">{{ $message }}</span>@enderror
            </div>

            <!-- ROW 4: PASSWORD & CONFIRM -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="salu-field-group">
                        <label for="pwdSignUpInp" class="salu-label">Password<span class="text-danger">*</span></label>
                        <div class="salu-input-box">
                            <i class="fas fa-lock salu-input-icon"></i>
                            <input name="password" id="pwdSignUpInp" class="salu-input" type="password" placeholder="Min 8 characters" autocomplete="new-password" required />
                            <button type="button" class="salu-eye-btn" onclick="togglePwdVisibility('pwdSignUpInp', this)" tabindex="-1">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')<span class="salu-field-err">{{ $message }}</span>@enderror

                        <!-- Password Strength Bar -->
                        <div class="salu-strength-bar mt-2" id="strengthBar">
                            <div class="salu-strength-fill" id="strengthFill"></div>
                        </div>
                        <span class="salu-strength-label" id="strengthLabel"></span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="salu-field-group">
                        <label for="confirmPwdSignUpInp" class="salu-label">Confirm Password<span class="text-danger">*</span></label>
                        <div class="salu-input-box">
                            <i class="fas fa-lock salu-input-icon"></i>
                            <input name="password_confirmation" id="confirmPwdSignUpInp" class="salu-input" type="password" placeholder="Re-enter Password" autocomplete="new-password" required />
                            <button type="button" class="salu-eye-btn" onclick="togglePwdVisibility('confirmPwdSignUpInp', this)" tabindex="-1">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password_confirmation')<span class="salu-field-err">{{ $message }}</span>@enderror

                        <div class="salu-match-label mt-2" id="matchLabel" style="display:none"></div>
                    </div>
                </div>
            </div>

            <!-- SUBMIT BUTTON -->
            <div class="text-center mt-3 mb-2">
                <button type="submit" class="salu-btn-orange">Register</button>
            </div>
        </form>

        <!-- CARD FOOTER -->
        <div class="salu-card-footer">
            <p class="mb-1 text-muted small">Copyright &copy; {{ date('Y') }} SALU.</p>
            <a href="{{ route('login') }}" class="salu-register-link">Already registered? Login here</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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
    const cnicInput = document.getElementById('cnicSignUpInput');
    if (cnicInput) {
        const formatCnic = function () {
            let digits = this.value.replace(/\D/g, '').substring(0, 13);
            let formatted = '';
            if (digits.length > 0) {
                formatted = digits.substring(0, 5);
                if (digits.length > 5) formatted += '-' + digits.substring(5, 12);
                if (digits.length > 12) formatted += '-' + digits.substring(12, 13);
            }
            this.value = formatted;
        };
        cnicInput.addEventListener('input', formatCnic);
        cnicInput.addEventListener('keyup', formatCnic);
    }

    // Auto-hyphenation for Phone
    const phoneInput = document.getElementById('phoneSignUpInput');
    if (phoneInput) {
        const formatPhone = function () {
            let digits = this.value.replace(/\D/g, '').substring(0, 11);
            let formatted = '';
            if (digits.length > 0) {
                formatted = digits.substring(0, 4);
                if (digits.length > 4) formatted += '-' + digits.substring(4, 11);
            }
            this.value = formatted;
        };
        phoneInput.addEventListener('input', formatPhone);
        phoneInput.addEventListener('keyup', formatPhone);
    }

    // Password strength and match checking
    const pwdInp = document.getElementById('pwdSignUpInp');
    const confirmInp = document.getElementById('confirmPwdSignUpInp');
    const fill = document.getElementById('strengthFill');
    const label = document.getElementById('strengthLabel');
    const matchLbl = document.getElementById('matchLabel');

    function updatePwdMeter() {
        if (!pwdInp || !fill || !label) return;
        const v = pwdInp.value;
        if (!v) {
            fill.style.width = '0%';
            label.textContent = '';
            return;
        }
        let score = 0;
        if (v.length >= 8) score++;
        if (/[A-Z]/.test(v)) score++;
        if (/[a-z]/.test(v)) score++;
        if (/\d/.test(v)) score++;
        if (/[@$!%*?&]/.test(v)) score++;

        if (score <= 2) {
            fill.style.width = '33%';
            fill.style.background = '#ef4444';
            label.textContent = 'Weak Password';
            label.style.color = '#f87171';
        } else if (score <= 4) {
            fill.style.width = '66%';
            fill.style.background = '#f59e0b';
            label.textContent = 'Moderate Password';
            label.style.color = '#fbbf24';
        } else {
            fill.style.width = '100%';
            fill.style.background = '#10b981';
            label.textContent = 'Strong Password';
            label.style.color = '#34d399';
        }
    }

    function updateMatchStatus() {
        if (!pwdInp || !confirmInp || !matchLbl) return;
        const p = pwdInp.value;
        const c = confirmInp.value;
        if (!c) {
            matchLbl.style.display = 'none';
            return;
        }
        matchLbl.style.display = 'block';
        if (p === c) {
            matchLbl.textContent = '✔ Passwords match';
            matchLbl.style.color = '#34d399';
        } else {
            matchLbl.textContent = '✖ Passwords do not match';
            matchLbl.style.color = '#f87171';
        }
    }

    if (pwdInp) pwdInp.addEventListener('input', () => { updatePwdMeter(); updateMatchStatus(); });
    if (confirmInp) confirmInp.addEventListener('input', updateMatchStatus);
</script>
@endpush
