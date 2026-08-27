@extends('layouts.app')

@section('title', 'Student Academic Dashboard')

@php
    $title = 'Student Dashboard';
@endphp

@section('content')
<!-- HERO WELCOME BANNER WITH SOFT SAPPHIRE GRADIENT -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card salu-student-hero-card">
            <div class="card-body p-4 p-lg-5 position-relative" style="z-index: 2;">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
                    <div class="d-flex align-items-center gap-3 gap-md-4">
                        <div class="salu-hero-avatar-wrap">
                            <div class="salu-hero-avatar">
                                <i class="fas fa-user-graduate fa-2x text-warning"></i>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                <h2 class="salu-hero-greeting mb-0">Welcome back, {{ auth()->user()->full_name }}!</h2>
                                <span class="salu-hero-badge"><i class="fas fa-shield-halved me-1"></i> Verified Student</span>
                            </div>
                            <p class="salu-hero-meta mb-0">
                                <span><i class="fas fa-id-card text-warning me-1"></i> CNIC: <strong>{{ auth()->user()->cnic }}</strong></span>
                                <span class="mx-2 opacity-50 d-none d-sm-inline">|</span>
                                <span class="d-block d-sm-inline mt-1 mt-sm-0"><i class="fas fa-envelope text-warning me-1"></i> {{ auth()->user()->email }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <div class="salu-hero-stat-pill">
                            <span class="salu-stat-sub">Campus</span>
                            <span class="salu-stat-val"><i class="fas fa-university text-warning me-1"></i> SALU Khairpur</span>
                        </div>
                        <div class="salu-hero-stat-pill">
                            <span class="salu-stat-sub">Status</span>
                            <span class="salu-stat-val text-success"><i class="fas fa-circle-check me-1"></i> Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FEE STATUS BANNER -->
@if(isset($latestFee) && !in_array($latestFee->status, ['PAID', 'VERIFIED']))
    @if($latestFee->status === 'UNDER_REVIEW')
        <div class="salu-status-banner salu-banner-blue mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="salu-banner-icon bg-primary text-white">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div>
                    <strong class="d-block text-dark fs-6">
                        Payment Under Admin Review: PKR {{ number_format($latestFee->amount, 0) }}
                    </strong>
                    <span class="text-muted small">
                        Challan No: <code>{{ $latestFee->challan_number }}</code> &bull; TID: <strong>{{ $latestFee->transaction_reference ?? 'Submitted' }}</strong> &bull; Method: {{ $latestFee->payment_method ?? 'JazzCash' }}
                    </span>
                </div>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('payment.checkout', $latestFee->id) }}" class="btn salu-btn-pill-blue">
                    <i class="fas fa-eye me-1"></i> Check Status
                </a>
            </div>
        </div>
    @elseif($latestFee->status === 'REJECTED')
        <div class="salu-status-banner salu-banner-red mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="salu-banner-icon bg-danger text-white">
                    <i class="fas fa-circle-xmark"></i>
                </div>
                <div>
                    <strong class="d-block text-danger fs-6">
                        Payment Verification Rejected
                    </strong>
                    <span class="text-muted small">
                        Administration was unable to verify the submitted Transaction ID. Please re-submit the valid transaction reference.
                    </span>
                </div>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('payment.checkout', $latestFee->id) }}" class="btn salu-btn-pill-red">
                    <i class="fas fa-rotate-right me-1"></i> Re-submit TID
                </a>
            </div>
        </div>
    @else
        <div class="salu-status-banner salu-banner-amber mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="salu-banner-icon bg-warning text-dark">
                    <i class="fas fa-qrcode"></i>
                </div>
                <div>
                    <strong class="d-block text-dark fs-6">
                        Pending {{ $latestFee->type === 'EXAMINATION_FEE' ? 'Examination Fee' : 'Enrollment Fee' }}: PKR {{ number_format($latestFee->amount, 0) }}
                    </strong>
                    <span class="text-muted small">
                        Challan No: <code>{{ $latestFee->challan_number }}</code> &bull; Due Date: <strong>{{ $latestFee->due_date->format('d M Y') }}</strong>
                    </span>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3 mt-md-0">
                <a href="{{ route('payment.checkout', $latestFee->id) }}" class="btn salu-btn-pill-green">
                    <i class="fas fa-bolt me-1"></i> Pay Online
                </a>
                <a href="{{ route('enrollment.challan-pdf', $latestFee->id) }}" target="_blank" class="btn salu-btn-pill-outline">
                    <i class="fas fa-print me-1"></i> Bank Challan PDF
                </a>
            </div>
        </div>
    @endif
@endif

