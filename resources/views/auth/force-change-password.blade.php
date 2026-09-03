@php
    $authSide = 'left';
    $title = 'Security Verification — Change Password';
    $logoUrl = asset('images/salu-logo.png');
@endphp

@extends('layouts.auth')

@section('title', 'Change Password Required - Shah Abdul Latif University')

@section('content')
<div class="salu-card-wrapper">
    <div class="salu-login-card" style="max-width: 440px;">
        <!-- PROTRUDING SALU CREST LOGO -->
        <div class="salu-crest-wrap">
            <img src="{{ $logoUrl }}" alt="Shah Abdul Latif University Logo" class="salu-crest-img" />
        </div>

        <div class="salu-role-bar" style="background: rgba(220, 53, 69, 0.1); border-color: rgba(220, 53, 69, 0.2);">
            <span class="text-danger fw-bold small py-1 px-2 d-inline-flex align-items-center">
                <i class="fas fa-shield-alt me-2"></i> Initial Security Setup Required
            </span>
        </div>

        <!-- CARD HEADER -->
        <h2 class="salu-card-title mt-2">Update Password</h2>
        <p class="salu-card-subtitle">You are logged in as <strong>{{ $user->full_name }}</strong> ({{ $user->role }}). Please set a secure personal password to continue.</p>

        @if(session('warning'))
            <div class="alert alert-warning border-0 shadow-sm rounded-3 py-2 px-3 mb-3 small" role="alert">
                <i class="fas fa-exclamation-triangle me-1"></i> {{ session('warning') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 py-2 px-3 mb-3 small" role="alert">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-3 py-2 px-3 mb-3 small" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="post" action="{{ route('password.force_change.update') }}" id="forcePasswordForm" novalidate>
            @csrf

            <!-- CURRENT / TEMPORARY PASSWORD -->
            <div class="salu-field-group">
                <label for="currentPwdInput" class="salu-label">Current / Temporary Password<span class="text-danger">*</span></label>
                <div class="salu-input-box">
                    <i class="fas fa-key salu-input-icon"></i>
                    <input name="current_password" id="currentPwdInput" class="salu-input" type="password" placeholder="Enter current password" autocomplete="current-password" required autofocus />
                    <button type="button" class="salu-eye-btn" onclick="togglePwdVisibility('currentPwdInput', this)" tabindex="-1" aria-label="Toggle password visibility">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('current_password')<span class="salu-field-err">{{ $message }}</span>@enderror
            </div>

            <!-- NEW PASSWORD -->
            <div class="salu-field-group">
                <label for="newPwdInput" class="salu-label">New Password<span class="text-danger">*</span></label>
                <div class="salu-input-box">
                    <i class="fas fa-lock salu-input-icon"></i>
                    <input name="password" id="newPwdInput" class="salu-input" type="password" placeholder="Minimum 8 characters" autocomplete="new-password" required oninput="checkStrength(this.value)" />
                    <button type="button" class="salu-eye-btn" onclick="togglePwdVisibility('newPwdInput', this)" tabindex="-1" aria-label="Toggle password visibility">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password')<span class="salu-field-err">{{ $message }}</span>@enderror

                <!-- Password Strength Meter -->
                <div class="mt-1" id="strengthMeterWrap" style="display:none;">
                    <div class="progress" style="height: 4px; background: #e9ecef; border-radius: 4px;">
                        <div class="progress-bar" id="strengthBar" role="progressbar" style="width: 0%; transition: width 0.3s ease;"></div>
                    </div>
                    <small class="text-muted d-block mt-1" id="strengthLabel" style="font-size: 0.72rem;">Minimum 8 characters with letters and numbers</small>
                </div>
            </div>

            <!-- CONFIRM NEW PASSWORD -->
            <div class="salu-field-group">
                <label for="confirmPwdInput" class="salu-label">Confirm New Password<span class="text-danger">*</span></label>
                <div class="salu-input-box">
                    <i class="fas fa-check-double salu-input-icon"></i>
                    <input name="password_confirmation" id="confirmPwdInput" class="salu-input" type="password" placeholder="Re-enter new password" autocomplete="new-password" required />
                    <button type="button" class="salu-eye-btn" onclick="togglePwdVisibility('confirmPwdInput', this)" tabindex="-1" aria-label="Toggle password visibility">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- SUBMIT BUTTON -->
            <div class="text-center mt-3 mb-2">
                <button type="submit" class="salu-btn-orange">
                    Save New Password & Continue
                </button>
            </div>
        </form>

        <!-- CARD FOOTER & LOGOUT OPTION -->
        <div class="salu-card-footer mt-3 pt-2 border-top">
            <form method="post" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-link text-muted p-0 text-decoration-none small" style="font-size: 0.8rem;">
                    <i class="fas fa-sign-out-alt me-1"></i> Sign out to switch account
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function togglePwdVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function checkStrength(val) {
    const wrap = document.getElementById('strengthMeterWrap');
    const bar = document.getElementById('strengthBar');
    const label = document.getElementById('strengthLabel');
    if (!val) {
        wrap.style.display = 'none';
        return;
    }
    wrap.style.display = 'block';

    let score = 0;
    if (val.length >= 8) score += 25;
    if (/[a-z]/.test(val) && /[A-Z]/.test(val)) score += 25;
    if (/\d/.test(val)) score += 25;
    if (/[^a-zA-Z\d]/.test(val)) score += 25;

    bar.style.width = score + '%';
    if (score < 50) {
        bar.className = 'progress-bar bg-danger';
        label.textContent = 'Weak password (must be at least 8 chars)';
    } else if (score < 75) {
        bar.className = 'progress-bar bg-warning';
        label.textContent = 'Moderate password (add mixed case, numbers, symbols)';
    } else {
        bar.className = 'progress-bar bg-success';
        label.textContent = 'Strong password';
    }
}
</script>
@endsection
