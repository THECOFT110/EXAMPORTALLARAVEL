@extends('layouts.app')

@section('title', 'University Student Portal - Shah Abdul Latif University')

@php
    $title = 'Academic Dashboard';
    $authUser = auth()->user();
    $currentYear = date('Y');
    $nextYear = $currentYear + 1;
@endphp

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet" />
<style>
/* ══════════════════════════════════════════════════════════════════
   SALU HIGHER EDUCATION UNIVERSITY DASHBOARD - ENTERPRISE GRADIENTS
   ══════════════════════════════════════════════════════════════════ */
:root {
    --salu-navy-deep: #0a1128;
    --salu-navy-main: #0f1c3f;
    --salu-card-radius: 18px;
    --salu-radius-sm: 10px;
    
    /* Curated University Gradients */
    --grad-sapphire: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    --grad-sunset: linear-gradient(135deg, #e11d48 0%, #f97316 100%);
    --grad-emerald: linear-gradient(135deg, #059669 0%, #10b981 100%);
    --grad-indigo: linear-gradient(135deg, #4338ca 0%, #6366f1 100%);
    --grad-cyan: linear-gradient(135deg, #0284c7 0%, #06b6d4 100%);
    --grad-amber: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
    --grad-violet: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
    --grad-dark-mesh: linear-gradient(135deg, #070e24 0%, #0f1c3f 50%, #172a5a 100%);
}

.salu-dash-container {
    font-family: 'Plus Jakarta Sans', sans-serif;
    color: #0f172a;
    padding-bottom: 4rem;
}

/* ── UNIVERSITY EXECUTIVE HERO BANNER ── */
.salu-dash-hero {
    background: var(--grad-dark-mesh);
    border-radius: var(--salu-card-radius);
    padding: 2.25rem 2.75rem;
    color: #ffffff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 16px 36px rgba(15, 28, 63, 0.28);
    border: 1px solid rgba(255, 255, 255, 0.12);
    margin-bottom: 2rem;
}

.salu-dash-hero::before {
    content: '';
    position: absolute;
    top: -60%;
    right: -20%;
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(249, 115, 22, 0.2) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
    pointer-events: none;
}

.salu-dash-hero::after {
    content: '';
    position: absolute;
    bottom: -60%;
    left: 15%;
    width: 450px;
    height: 450px;
    background: radial-gradient(circle, rgba(37, 99, 235, 0.25) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
    pointer-events: none;
}

.salu-hero-avatar-ring {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    padding: 3px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 22px rgba(249, 115, 22, 0.4);
    flex-shrink: 0;
}

.salu-hero-avatar-inner {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: #0f1c3f;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 1.75rem;
    font-weight: 800;
}

.salu-hero-heading {
    font-family: 'Playfair Display', serif;
    font-size: 1.85rem;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
    letter-spacing: -0.01em;
}

.salu-stat-glass-pill {
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 12px;
    padding: 0.65rem 1.35rem;
    color: #ffffff;
    display: flex;
    flex-direction: column;
    min-width: 140px;
}

/* ── 3D FLIP SERVICE CARDS ── */
.salu-flip-scene {
    perspective: 1400px;
    margin-bottom: 1.5rem;
    height: 310px;
}

.salu-flip-card {
    width: 100%;
    height: 100%;
    position: relative;
    transform-style: preserve-3d;
    transition: transform 0.65s cubic-bezier(0.34, 1.56, 0.64, 1);
    cursor: pointer;
}

.salu-flip-card.is-flipped {
    transform: rotateY(180deg);
}

.salu-flip-face {
    position: absolute;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
    border-radius: var(--salu-card-radius);
    padding: 1.75rem 1.85rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: 0 10px 30px -5px rgba(15, 28, 63, 0.08), 0 4px 12px -2px rgba(15, 28, 63, 0.04);
    transition: box-shadow 0.3s ease;
    border: 1px solid rgba(226, 232, 240, 0.85);
    background: #ffffff;
    overflow: hidden;
}

.salu-flip-scene:hover .salu-flip-face {
    box-shadow: 0 20px 35px -8px rgba(15, 28, 63, 0.16);
}

/* FRONT FACE */
.salu-flip-front {
    background: #ffffff;
    z-index: 2;
    transform: rotateY(0deg);
}

.salu-card-glow-bar {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
}

.salu-icon-container {
    width: 54px;
    height: 54px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 1.45rem;
    box-shadow: 0 8px 18px rgba(0, 0, 0, 0.15);
    margin-bottom: 1.15rem;
}

.salu-card-title {
    font-size: 1.18rem;
    font-weight: 800;
    color: #0f1c3f;
    margin-bottom: 0.35rem;
    letter-spacing: -0.01em;
}

.salu-card-desc {
    font-size: 0.84rem;
    color: #64748b;
    line-height: 1.45;
    margin-bottom: 0;
}

.salu-card-footer-action {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 0.75rem;
    border-top: 1px solid #f1f5f9;
}

/* BACK FACE */
.salu-flip-back {
    transform: rotateY(180deg);
    color: #ffffff;
    z-index: 1;
    border: none;
}

.salu-back-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    padding-bottom: 0.65rem;
    margin-bottom: 0.85rem;
}

.salu-back-title {
    font-size: 1.05rem;
    font-weight: 800;
    margin: 0;
    color: #ffffff;
}

.salu-back-list {
    list-style: none;
    padding: 0;
    margin: 0 0 1rem 0;
    font-size: 0.82rem;
}

.salu-back-list li {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    margin-bottom: 0.45rem;
    color: rgba(255, 255, 255, 0.95);
    line-height: 1.35;
}

.salu-back-list li i {
    margin-top: 2px;
    color: #fef08a;
    font-size: 0.75rem;
}

.salu-btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    padding: 0.68rem 1rem;
    border-radius: var(--salu-radius-sm);
    font-weight: 700;
    font-size: 0.88rem;
    background: #ffffff;
    color: #0f1c3f;
    border: none;
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transition: transform 0.15s ease, background 0.15s ease;
}
.salu-btn-action:hover {
    background: #f8fafc;
    color: #000000;
    transform: translateY(-2px);
}

.salu-btn-flip-back {
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: #ffffff;
    border-radius: 50%;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    cursor: pointer;
    transition: background 0.2s;
}
.salu-btn-flip-back:hover {
    background: rgba(255, 255, 255, 0.4);
}

/* ── STATUS BANNERS ── */
.salu-dash-banner {
    border-radius: var(--salu-radius-sm);
    padding: 1.15rem 1.5rem;
    margin-bottom: 1.75rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
}

.salu-banner-amber {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    border: 1.5px solid #fde68a;
}
.salu-banner-green {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border: 1.5px solid #bbf7d0;
}
.salu-banner-blue {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border: 1.5px solid #bfdbfe;
}

/* ── OVERVIEW METRICS CARD ── */
.salu-summary-card {
    background: #ffffff;
    border-radius: var(--salu-card-radius);
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 25px rgba(15, 28, 63, 0.05);
    overflow: hidden;
    margin-bottom: 2rem;
}

.salu-summary-header {
    background: var(--grad-sapphire);
    color: #ffffff;
    padding: 1.15rem 1.75rem;
}

/* Responsive */
@media (max-width: 768px) {
    .salu-dash-hero { padding: 1.5rem 1.25rem; }
    .salu-flip-scene { height: 320px; }
}
</style>
@endpush

@section('content')
<div class="container-fluid salu-dash-container">

    <!-- ═══════════════════ TOP UNIVERSITY GREETING BANNER ═══════════════════ -->
    <div class="salu-dash-hero">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4 position-relative" style="z-index: 2;">
            <div class="d-flex align-items-center gap-3 gap-md-4">
                <div class="salu-hero-avatar-ring">
                    <div class="salu-hero-avatar-inner">
                        {{ strtoupper(substr($authUser?->full_name ?? 'U', 0, 1)) }}
                    </div>
                </div>
                <div>
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                        <h1 class="salu-hero-heading">Welcome, {{ $authUser?->full_name ?? 'Undergraduate Candidate' }}</h1>
                        <span class="badge bg-warning text-dark fw-bold px-2.5 py-1 rounded-pill">
                            <i class="fas fa-university me-1"></i> University Scholar
                        </span>
                    </div>
                    <div class="text-white-50 small d-flex flex-wrap gap-3">
                        <span><i class="fas fa-id-card text-warning me-1"></i> CNIC: <strong>{{ $authUser?->cnic ?? 'Not Registered' }}</strong></span>
                        <span>&bull;</span>
                        <span><i class="fas fa-envelope text-warning me-1"></i> {{ $authUser?->email }}</span>
                        <span>&bull;</span>
                        <span><i class="fas fa-phone text-warning me-1"></i> {{ $authUser?->phone ?? 'Not Available' }}</span>
                    </div>
                </div>
            </div>

            <!-- Stats Right Header -->
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <div class="salu-stat-glass-pill">
                    <span class="text-white-50 small text-uppercase fw-bold" style="font-size:0.68rem;">Institution</span>
                    <span class="fw-bold fs-6"><i class="fas fa-building-columns text-warning me-1"></i> SALU Khairpur</span>
                </div>
                <div class="salu-stat-glass-pill">
                    <span class="text-white-50 small text-uppercase fw-bold" style="font-size:0.68rem;">Academic Session</span>
                    <span class="fw-bold fs-6 text-warning"><i class="fas fa-calendar-alt me-1"></i> {{ $currentYear }}-{{ $nextYear }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════ NOTICES / FEE STATUS BANNERS ═══════════════════ -->
    @if(isset($latestFee) && !in_array($latestFee->status, ['PAID', 'VERIFIED']))
        <div class="salu-dash-banner salu-banner-amber">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;border-radius:50%;background:#f59e0b;color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.2rem;">
                    <i class="fas fa-receipt"></i>
                </div>
                <div>
                    <strong class="d-block text-dark fs-6">
                        Pending {{ $latestFee->type === 'EXAMINATION_FEE' ? 'University Semester Examination Fee' : 'University Enrollment Fee' }}: PKR {{ number_format($latestFee->amount, 0) }}
                    </strong>
                    <span class="text-muted small">
                        Challan Voucher No: <code>{{ $latestFee->challan_number }}</code> &bull; Due Date: <strong>{{ $latestFee->due_date ? $latestFee->due_date->format('d M Y') : 'Active' }}</strong>
                    </span>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('payment.checkout', $latestFee->id) }}" class="btn btn-success fw-bold px-3 py-2 rounded-pill shadow-sm">
                    <i class="fas fa-bolt me-1"></i> Pay Online
                </a>
                <a href="{{ route('enrollment.challan-pdf', $latestFee->id) }}" target="_blank" class="btn btn-outline-dark fw-bold px-3 py-2 rounded-pill">
                    <i class="fas fa-print me-1"></i> Download Challan
                </a>
            </div>
        </div>
    @endif

    <!-- ═══════════════════ SECTION HEADER ═══════════════════ -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h2 class="fw-bold text-dark mb-0" style="font-size: 1.25rem;">
                <i class="fas fa-graduation-cap me-2 text-primary"></i>University Academic Directorate &amp; Examination Services
            </h2>
            <small class="text-muted">Higher Education Commission (HEC) degree registration, examination scheduling, and official transcripts</small>
        </div>
    </div>

    <!-- ═══════════════════ 3D FLIP SERVICE CARDS GRID ═══════════════════ -->
    <div class="row g-4">

        <!-- 1. ENROLLMENT APPLICATION CARD (Sunset Coral Gradient) -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="salu-flip-scene">
                <div class="salu-flip-card" onclick="toggleCardFlip(this, event)">
                    <!-- Front Face -->
                    <div class="salu-flip-face salu-flip-front">
                        <div class="salu-card-glow-bar" style="background: var(--grad-sunset);"></div>
                        <div>
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="salu-icon-container" style="background: var(--grad-sunset);">
                                    <i class="fas fa-file-signature"></i>
                                </div>
                                <span class="badge bg-danger-subtle text-danger fw-bold rounded-pill px-2.5 py-1">Admission Dossier</span>
                            </div>
                            <h3 class="salu-card-title">Enrollment Application</h3>
                            <p class="salu-card-desc">Formal university degree registration, affiliated college program assignment, and provisional admission certificate.</p>
                        </div>
                        <div class="salu-card-footer-action">
                            <span class="text-muted small fw-semibold"><i class="fas fa-circle-nodes me-1 text-danger"></i> 7 Verification Stages</span>
                            <span class="text-danger fw-bold small">Open Service <i class="fas fa-arrow-right ms-1"></i></span>
                        </div>
                    </div>
                    <!-- Back Face -->
                    <div class="salu-flip-face salu-flip-back" style="background: var(--grad-sunset);">
                        <div>
                            <div class="salu-back-header">
                                <h4 class="salu-back-title"><i class="fas fa-university me-1"></i> Degree Enrollment</h4>
                                <button type="button" class="salu-btn-flip-back" onclick="event.stopPropagation(); toggleCardFlip(this.closest('.salu-flip-card'), event);">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <ul class="salu-back-list">
                                <li><i class="fas fa-check-circle"></i> Affiliated College &amp; Faculty selection</li>
                                <li><i class="fas fa-check-circle"></i> Intermediate / DAE academic verification</li>
                                <li><i class="fas fa-check-circle"></i> Digital CNIC &amp; testimonial repository</li>
                                <li><i class="fas fa-check-circle"></i> Official University Admission Certificate</li>
                            </ul>
                        </div>
                        <div>
                            <a href="{{ route('enrollment.create') }}" class="salu-btn-action" onclick="event.stopPropagation();">
                                <i class="fas fa-pen-to-square text-danger"></i> Open Enrollment Form
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. ENROLLMENT REGISTRATION CARD (Cyan Gradient) -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="salu-flip-scene">
                <div class="salu-flip-card" onclick="toggleCardFlip(this, event)">
                    <!-- Front Face -->
                    <div class="salu-flip-face salu-flip-front">
                        <div class="salu-card-glow-bar" style="background: var(--grad-cyan);"></div>
                        <div>
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="salu-icon-container" style="background: var(--grad-cyan);">
                                    <i class="fas fa-id-card-clip"></i>
                                </div>
                                <span class="badge bg-info-subtle text-info fw-bold rounded-pill px-2.5 py-1">HEC Registration</span>
                            </div>
                            <h3 class="salu-card-title">Enrollment Card</h3>
                            <p class="salu-card-desc">Permanent university registration credentials, institutional student identity, and QR-authenticated verification record.</p>
                        </div>
                        <div class="salu-card-footer-action">
                            <span class="text-muted small fw-semibold"><i class="fas fa-shield-halved me-1 text-info"></i> Official Credential</span>
                            <span class="text-primary fw-bold small">View Card <i class="fas fa-arrow-right ms-1"></i></span>
                        </div>
                    </div>
                    <!-- Back Face -->
                    <div class="salu-flip-face salu-flip-back" style="background: var(--grad-cyan);">
                        <div>
                            <div class="salu-back-header">
                                <h4 class="salu-back-title"><i class="fas fa-id-badge me-1"></i> Permanent Registration</h4>
                                <button type="button" class="salu-btn-flip-back" onclick="event.stopPropagation(); toggleCardFlip(this.closest('.salu-flip-card'), event);">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <ul class="salu-back-list">
                                <li><i class="fas fa-check-circle"></i> Permanent university registration number</li>
                                <li><i class="fas fa-check-circle"></i> Encrypted QR Code verification</li>
                                <li><i class="fas fa-check-circle"></i> Institutional seal &amp; Registrar authority</li>
                                <li><i class="fas fa-check-circle"></i> Mandatory credential for exam halls</li>
                            </ul>
                        </div>
                        <div>
                            <a href="{{ route('enrollment.card') }}" class="salu-btn-action" onclick="event.stopPropagation();">
                                <i class="fas fa-download text-primary"></i> Download Official Card
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. UNIVERSITY FEE CHALLAN (Emerald Ocean Gradient) -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="salu-flip-scene">
                <div class="salu-flip-card" onclick="toggleCardFlip(this, event)">
                    <!-- Front Face -->
                    <div class="salu-flip-face salu-flip-front">
                        <div class="salu-card-glow-bar" style="background: var(--grad-emerald);"></div>
                        <div>
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="salu-icon-container" style="background: var(--grad-emerald);">
                                    <i class="fas fa-receipt"></i>
                                </div>
                                <span class="badge bg-success-subtle text-success fw-bold rounded-pill px-2.5 py-1">Accounts &amp; Treasury</span>
                            </div>
                            <h3 class="salu-card-title">Fee Challan &amp; Payments</h3>
                            <p class="salu-card-desc">Generate official bank vouchers (HBL, Allied Bank, Sindh Bank) or complete instant 1Link / JazzCash digital reconciliation.</p>
                        </div>
                        <div class="salu-card-footer-action">
                            <span class="text-muted small fw-semibold"><i class="fas fa-check-double me-1 text-success"></i> Auto-Reconciled</span>
                            <span class="text-success fw-bold small">Manage Fees <i class="fas fa-arrow-right ms-1"></i></span>
                        </div>
                    </div>
                    <!-- Back Face -->
                    <div class="salu-flip-face salu-flip-back" style="background: var(--grad-emerald);">
                        <div>
                            <div class="salu-back-header">
                                <h4 class="salu-back-title"><i class="fas fa-building-columns me-1"></i> University Treasury</h4>
                                <button type="button" class="salu-btn-flip-back" onclick="event.stopPropagation(); toggleCardFlip(this.closest('.salu-flip-card'), event);">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <ul class="salu-back-list">
                                <li><i class="fas fa-check-circle"></i> University Enrollment Fee: PKR 1,500</li>
                                <li><i class="fas fa-check-circle"></i> Semester Examination Fee: PKR 2,000</li>
                                <li><i class="fas fa-check-circle"></i> Instant 1Link / JazzCash transaction clearance</li>
                                <li><i class="fas fa-check-circle"></i> Download 4-Copy Bank Deposit Slip</li>
                            </ul>
                        </div>
                        <div>
                            <a href="{{ route('exams.fee-challan') }}" class="salu-btn-action" onclick="event.stopPropagation();">
                                <i class="fas fa-credit-card text-success"></i> Open Fee Portal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. SEMESTER EXAMINATION FORM (Indigo Gradient) -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="salu-flip-scene">
                <div class="salu-flip-card" onclick="toggleCardFlip(this, event)">
                    <!-- Front Face -->
                    <div class="salu-flip-face salu-flip-front">
                        <div class="salu-card-glow-bar" style="background: var(--grad-indigo);"></div>
                        <div>
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="salu-icon-container" style="background: var(--grad-indigo);">
                                    <i class="fas fa-file-lines"></i>
                                </div>
                                <span class="badge bg-primary-subtle text-primary fw-bold rounded-pill px-2.5 py-1">Semester Schedule</span>
                            </div>
                            <h3 class="salu-card-title">Examination Registration</h3>
                            <p class="salu-card-desc">Course registration for regular terminal semester examinations and repeat / improver papers (`eForm-I`).</p>
                        </div>
                        <div class="salu-card-footer-action">
                            <span class="text-muted small fw-semibold"><i class="fas fa-book-open me-1 text-primary"></i> Major &amp; Electives</span>
                            <span class="text-primary fw-bold small">Course Form <i class="fas fa-arrow-right ms-1"></i></span>
                        </div>
                    </div>
                    <!-- Back Face -->
                    <div class="salu-flip-face salu-flip-back" style="background: var(--grad-indigo);">
                        <div>
                            <div class="salu-back-header">
                                <h4 class="salu-back-title"><i class="fas fa-graduation-cap me-1"></i> Exam Controller</h4>
                                <button type="button" class="salu-btn-flip-back" onclick="event.stopPropagation(); toggleCardFlip(this.closest('.salu-flip-card'), event);">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <ul class="salu-back-list">
                                <li><i class="fas fa-check-circle"></i> Degree curriculum course code selection</li>
                                <li><i class="fas fa-check-circle"></i> Attendance threshold certification</li>
                                <li><i class="fas fa-check-circle"></i> Repeat / Improvement course additions</li>
                                <li><i class="fas fa-check-circle"></i> Direct submission to Examination Branch</li>
                            </ul>
                        </div>
                        <div>
                            <a href="{{ route('exams.form') }}" class="salu-btn-action" onclick="event.stopPropagation();">
                                <i class="fas fa-file-pen text-primary"></i> Register Courses
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. EXAMINATION ADMIT CARD (Amber / Gold Gradient) -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="salu-flip-scene">
                <div class="salu-flip-card" onclick="toggleCardFlip(this, event)">
                    <!-- Front Face -->
                    <div class="salu-flip-face salu-flip-front">
                        <div class="salu-card-glow-bar" style="background: var(--grad-amber);"></div>
                        <div>
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="salu-icon-container" style="background: var(--grad-amber);">
                                    <i class="fas fa-id-card"></i>
                                </div>
                                <span class="badge bg-warning-subtle text-dark fw-bold rounded-pill px-2.5 py-1">Examination Center</span>
                            </div>
                            <h3 class="salu-card-title">Admit Card &amp; Roll No.</h3>
                            <p class="salu-card-desc">Assigned examination center location, seat allotment number, paper timetable, and Controller authorized entry slip.</p>
                        </div>
                        <div class="salu-card-footer-action">
                            <span class="text-muted small fw-semibold"><i class="fas fa-location-dot me-1 text-warning"></i> Center Allocation</span>
                            <span class="text-warning fw-bold small">Admit Slip <i class="fas fa-arrow-right ms-1"></i></span>
                        </div>
                    </div>
                    <!-- Back Face -->
                    <div class="salu-flip-face salu-flip-back" style="background: var(--grad-amber);">
                        <div>
                            <div class="salu-back-header">
                                <h4 class="salu-back-title text-dark"><i class="fas fa-door-open me-1"></i> Exam Hall Authority</h4>
                                <button type="button" class="salu-btn-flip-back text-dark" onclick="event.stopPropagation(); toggleCardFlip(this.closest('.salu-flip-card'), event);">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <ul class="salu-back-list text-dark">
                                <li><i class="fas fa-check-circle text-dark"></i> Roll number &amp; Center venue location</li>
                                <li><i class="fas fa-check-circle text-dark"></i> Paper schedule with exact timings</li>
                                <li><i class="fas fa-check-circle text-dark"></i> Student photo &amp; Controller seal</li>
                                <li><i class="fas fa-check-circle text-dark"></i> Mandatory for examination entry</li>
                            </ul>
                        </div>
                        <div>
                            <a href="{{ route('exams.admit-card') }}" class="salu-btn-action" onclick="event.stopPropagation();">
                                <i class="fas fa-print text-warning"></i> Download Admit Card
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. ACADEMIC RESULTS & CGPA (Ruby Rose Gradient) -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="salu-flip-scene">
                <div class="salu-flip-card" onclick="toggleCardFlip(this, event)">
                    <!-- Front Face -->
                    <div class="salu-flip-face salu-flip-front">
                        <div class="salu-card-glow-bar" style="background: var(--grad-sunset);"></div>
                        <div>
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="salu-icon-container" style="background: var(--grad-sunset);">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <span class="badge bg-danger-subtle text-danger fw-bold rounded-pill px-2.5 py-1">GPA &amp; Transcripts</span>
                            </div>
                            <h3 class="salu-card-title">Semester Results &amp; GPA</h3>
                            <p class="salu-card-desc">Official semester examination gazettes, Cumulative Grade Point Average (CGPA), course credits, and transcripts.</p>
                        </div>
                        <div class="salu-card-footer-action">
                            <span class="text-muted small fw-semibold"><i class="fas fa-chart-line me-1 text-danger"></i> Official Gazette</span>
                            <span class="text-danger fw-bold small">View Marks <i class="fas fa-arrow-right ms-1"></i></span>
                        </div>
                    </div>
                    <!-- Back Face -->
                    <div class="salu-flip-face salu-flip-back" style="background: var(--grad-sunset);">
                        <div>
                            <div class="salu-back-header">
                                <h4 class="salu-back-title"><i class="fas fa-award me-1"></i> Academic Transcripts</h4>
                                <button type="button" class="salu-btn-flip-back" onclick="event.stopPropagation(); toggleCardFlip(this.closest('.salu-flip-card'), event);">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <ul class="salu-back-list">
                                <li><i class="fas fa-check-circle"></i> Official verified semester marksheet</li>
                                <li><i class="fas fa-check-circle"></i> Grade Point Average (GPA &amp; CGPA)</li>
                                <li><i class="fas fa-check-circle"></i> Online re-totaling / paper scrutiny</li>
                                <li><i class="fas fa-check-circle"></i> University transcript ledger archive</li>
                            </ul>
                        </div>
                        <div>
                            <a href="{{ route('exams.results') }}" class="salu-btn-action" onclick="event.stopPropagation();">
                                <i class="fas fa-chart-line text-danger"></i> Check Semester Results
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ═══════════════════ APPLICATION SUMMARY SECTION ═══════════════════ -->
    @if(isset($myEnrollment))
        <div class="salu-summary-card mt-2">
            <div class="salu-summary-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-university text-warning fs-5"></i>
                    <h5 class="mb-0 fw-bold">Active University Enrollment Record</h5>
                </div>
                <span class="badge bg-white text-dark fw-bold px-3 py-1.5 rounded-pill">
                    Status: {{ $myEnrollment->status }}
                </span>
            </div>
            <div class="p-4">
                <div class="row g-4">
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size:0.7rem;">University Degree Program</small>
                        <strong class="text-dark fs-6">{{ $myEnrollment->program }}</strong>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size:0.7rem;">Academic Session</small>
                        <strong class="text-dark fs-6">{{ $myEnrollment->session }}</strong>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size:0.7rem;">Affiliated College / Institute</small>
                        <strong class="text-dark fs-6">{{ $myEnrollment->college->name ?? 'SALU Department / Campus' }}</strong>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size:0.7rem;">Registration Dossier ID</small>
                        <strong class="text-primary fs-6">#{{ strtoupper(substr($myEnrollment->id, 0, 8)) }}</strong>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                    <a href="{{ route('enrollment.details', $myEnrollment->id) }}" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <i class="fas fa-file-lines me-1"></i> View Full Application Dossier
                    </a>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
// Interactive 3D Card Flip Handler
function toggleCardFlip(cardEl, event) {
    // If user clicked direct action link inside back face, let navigation proceed
    if (event.target.closest('.salu-btn-action')) {
        return;
    }
    cardEl.classList.toggle('is-flipped');
}
</script>
@endpush