<!-- ENROLLMENT WINDOW STATUS -->
@if(!isset($myEnrollment))
    @if($isWindowOpen ?? false)
        <div class="salu-status-banner salu-banner-green mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="salu-banner-icon bg-success text-white">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <strong class="d-block text-success fs-6">Enrollment Window is OPEN</strong>
                    <span class="text-muted small">Admissions for academic session <strong>{{ $currentWindow->academicYear->name ?? 'Current' }}</strong> are active. Complete your form before the deadline.</span>
                </div>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('enrollment.create') }}" class="btn salu-btn-pill-green">
                    <i class="fas fa-pen-to-square me-1"></i> Apply for Enrollment
                </a>
            </div>
        </div>
    @else
        <div class="salu-status-banner salu-banner-red mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="salu-banner-icon bg-danger text-white">
                    <i class="fas fa-lock"></i>
                </div>
                <div>
                    <strong class="d-block text-danger fs-6">Enrollment Window is CLOSED</strong>
                    <span class="text-muted small">Online enrollment submissions are currently closed by university administration. Contact administration for details.</span>
                </div>
            </div>
        </div>
    @endif
@endif

<!-- SECTION TITLE -->
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h4 class="fw-bold text-dark mb-0" style="font-size: 1.15rem; letter-spacing: -0.01em;">
            <i class="fas fa-compass me-2 text-primary"></i>Quick Services &amp; Academic Actions
        </h4>
        <span class="text-muted small">Select an examination service or manage your academic application</span>
    </div>
</div>

<!-- ACTION CARDS WITH SOFT MODERN GRADIENTS -->
<div class="row g-3 mb-4">
    <!-- ENROLLMENT FORM CARD -->
    @if($canCreateEnrollment ?? false)
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <a href="{{ route('enrollment.create') }}" class="text-decoration-none h-100 d-block">
                <div class="card salu-service-card h-100">
                    <div class="card-body p-4 text-center d-flex flex-column justify-content-between">
                        <div>
                            <div class="salu-service-icon-box salu-icon-orange mb-3 mx-auto">
                                <i class="fas fa-file-signature"></i>
                            </div>
                            <h5 class="salu-service-title">Enrollment Form</h5>
                            <p class="salu-service-desc">Submit your official enrollment application</p>
                        </div>
                        <span class="salu-service-link mt-3">Start Form <i class="fas fa-arrow-right ms-1"></i></span>
                    </div>
                </div>
            </a>
        </div>
    @elseif(!isset($myEnrollment))
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <div class="card salu-service-card salu-service-disabled h-100">
                <div class="card-body p-4 text-center d-flex flex-column justify-content-between">
                    <div>
                        <div class="salu-service-icon-box salu-icon-gray mb-3 mx-auto">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h5 class="salu-service-title text-muted">Enrollment Closed</h5>
                        <p class="salu-service-desc">Window is currently closed</p>
                    </div>
                    <span class="badge bg-secondary-subtle text-secondary mt-3 py-2">Unavailable</span>
                </div>
            </div>
        </div>
    @else
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <a href="{{ route('enrollment.details', $myEnrollment->id) }}" class="text-decoration-none h-100 d-block">
                <div class="card salu-service-card h-100">
                    <div class="card-body p-4 text-center d-flex flex-column justify-content-between">
                        <div>
                            <div class="salu-service-icon-box salu-icon-blue mb-3 mx-auto">
                                <i class="fas fa-eye"></i>
                            </div>
                            <h5 class="salu-service-title">View Enrollment</h5>
                            <p class="salu-service-desc">Track review &amp; verification status</p>
                        </div>
                        <span class="salu-service-link mt-3">View Application <i class="fas fa-arrow-right ms-1"></i></span>
                    </div>
                </div>
            </a>
        </div>
    @endif

    <!-- ENROLLMENT CARD -->
    <div class="col-sm-6 col-lg-4 col-xl-3">
        <a href="{{ route('enrollment.card') }}" class="text-decoration-none h-100 d-block">
            <div class="card salu-service-card h-100">
                <div class="card-body p-4 text-center d-flex flex-column justify-content-between">
                    <div>
                        <div class="salu-service-icon-box salu-icon-cyan mb-3 mx-auto">
                            <i class="fas fa-id-card-clip"></i>
                        </div>
                        <h5 class="salu-service-title">Enrollment Card</h5>
                        <p class="salu-service-desc">View &amp; download official registration card</p>
                    </div>
                    <span class="salu-service-link mt-3">Download PDF <i class="fas fa-download ms-1"></i></span>
                </div>
            </div>
        </a>
    </div>

    <!-- FEE CHALLAN -->
    <div class="col-sm-6 col-lg-4 col-xl-3">
        <a href="{{ route('exams.fee-challan') }}" class="text-decoration-none h-100 d-block">
            <div class="card salu-service-card h-100">
                <div class="card-body p-4 text-center d-flex flex-column justify-content-between">
                    <div>
                        <div class="salu-service-icon-box salu-icon-green mb-3 mx-auto">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <h5 class="salu-service-title">Fee Challan</h5>
                        <p class="salu-service-desc">Generate challan &amp; submit payment</p>
                    </div>
                    <span class="salu-service-link mt-3">Manage Fees <i class="fas fa-arrow-right ms-1"></i></span>
                </div>
            </div>
        </a>
    </div>

    <!-- EXAMINATION FORM -->
    <div class="col-sm-6 col-lg-4 col-xl-3">
        <a href="{{ route('exams.form') }}" class="text-decoration-none h-100 d-block">
            <div class="card salu-service-card h-100 position-relative">
                <span class="salu-corner-badge">Coming Soon</span>
                <div class="card-body p-4 text-center d-flex flex-column justify-content-between">
                    <div>
                        <div class="salu-service-icon-box salu-icon-indigo mb-3 mx-auto">
                            <i class="fas fa-file-lines"></i>
                        </div>
                        <h5 class="salu-service-title">Examination Form</h5>
                        <p class="salu-service-desc">Exam registration (`eFormI`)</p>
                    </div>
                    <span class="salu-service-link mt-3 text-indigo">Exam Form <i class="fas fa-arrow-right ms-1"></i></span>
                </div>
            </div>
        </a>
    </div>

    <!-- ADMIT CARD -->
    <div class="col-sm-6 col-lg-4 col-xl-3">
        <a href="{{ route('exams.admit-card') }}" class="text-decoration-none h-100 d-block">
            <div class="card salu-service-card h-100">
                <div class="card-body p-4 text-center d-flex flex-column justify-content-between">
                    <div>
                        <div class="salu-service-icon-box salu-icon-purple mb-3 mx-auto">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <h5 class="salu-service-title">Admit Card</h5>
                        <p class="salu-service-desc">Download exam hall entry admit card</p>
                    </div>
                    <span class="salu-service-link mt-3">Get Admit Card <i class="fas fa-arrow-right ms-1"></i></span>
                </div>
            </div>
        </a>
    </div>

    <!-- RESULTS CARD -->
    <div class="col-sm-6 col-lg-4 col-xl-3">
        <a href="{{ route('exams.results') }}" class="text-decoration-none h-100 d-block">
            <div class="card salu-service-card h-100">
                <div class="card-body p-4 text-center d-flex flex-column justify-content-between">
                    <div>
                        <div class="salu-service-icon-box salu-icon-pink mb-3 mx-auto">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h5 class="salu-service-title">Semester Results</h5>
                        <p class="salu-service-desc">View published GPA and mark sheets</p>
                    </div>
                    <span class="salu-service-link mt-3">View Results <i class="fas fa-arrow-right ms-1"></i></span>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- MY ENROLLMENT OVERVIEW CARD -->
@if(isset($myEnrollment))
    @php
        $statusClass = match($myEnrollment->status) {
            'APPROVED' => 'bg-success text-white',
            'REJECTED' => 'bg-danger text-white',
            'PENDING' => 'bg-warning text-dark',
            default => 'bg-secondary text-white'
        };
    @endphp

    <div class="row mb-4">
        <div class="col-12">
            <div class="card salu-overview-card">
                <div class="card-header salu-overview-header d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-graduation-cap text-warning fa-lg"></i>
                        <h5 class="mb-0 fw-bold text-white">Enrollment Application Summary</h5>
                    </div>
                    <span class="badge {{ $statusClass }} fs-6 px-3 py-2 rounded-pill shadow-sm">
                        {{ $myEnrollment->status }}
                    </span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-sm-6 col-md-3">
                            <div class="salu-metric-box">
                                <span class="salu-metric-label">ACADEMIC PROGRAM</span>
                                <strong class="salu-metric-value">{{ $myEnrollment->program }}</strong>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="salu-metric-box">
                                <span class="salu-metric-label">SESSION</span>
                                <strong class="salu-metric-value">{{ $myEnrollment->session }}</strong>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="salu-metric-box">
                                <span class="salu-metric-label">COLLEGE / DEPARTMENT</span>
                                <strong class="salu-metric-value">{{ $myEnrollment->college->name ?? 'Main Campus' }}</strong>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="salu-metric-box">
                                <span class="salu-metric-label">ENROLLMENT ID</span>
                                <strong class="salu-metric-value text-primary">#{{ strtoupper(substr($myEnrollment->id, 0, 8)) }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="text-end mt-4 pt-3 border-top border-light-subtle">
                        <a href="{{ route('enrollment.details', $myEnrollment->id) }}" class="btn salu-btn-pill-blue">
                            <i class="fas fa-file-lines me-2"></i> View Full Application
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@endsection
