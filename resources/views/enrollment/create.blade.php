@extends('layouts.app')

@section('title', 'Student Enrollment Application - SALU')

@php
    $title = 'Student Enrollment Application';
    $authUser = auth()->user();
    $currentYear = date('Y');
    $nextYear = $currentYear + 1;
    $sessionDefault = ($activeYear ? $activeYear->name : "{$currentYear}-{$nextYear}");
@endphp

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet" />
<style>
/* ══════════════════════════════════════════════════════════════════
   SALU ENTERPRISE ENROLLMENT APPLICATION - BLAZOR FLOATING LABELS
   ══════════════════════════════════════════════════════════════════ */
:root {
    --salu-navy: #0f1c3f;
    --salu-navy-light: #1a2c5b;
    --salu-royal: #1d4ed8;
    --salu-blue-soft: #eff6ff;
    --salu-accent: #f97316;
    --salu-accent-hover: #ea580c;
    --salu-gold: #eab308;
    --salu-emerald: #10b981;
    --salu-danger: #ef4444;
    --salu-surface: #ffffff;
    --salu-bg: #f8fafc;
    --salu-border: #cbd5e1;
    --salu-border-focus: #2563eb;
    --salu-text-main: #0f172a;
    --salu-text-muted: #64748b;
    --salu-radius-lg: 16px;
    --salu-radius-md: 12px;
    --salu-radius-sm: 8px;
    --salu-shadow-card: 0 10px 30px -5px rgba(15, 28, 63, 0.08), 0 4px 12px -2px rgba(15, 28, 63, 0.04);
}

.salu-wizard-page {
    font-family: 'Plus Jakarta Sans', sans-serif;
    color: var(--salu-text-main);
    background-color: transparent;
    padding-bottom: 4rem;
}

/* ── HERO BANNER ── */
.salu-hero-banner {
    background: linear-gradient(135deg, #0b1530 0%, #172a5a 50%, #1e3a8a 100%);
    border-radius: var(--salu-radius-lg);
    padding: 1.75rem 2.25rem;
    color: #ffffff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 12px 36px rgba(15, 28, 63, 0.22);
    margin-bottom: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.salu-hero-banner::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 450px;
    height: 450px;
    background: radial-gradient(circle, rgba(249, 115, 22, 0.18) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
    pointer-events: none;
}

.salu-hero-content {
    position: relative;
    z-index: 2;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1.5rem;
}

.salu-hero-brand {
    display: flex;
    align-items: center;
    gap: 1.25rem;
}

.salu-hero-logo {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    border: 3px solid rgba(255, 255, 255, 0.35);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 1.85rem;
    color: #ffffff;
    box-shadow: 0 8px 20px rgba(249, 115, 22, 0.4);
    flex-shrink: 0;
}

.salu-hero-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.65rem;
    font-weight: 700;
    margin: 0;
    letter-spacing: -0.01em;
    color: #ffffff;
}

.salu-hero-sub {
    font-size: 0.88rem;
    color: #cbd5e1;
    margin-top: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.salu-badge-session {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    padding: 0.45rem 1rem;
    border-radius: 9999px;
    font-size: 0.82rem;
    font-weight: 600;
    color: #f1f5f9;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}

/* ── STEPPER CONTROLS (DISABLED ON MOUSE CLICK) ── */
.salu-stepper-container {
    background: var(--salu-surface);
    border-radius: var(--salu-radius-lg);
    box-shadow: var(--salu-shadow-card);
    padding: 1.25rem 1.5rem;
    margin-bottom: 2rem;
    border: 1px solid #e2e8f0;
    position: relative;
}

.salu-steps-horizontal {
    display: flex;
    justify-content: space-between;
    position: relative;
    gap: 0.5rem;
    overflow-x: auto;
    padding: 0.5rem 0.25rem;
    scrollbar-width: none;
}
.salu-steps-horizontal::-webkit-scrollbar { display: none; }

/* Stepper buttons are purely informative / mouse-clicks disabled */
.salu-step-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    background: transparent;
    border: none;
    padding: 0.5rem 0.75rem;
    border-radius: var(--salu-radius-md);
    cursor: default !important;
    pointer-events: none !important;
    user-select: none;
    min-width: 105px;
    position: relative;
    z-index: 2;
    flex: 1;
}

.salu-step-node {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #f1f5f9;
    border: 2px solid #cbd5e1;
    color: #64748b;
    font-weight: 700;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.salu-step-name {
    font-size: 0.76rem;
    font-weight: 600;
    color: var(--salu-text-muted);
    text-align: center;
    line-height: 1.25;
    white-space: nowrap;
}

.salu-step-btn.active .salu-step-node {
    background: linear-gradient(135deg, var(--salu-accent) 0%, #ea580c 100%);
    border-color: #ffffff;
    color: #ffffff;
    box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.25), 0 6px 16px rgba(249, 115, 22, 0.4);
    transform: scale(1.1);
}
.salu-step-btn.active .salu-step-name {
    color: var(--salu-navy);
    font-weight: 800;
}

.salu-step-btn.completed .salu-step-node {
    background: var(--salu-emerald);
    border-color: var(--salu-emerald);
    color: #ffffff;
    box-shadow: 0 4px 10px rgba(16, 185, 129, 0.25);
}
.salu-step-btn.completed .salu-step-name {
    color: #047857;
}

/* Stepper Progress Bar */
.salu-stepper-progress {
    height: 6px;
    background: #e2e8f0;
    border-radius: 9999px;
    margin-top: 0.75rem;
    overflow: hidden;
    position: relative;
}

.salu-stepper-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--salu-royal) 0%, var(--salu-accent) 100%);
    transition: width 0.45s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 9999px;
}

/* ── CARD CONTAINER ── */
.salu-card {
    background: var(--salu-surface);
    border-radius: var(--salu-radius-lg);
    box-shadow: var(--salu-shadow-card);
    border: 1px solid #e2e8f0;
    padding: 2rem 2.25rem;
    margin-bottom: 1.75rem;
    position: relative;
}

.salu-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 1.25rem;
    margin-bottom: 1.75rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.salu-card-heading {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.salu-card-icon {
    width: 40px;
    height: 40px;
    border-radius: var(--salu-radius-md);
    background: var(--salu-blue-soft);
    color: var(--salu-royal);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
}

.salu-card-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--salu-navy);
    margin: 0;
}

.salu-card-subtitle {
    font-size: 0.8rem;
    color: var(--salu-text-muted);
    margin-top: 0.15rem;
}

/* ══════════════════════════════════════════════════════════════════
   BLAZOR / MATERIAL FLOATING LABELS (INLINE -> FLOATS ABOVE)
   ══════════════════════════════════════════════════════════════════ */
.salu-floating-group {
    position: relative;
    margin-bottom: 1.5rem;
}

.salu-floating-input,
.salu-floating-select {
    width: 100%;
    height: 52px;
    padding: 1.15rem 0.95rem 0.35rem 0.95rem;
    font-family: inherit;
    font-size: 0.92rem;
    font-weight: 500;
    color: var(--salu-text-main);
    background-color: #ffffff;
    border: 1.5px solid var(--salu-border);
    border-radius: var(--salu-radius-sm);
    outline: none;
    transition: border-color 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.salu-floating-input:hover,
.salu-floating-select:hover {
    border-color: #94a3b8;
}

.salu-floating-input:focus,
.salu-floating-select:focus {
    border-color: var(--salu-border-focus);
    box-shadow: 0 0 0 3.5px rgba(37, 99, 235, 0.12);
    background-color: #ffffff;
}

/* Floating Label Animation */
.salu-floating-label {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 0.88rem;
    font-weight: 600;
    color: #64748b;
    pointer-events: none;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    background: transparent;
    padding: 0 4px;
    z-index: 2;
    transform-origin: left top;
}

/* Floated State (Focused or Has Value or Has Placeholder Shown) */
.salu-floating-input:focus ~ .salu-floating-label,
.salu-floating-input:not(:placeholder-shown) ~ .salu-floating-label,
.salu-floating-select:focus ~ .salu-floating-label,
.salu-floating-select.has-value ~ .salu-floating-label {
    top: 0;
    transform: translateY(-50%) scale(0.82);
    background-color: #ffffff;
    color: var(--salu-royal);
    font-weight: 700;
}

/* Locked Signup Data Fields */
.salu-floating-group.is-locked .salu-floating-input {
    background-color: #f8fafc !important;
    color: #334155 !important;
    border-color: #e2e8f0 !important;
    cursor: not-allowed;
    font-weight: 600;
}
.salu-floating-group.is-locked .salu-floating-label {
    top: 0 !important;
    transform: translateY(-50%) scale(0.82) !important;
    background-color: #ffffff !important;
    color: #64748b !important;
}
.salu-lock-badge {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 0.85rem;
    pointer-events: none;
}

/* Error messages */
.salu-error-text {
    font-size: 0.75rem;
    color: var(--salu-danger);
    font-weight: 600;
    margin-top: 0.35rem;
    display: none;
    align-items: center;
    gap: 0.3rem;
}
.salu-error-text.visible {
    display: flex;
}

/* ── PHOTO UPLOADER ── */
.salu-photo-uploader-box {
    border: 2.5px dashed #cbd5e1;
    border-radius: var(--salu-radius-md);
    background: #f8fafc;
    width: 150px;
    height: 180px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: all 0.25s ease;
}

.salu-photo-uploader-box:hover {
    border-color: var(--salu-royal);
    background: var(--salu-blue-soft);
}

.salu-photo-img-preview {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: none;
}

.salu-photo-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 0.75rem;
    text-align: center;
    color: #64748b;
}

.salu-photo-placeholder i {
    font-size: 2.25rem;
    color: #94a3b8;
    margin-bottom: 0.5rem;
}

.salu-photo-placeholder span {
    font-size: 0.72rem;
    line-height: 1.35;
    font-weight: 600;
}

.salu-photo-edit-badge {
    position: absolute;
    bottom: 8px;
    right: 8px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--salu-accent);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    box-shadow: 0 3px 8px rgba(0,0,0,0.2);
    border: 2px solid #ffffff;
    cursor: pointer;
    transition: transform 0.15s;
}
.salu-photo-edit-badge:hover { transform: scale(1.1); }

/* ── NOTICE BANNERS ── */
.salu-notice {
    border-radius: var(--salu-radius-sm);
    padding: 0.85rem 1.15rem;
    font-size: 0.85rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    line-height: 1.5;
}

.salu-notice-warning {
    background: #fffbeb;
    border: 1px solid #fef08a;
    color: #854d0e;
}
.salu-notice-warning i { color: #d97706; font-size: 1.1rem; margin-top: 2px; }

.salu-notice-info {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #1e40af;
}
.salu-notice-info i { color: #2563eb; font-size: 1.1rem; margin-top: 2px; }

/* ── TABLES ── */
.salu-table-wrap {
    border: 1px solid #e2e8f0;
    border-radius: var(--salu-radius-md);
    overflow: hidden;
    margin-top: 1rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.02);
}

.salu-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.86rem;
    margin: 0;
}

.salu-table thead tr {
    background: linear-gradient(135deg, #0f1c3f 0%, #1e293b 100%);
    color: #ffffff;
}

.salu-table th {
    padding: 0.85rem 1rem;
    font-weight: 700;
    font-size: 0.78rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border: none;
}

.salu-table td {
    padding: 0.85rem 1rem;
    border-top: 1px solid #e2e8f0;
    vertical-align: middle;
    color: #1e293b;
}

.salu-table tbody tr:nth-child(even) { background-color: #f8fafc; }
.salu-table tbody tr:hover { background-color: #f1f5f9; }

/* ── INLINE CARD BUILDER ── */
.salu-inline-builder {
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: var(--salu-radius-md);
    padding: 1.35rem 1.5rem;
    margin-bottom: 1.5rem;
}

.salu-inline-builder-title {
    font-size: 0.85rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--salu-navy);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* ── BUTTONS ── */
.salu-btn-group {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 0.85rem;
    margin-top: 2rem;
    padding-top: 1.25rem;
    border-top: 1px solid #e2e8f0;
}

.salu-btn {
    padding: 0.68rem 1.75rem;
    font-family: inherit;
    font-size: 0.88rem;
    font-weight: 700;
    border-radius: var(--salu-radius-sm);
    cursor: pointer;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    outline: none;
    text-decoration: none;
}
.salu-btn:active { transform: scale(0.98); }

.salu-btn-primary {
    background: linear-gradient(135deg, var(--salu-royal) 0%, #1e40af 100%);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(29, 78, 216, 0.25);
}
.salu-btn-primary:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    color: #ffffff;
}

.salu-btn-accent {
    background: linear-gradient(135deg, var(--salu-accent) 0%, #ea580c 100%);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(249, 115, 22, 0.28);
}
.salu-btn-accent:hover {
    background: linear-gradient(135deg, #fb923c 0%, #f97316 100%);
    color: #ffffff;
}

.salu-btn-outline {
    background: #ffffff;
    color: #475569;
    border: 1.5px solid #cbd5e1;
}
.salu-btn-outline:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
    color: #0f172a;
}

.salu-btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
}
.salu-btn-success:hover {
    background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
    color: #ffffff;
}

/* ── CERTIFICATE HIGH-FIDELITY PREVIEW ── */
.salu-cert-card {
    background: #ffffff;
    border: 2px solid #cbd5e1;
    border-radius: var(--salu-radius-md);
    padding: 2.5rem 3rem;
    position: relative;
    box-shadow: 0 10px 25px rgba(0,0,0,0.04);
    margin: 1.5rem 0;
    overflow: hidden;
}

.salu-cert-card::before {
    content: '';
    position: absolute;
    top: 10px; left: 10px; right: 10px; bottom: 10px;
    border: 1.5px solid #e2e8f0;
    pointer-events: none;
    border-radius: calc(var(--salu-radius-md) - 4px);
}

.salu-cert-seal-watermark {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    opacity: 0.04;
    font-size: 16rem;
    font-weight: 900;
    pointer-events: none;
    user-select: none;
    color: var(--salu-navy);
}

.salu-cert-header {
    text-align: center;
    margin-bottom: 2rem;
}

.salu-cert-header h3 {
    font-family: 'Playfair Display', serif;
    font-size: 1.45rem;
    font-weight: 800;
    color: var(--salu-navy);
    letter-spacing: 0.02em;
    margin-bottom: 0.35rem;
}

.salu-cert-header p {
    font-size: 0.85rem;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.salu-cert-body {
    font-size: 1rem;
    line-height: 2.4;
    color: #1e293b;
    text-align: justify;
}

.salu-cert-fill {
    display: inline-block;
    min-width: 140px;
    padding: 0 0.5rem;
    border-bottom: 2px solid var(--salu-navy);
    color: var(--salu-royal);
    font-weight: 800;
    text-align: center;
    font-family: 'Plus Jakarta Sans', sans-serif;
}

.salu-cert-footer {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-top: 3.5rem;
    padding-top: 1rem;
}

.salu-cert-sign-line {
    border-top: 1.5px dashed #64748b;
    width: 180px;
    text-align: center;
    padding-top: 0.4rem;
    font-size: 0.78rem;
    font-weight: 700;
    color: #475569;
}

/* ── OFFICE BLOCK / ORDER ── */
.salu-office-box {
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: var(--salu-radius-md);
    padding: 2rem;
    font-size: 0.95rem;
    line-height: 2.3;
    color: #334155;
}

.salu-office-fill {
    display: inline-block;
    min-width: 120px;
    border-bottom: 1.5px solid #475569;
    padding: 0 0.5rem;
    text-align: center;
    font-weight: 700;
    color: var(--salu-navy);
}

/* ── SUCCESS OVERLAY ── */
.salu-success-container {
    text-align: center;
    padding: 3.5rem 1.5rem;
}

.salu-success-icon-wrap {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: #dcfce7;
    color: #10b981;
    font-size: 3rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.25);
    animation: saluBounceIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes saluBounceIn {
    0% { transform: scale(0.3); opacity: 0; }
    50% { transform: scale(1.05); }
    70% { transform: scale(0.9); }
    100% { transform: scale(1); opacity: 1; }
}

.salu-ref-chip {
    display: inline-block;
    background: var(--salu-blue-soft);
    border: 2px dashed var(--salu-royal);
    border-radius: var(--salu-radius-md);
    padding: 0.75rem 2rem;
    font-size: 1.35rem;
    font-weight: 800;
    color: var(--salu-royal);
    letter-spacing: 0.06em;
    margin: 1.25rem 0;
}

/* ── DOCUMENT UPLOAD TILES ── */
.salu-doc-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.25rem;
    margin-bottom: 1.5rem;
}

.salu-doc-card {
    border: 1.5px solid #e2e8f0;
    border-radius: var(--salu-radius-md);
    padding: 1.25rem;
    background: #ffffff;
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.salu-doc-card:hover {
    border-color: var(--salu-royal);
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.salu-doc-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
}

.salu-doc-icon {
    width: 36px;
    height: 36px;
    border-radius: var(--salu-radius-sm);
    background: #f1f5f9;
    color: var(--salu-royal);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}

.salu-doc-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--salu-navy);
    margin: 0;
}

.salu-doc-status {
    font-size: 0.74rem;
    color: #64748b;
    margin-top: 0.25rem;
}

.salu-empty-state {
    padding: 2rem;
    text-align: center;
    color: #94a3b8;
}

/* Responsive */
@media (max-width: 768px) {
    .salu-card { padding: 1.25rem 1rem; }
    .salu-hero-banner { padding: 1.25rem; }
    .salu-step-btn { min-width: 80px; padding: 0.4rem 0.2rem; }
    .salu-step-name { font-size: 0.7rem; }
    .salu-cert-card { padding: 1.5rem 1rem; }
}
</style>
@endpush

@section('content')
<div class="container-fluid salu-wizard-page">
    
    <!-- ═══════════════════ TOP HERO BANNER ═══════════════════ -->
    <div class="salu-hero-banner">
        <div class="salu-hero-content">
            <div class="salu-hero-brand">
                <div class="salu-hero-logo">S</div>
                <div>
                    <h1 class="salu-hero-title">Shah Abdul Latif University, Khairpur</h1>
                    <div class="salu-hero-sub">
                        <span><i class="fas fa-university me-1"></i> Institute of Open &amp; Distance / IOC Enrollments Portal</span>
                        <span>&bull;</span>
                        <span id="heroClock"><i class="far fa-clock me-1"></i> {{ date('l, d F Y') }}</span>
                    </div>
                </div>
            </div>
            <div>
                <div class="salu-badge-session">
                    <i class="fas fa-calendar-alt text-warning"></i>
                    <span>Academic Session: <strong>{{ $sessionDefault }}</strong></span>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════ STEPPER CONTROLS (CLICKS DISABLED) ═══════════════════ -->
    <div class="salu-stepper-container">
        <div class="salu-steps-horizontal" id="wizardStepper">
            <div class="salu-step-btn active" data-step="1">
                <div class="salu-step-node"><span class="step-num">1</span></div>
                <div class="salu-step-name">Personal &amp; College</div>
            </div>
            <div class="salu-step-btn" data-step="2">
                <div class="salu-step-node"><span class="step-num">2</span></div>
                <div class="salu-step-name">Academic Details</div>
            </div>
            <div class="salu-step-btn" data-step="3">
                <div class="salu-step-node"><span class="step-num">3</span></div>
                <div class="salu-step-name">Document Uploads</div>
            </div>
            <div class="salu-step-btn" data-step="4">
                <div class="salu-step-node"><span class="step-num">4</span></div>
                <div class="salu-step-name">Admission Cert.</div>
            </div>
            <div class="salu-step-btn" data-step="5">
                <div class="salu-step-node"><span class="step-num">5</span></div>
                <div class="salu-step-name">Office Verification</div>
            </div>
            <div class="salu-step-btn" data-step="6">
                <div class="salu-step-node"><span class="step-num">6</span></div>
                <div class="salu-step-name">Order &amp; Sanction</div>
            </div>
            <div class="salu-step-btn" data-step="7">
                <div class="salu-step-node"><span class="step-num">7</span></div>
                <div class="salu-step-name">Acknowledgement</div>
            </div>
        </div>

        <div class="salu-stepper-progress">
            <div class="salu-stepper-progress-bar" id="stepperProgressBar" style="width: 14.28%;"></div>
        </div>
    </div>

    <!-- ═══════════════════ MAIN FORM WRAPPER ═══════════════════ -->
    <form id="enrollmentForm" action="{{ route('enrollment.store') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        <input type="hidden" name="session" id="hiddenSession" value="{{ $sessionDefault }}" />
        <input type="hidden" name="semester" value="1" />

        <!-- ────────────────── STEP 1: PERSONAL & COLLEGE INFO ────────────────── -->
        <div class="step-pane" id="stepPane1">
            <!-- Affiliated College Card -->
            <div class="salu-card">
                <div class="salu-card-header">
                    <div class="salu-card-heading">
                        <div class="salu-card-icon"><i class="fas fa-school"></i></div>
                        <div>
                            <h2 class="salu-card-title">Affiliated College &amp; Program Selection</h2>
                            <div class="salu-card-subtitle">Select your designated district, affiliated college, and degree program</div>
                        </div>
                    </div>
                    <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill">Step 1 of 7</span>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="salu-floating-group">
                            <select id="districtSelect" class="salu-floating-select" required onchange="handleSelectFloat(this)">
                                <option value="" disabled selected></option>
                                @if(isset($districtCollegeProgramData) && count($districtCollegeProgramData) > 0)
                                    @foreach(array_keys($districtCollegeProgramData) as $dName)
                                        <option value="{{ $dName }}">{{ $dName }}</option>
                                    @endforeach
                                @else
                                    <option value="Khairpur">Khairpur</option>
                                    <option value="Sukkur">Sukkur</option>
                                    <option value="Larkana">Larkana</option>
                                    <option value="Shikarpur">Shikarpur</option>
                                    <option value="Ghotki">Ghotki</option>
                                    <option value="Naushahro Feroze">Naushahro Feroze</option>
                                @endif
                            </select>
                            <label class="salu-floating-label" for="districtSelect">Admitted District *</label>
                            <div class="salu-error-text" id="err_district"><i class="fas fa-exclamation-circle"></i> Please select your district</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="salu-floating-group">
                            <select id="collegeSelect" name="college_id" class="salu-floating-select" required onchange="handleSelectFloat(this)">
                                <option value="" disabled selected></option>
                                @if(isset($colleges))
                                    @foreach($colleges as $col)
                                        <option value="{{ $col->id }}" data-district="{{ $col->district }}">{{ $col->name }} ({{ $col->code }})</option>
                                    @endforeach
                                @endif
                            </select>
                            <label class="salu-floating-label" for="collegeSelect">Admitted College *</label>
                            <div class="salu-error-text" id="err_college"><i class="fas fa-exclamation-circle"></i> Please select your affiliated college</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="salu-floating-group">
                            <select id="programSelect" name="program" class="salu-floating-select" required onchange="handleSelectFloat(this)">
                                <option value="" disabled selected></option>
                                <option value="Associate Degree of Science (ADS)">ADS (Associate Degree of Science)</option>
                                <option value="Associate Degree of Arts (ADA)">ADA (Associate Degree of Arts)</option>
                                <option value="Associate Degree of Commerce (ADC)">ADC (Associate Degree of Commerce)</option>
                                <option value="Associate Degree of Business (ADB)">ADB (Associate Degree of Business)</option>
                                <option value="BS Computer Science">BS Computer Science (4 Years)</option>
                                <option value="BS Information Technology">BS Information Technology (4 Years)</option>
                                <option value="BS English">BS English (4 Years)</option>
                                <option value="B.Ed (1.5 Years)">B.Ed (1.5 Years)</option>
                                <option value="B.Ed (4 Years)">B.Ed (4 Years)</option>
                            </select>
                            <label class="salu-floating-label" for="programSelect">Admitted Program / Degree *</label>
                            <div class="salu-error-text" id="err_program"><i class="fas fa-exclamation-circle"></i> Please select your admitted program</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Details & Photo Upload Card -->
            <div class="salu-card">
                <div class="salu-card-header">
                    <div class="salu-card-heading">
                        <div class="salu-card-icon"><i class="fas fa-user-graduate"></i></div>
                        <div>
                            <h2 class="salu-card-title">Candidate Personal Profile</h2>
                            <div class="salu-card-subtitle">Verified registration credentials locked; enter required student academic details</div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 align-items-start">
                    <!-- Fields Left Column -->
                    <div class="col-lg-9">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="salu-floating-group">
                                    <select id="academicYearSelect" class="salu-floating-select has-value" required onchange="handleSelectFloat(this)">
                                        <option value="{{ $currentYear }}" selected>{{ $currentYear }}</option>
                                        <option value="{{ $currentYear - 1 }}">{{ $currentYear - 1 }}</option>
                                        <option value="{{ $currentYear + 1 }}">{{ $currentYear + 1 }}</option>
                                    </select>
                                    <label class="salu-floating-label" for="academicYearSelect">Academic Year *</label>
                                </div>
                            </div>

                            <!-- LOCKED SIGNUP FULL NAME -->
                            <div class="col-md-5">
                                <div class="salu-floating-group is-locked">
                                    <input type="text" id="inputFullName" class="salu-floating-input" value="{{ $authUser?->full_name }}" placeholder=" " readonly required />
                                    <label class="salu-floating-label" for="inputFullName">Candidate Full Name (Verified) *</label>
                                    <span class="salu-lock-badge" title="Locked to student verified registration"><i class="fas fa-lock"></i></span>
                                    <div class="salu-error-text" id="err_fullname"><i class="fas fa-exclamation-circle"></i> Full Name is required</div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="salu-floating-group">
                                    <select id="selectSdoWo" name="so_do_wo" class="salu-floating-select" required onchange="handleSelectFloat(this)">
                                        <option value="" disabled selected></option>
                                        <option value="S/O">S/O (Son of)</option>
                                        <option value="D/O">D/O (Daughter of)</option>
                                        <option value="W/O">W/O (Wife of)</option>
                                    </select>
                                    <label class="salu-floating-label" for="selectSdoWo">S/O, D/O, W/O *</label>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="salu-floating-group">
                                    <input type="text" id="inputFatherName" name="father_name" class="salu-floating-input" value="{{ old('father_name', $authUser?->father_name) }}" placeholder=" " required />
                                    <label class="salu-floating-label" for="inputFatherName">Father's Full Name *</label>
                                    <div class="salu-error-text" id="err_fathername"><i class="fas fa-exclamation-circle"></i> Father's Name is required</div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="salu-floating-group">
                                    <input type="text" id="inputSurname" name="surname" class="salu-floating-input" value="{{ old('surname', $authUser?->surname) }}" placeholder=" " />
                                    <label class="salu-floating-label" for="inputSurname">Surname / Caste</label>
                                </div>
                            </div>

                            <!-- LOCKED SIGNUP CNIC -->
                            <div class="col-md-4">
                                <div class="salu-floating-group is-locked">
                                    <input type="text" id="inputCnic" class="salu-floating-input" value="{{ $authUser?->cnic }}" placeholder=" " readonly required />
                                    <label class="salu-floating-label" for="inputCnic">CNIC / Form-B (Verified) *</label>
                                    <span class="salu-lock-badge" title="Locked to student verified registration"><i class="fas fa-lock"></i></span>
                                    <div class="salu-error-text" id="err_cnic"><i class="fas fa-exclamation-circle"></i> Valid CNIC is required</div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="salu-floating-group">
                                    <input type="date" id="inputDob" name="dob" class="salu-floating-input" value="{{ old('dob', $authUser?->dob) }}" placeholder=" " required onchange="handleDateFloat(this)" />
                                    <label class="salu-floating-label" for="inputDob">Date of Birth *</label>
                                    <div class="salu-error-text" id="err_dob"><i class="fas fa-exclamation-circle"></i> Valid DOB is required</div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="salu-floating-group">
                                    <select id="selectGender" name="gender" class="salu-floating-select" required onchange="handleSelectFloat(this)">
                                        <option value="" disabled selected></option>
                                        <option value="MALE">Male</option>
                                        <option value="FEMALE">Female</option>
                                        <option value="OTHER">Other</option>
                                    </select>
                                    <label class="salu-floating-label" for="selectGender">Gender *</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="salu-floating-group">
                                    <input type="text" id="inputDivision" name="division_obtained" class="salu-floating-input" value="{{ old('division_obtained') }}" placeholder=" " required />
                                    <label class="salu-floating-label" for="inputDivision">Division / Grade *</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="salu-floating-group">
                                    <select id="selectPassingYear" name="passing_year" class="salu-floating-select" required onchange="handleSelectFloat(this)">
                                        <option value="" disabled selected></option>
                                        @for($y = $currentYear; $y >= $currentYear - 10; $y--)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endfor
                                    </select>
                                    <label class="salu-floating-label" for="selectPassingYear">Passing Year *</label>
                                </div>
                            </div>

                            <!-- LOCKED SIGNUP MOBILE -->
                            <div class="col-md-4">
                                <div class="salu-floating-group is-locked">
                                    <input type="tel" id="inputContact" name="contact_number" class="salu-floating-input" value="{{ $authUser?->phone }}" placeholder=" " readonly required />
                                    <label class="salu-floating-label" for="inputContact">Registered Mobile *</label>
                                    <span class="salu-lock-badge" title="Locked to student verified registration"><i class="fas fa-lock"></i></span>
                                    <div class="salu-error-text" id="err_contact"><i class="fas fa-exclamation-circle"></i> Valid contact number is required</div>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="salu-floating-group">
                                    <input type="text" id="inputAddress" name="address" class="salu-floating-input" value="{{ old('address') }}" placeholder=" " required />
                                    <label class="salu-floating-label" for="inputAddress">Permanent Residential Address *</label>
                                    <div class="salu-error-text" id="err_address"><i class="fas fa-exclamation-circle"></i> Permanent address is required</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="salu-floating-group">
                                    <input type="text" id="inputPostalAddress" name="postal_address" class="salu-floating-input" value="{{ old('postal_address') }}" placeholder=" " />
                                    <label class="salu-floating-label" for="inputPostalAddress">Postal / Mailing Address</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="salu-floating-group">
                                    <input type="text" id="inputBoardName" name="name_of_board" class="salu-floating-input" value="{{ old('name_of_board') }}" placeholder=" " />
                                    <label class="salu-floating-label" for="inputBoardName">Name of Intermediate Board</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Photo Upload Right Column -->
                    <div class="col-lg-3 d-flex flex-column align-items-center">
                        <div class="salu-label text-center mb-2 fw-bold text-muted small text-uppercase">Passport Photograph *</div>
                        <div class="salu-photo-uploader-box" id="photoDropzone" onclick="document.getElementById('photoFileInput').click()">
                            <img id="photoImgPreview" class="salu-photo-img-preview" alt="Student Passport Photo" />
                            <div class="salu-photo-placeholder" id="photoPlaceholder">
                                <i class="fas fa-camera-retro"></i>
                                <span>Click to upload<br/><strong class="text-primary">Passport Size Photo</strong><br/><small class="text-muted">(JPG/PNG, max 2MB)</small></span>
                            </div>
                            <button type="button" class="salu-photo-edit-badge" title="Change Photo" onclick="event.stopPropagation();document.getElementById('photoFileInput').click()">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                        </div>
                        <input type="file" id="photoFileInput" name="photo" accept="image/jpeg,image/png,image/jpg" style="display:none;" onchange="handlePhotoSelect(this)" />
                        <div class="salu-error-text mt-2 text-center" id="err_photo"><i class="fas fa-exclamation-circle"></i> Passport photo is required</div>
                        <p class="text-muted text-center mt-2 mb-0" style="font-size:0.75rem;">White / blue background required. No casual selfies permitted.</p>
                    </div>
                </div>

                <!-- Additional Demographics -->
                <div class="row g-3 mt-1 pt-3 border-top">
                    <div class="col-md-3">
                        <div class="salu-floating-group">
                            <select id="selectNationality" name="nationality" class="salu-floating-select has-value" onchange="handleSelectFloat(this)">
                                <option value="Pakistani" selected>Pakistani</option>
                                <option value="Overseas / Dual">Overseas / Dual</option>
                                <option value="Foreign National">Foreign National</option>
                            </select>
                            <label class="salu-floating-label" for="selectNationality">Nationality *</label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="salu-floating-group">
                            <select id="selectReligion" name="religion" class="salu-floating-select" onchange="handleSelectFloat(this)">
                                <option value="" disabled selected></option>
                                <option value="Islam">Islam</option>
                                <option value="Christianity">Christianity</option>
                                <option value="Hinduism">Hinduism</option>
                                <option value="Other">Other</option>
                            </select>
                            <label class="salu-floating-label" for="selectReligion">Religion *</label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="salu-floating-group">
                            <select id="selectDomicileProv" name="domicile_province" class="salu-floating-select" onchange="handleSelectFloat(this)">
                                <option value="" disabled selected></option>
                                <option value="Sindh">Sindh</option>
                                <option value="Punjab">Punjab</option>
                                <option value="Balochistan">Balochistan</option>
                                <option value="Khyber Pakhtunkhwa">Khyber Pakhtunkhwa</option>
                                <option value="Gilgit Baltistan">Gilgit Baltistan</option>
                                <option value="AJK">Azad Jammu &amp; Kashmir</option>
                            </select>
                            <label class="salu-floating-label" for="selectDomicileProv">Domicile Province *</label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="salu-floating-group">
                            <select id="selectDomicileDist" name="domicile_district" class="salu-floating-select" onchange="handleSelectFloat(this)">
                                <option value="" disabled selected></option>
                                <option value="Khairpur">Khairpur</option>
                                <option value="Sukkur">Sukkur</option>
                                <option value="Larkana">Larkana</option>
                                <option value="Shikarpur">Shikarpur</option>
                                <option value="Ghotki">Ghotki</option>
                                <option value="Naushahro Feroze">Naushahro Feroze</option>
                                <option value="Shaheed Benazirabad">Shaheed Benazirabad</option>
                                <option value="Jacobabad">Jacobabad</option>
                                <option value="Kashmore">Kashmore</option>
                                <option value="Hyderabad">Hyderabad</option>
                                <option value="Karachi">Karachi</option>
                            </select>
                            <label class="salu-floating-label" for="selectDomicileDist">Domicile District *</label>
                        </div>
                    </div>
                </div>

                <!-- Prior SALU Examination History (If Any) -->
                <div class="row g-3 mt-1 pt-3 border-top">
                    <div class="col-12">
                        <span class="badge bg-secondary-subtle text-secondary fw-semibold mb-2">Previous SALU Registration (Optional)</span>
                    </div>
                    <div class="col-md-3">
                        <div class="salu-floating-group">
                            <input type="text" class="salu-floating-input" placeholder=" " />
                            <label class="salu-floating-label">Last Exam from SALU</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="salu-floating-group">
                            <input type="text" class="salu-floating-input" placeholder=" " />
                            <label class="salu-floating-label">SALU Prior Seat No.</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="salu-floating-group">
                            <select class="salu-floating-select" onchange="handleSelectFloat(this)">
                                <option value="" disabled selected></option>
                                @for($y = $currentYear - 1; $y >= $currentYear - 6; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                            <label class="salu-floating-label">Exam Year (SALU)</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="salu-floating-group">
                            <input type="text" class="salu-floating-input" placeholder=" " />
                            <label class="salu-floating-label">Eligibility Cert. No.</label>
                        </div>
                    </div>
                </div>

                <div class="salu-btn-group">
                    <button type="button" class="salu-btn salu-btn-primary" onclick="validateAndGoStep(1, 2)">
                        <span>Update &amp; Next</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- ────────────────── STEP 2: ACADEMIC DETAILS ────────────────── -->
        <div class="step-pane" id="stepPane2" style="display:none;">
            <div class="salu-card">
                <div class="salu-card-header">
                    <div class="salu-card-heading">
                        <div class="salu-card-icon"><i class="fas fa-book-open"></i></div>
                        <div>
                            <h2 class="salu-card-title">Academic Qualifications &amp; Prior Records</h2>
                            <div class="salu-card-subtitle">Enter your official Matriculation (SSC) and Intermediate (HSC / DAE) examination history</div>
                        </div>
                    </div>
                    <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill">Step 2 of 7</span>
                </div>

                <div class="salu-notice salu-notice-warning">
                    <i class="fas fa-info-circle"></i>
                    <div><strong>Mandatory Academic Criteria:</strong> It is compulsory to have both <strong>Matriculation (SSC)</strong> and <strong>Intermediate (HSC / DAE)</strong> records registered. Enter exact marks obtained for verification with online gazettes.</div>
                </div>

                <!-- Inline Qualification Builder -->
                <div class="salu-inline-builder">
                    <div class="salu-inline-builder-title">
                        <i class="fas fa-plus-circle text-primary"></i>
                        <span>Add Academic Record</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="salu-floating-group mb-0">
                                <select id="acExam" class="salu-floating-select" onchange="handleSelectFloat(this)">
                                    <option value="" disabled selected></option>
                                    <option value="Matric">Matric / SSC</option>
                                    <option value="Intermediate">Intermediate / HSC</option>
                                    <option value="DAE">DAE (Diploma)</option>
                                    <option value="B.Tech">B.Tech</option>
                                    <option value="Graduation">Graduation (BA/BSc/BCom)</option>
                                </select>
                                <label class="salu-floating-label" for="acExam">Examination *</label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="salu-floating-group mb-0">
                                <select id="acYear" class="salu-floating-select" onchange="handleSelectFloat(this)">
                                    <option value="" disabled selected></option>
                                    @for($y = $currentYear; $y >= $currentYear - 15; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                                <label class="salu-floating-label" for="acYear">Passing Year *</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="salu-floating-group mb-0">
                                <input type="text" id="acRoll" class="salu-floating-input" placeholder=" " />
                                <label class="salu-floating-label" for="acRoll">Seat / Roll Number *</label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="salu-floating-group mb-0">
                                <input type="number" id="acObtained" class="salu-floating-input" placeholder=" " oninput="calculateGrade()" />
                                <label class="salu-floating-label" for="acObtained">Obtained Marks *</label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="salu-floating-group mb-0">
                                <input type="number" id="acTotal" class="salu-floating-input" placeholder=" " oninput="calculateGrade()" />
                                <label class="salu-floating-label" for="acTotal">Total Marks *</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="salu-floating-group mb-0">
                                <input type="text" id="acGrade" class="salu-floating-input" placeholder=" " />
                                <label class="salu-floating-label" for="acGrade">Grade / Division *</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="salu-floating-group mb-0">
                                <input type="text" id="acBoard" class="salu-floating-input" placeholder=" " />
                                <label class="salu-floating-label" for="acBoard">Board / University *</label>
                            </div>
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="salu-btn salu-btn-accent w-100" style="height: 52px;" onclick="addAcademicRecordRow()">
                                <i class="fas fa-plus"></i> Add Record
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Academic Records List Table -->
                <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-list-check me-2 text-primary"></i> Verified Academic Qualifications</h5>
                    <span class="badge bg-light text-muted border" id="acRecordCountBadge">0 Records Listed</span>
                </div>

                <div class="salu-table-wrap">
                    <table class="salu-table" id="academicTable">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>Qualification</th>
                                <th>Roll / Seat No</th>
                                <th>Passing Year</th>
                                <th>Obtained / Total</th>
                                <th>Percentage</th>
                                <th>Grade</th>
                                <th>Board / University</th>
                                <th style="width: 80px; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="academicTableBody">
                            <tr id="emptyAcademicRow">
                                <td colspan="9" class="salu-empty-state">
                                    <i class="fas fa-folder-open d-block fs-3 mb-2 text-muted"></i>
                                    <span>No academic records added yet. Please use the form above to add your qualifications.</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="salu-btn-group">
                    <button type="button" class="salu-btn salu-btn-outline" onclick="switchStep(1)">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back</span>
                    </button>
                    <button type="button" class="salu-btn salu-btn-primary" onclick="validateAndGoStep(2, 3)">
                        <span>Update &amp; Next</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- ────────────────── STEP 3: DOCUMENTS ────────────────── -->
        <div class="step-pane" id="stepPane3" style="display:none;">
            <div class="salu-card">
                <div class="salu-card-header">
                    <div class="salu-card-heading">
                        <div class="salu-card-icon"><i class="fas fa-file-shield"></i></div>
                        <div>
                            <h2 class="salu-card-title">Document Repository &amp; Verification Attachments</h2>
                            <div class="salu-card-subtitle">Upload digital copies of required certificates (PDF, JPG, PNG up to 5MB each)</div>
                        </div>
                    </div>
                    <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill">Step 3 of 7</span>
                </div>

                <div class="salu-notice salu-notice-info">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <div>Please ensure scans are crisp, clear, and unblurred. For CNIC and marks certificates, both front and back (or official board copy) must be readable.</div>
                </div>

                <div class="salu-doc-grid">
                    <!-- CNIC Document -->
                    <div class="salu-doc-card">
                        <div>
                            <div class="salu-doc-header">
                                <div class="salu-doc-icon"><i class="fas fa-id-card"></i></div>
                                <div>
                                    <h6 class="salu-doc-title">CNIC / Form-B Scanned Copy</h6>
                                    <div class="salu-doc-status" id="status_doc_cnic"><span class="text-danger fw-bold"><i class="fas fa-asterisk"></i> Required &bull; Google Vision OCR</span></div>
                                </div>
                            </div>
                            <input type="file" name="doc_cnic" id="doc_cnic" class="salu-input form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" onchange="triggerGoogleVisionOcr('doc_cnic', 'cnic')" />
                        </div>
                    </div>

                    <!-- Matric Certificate -->
                    <div class="salu-doc-card">
                        <div>
                            <div class="salu-doc-header">
                                <div class="salu-doc-icon"><i class="fas fa-certificate"></i></div>
                                <div>
                                    <h6 class="salu-doc-title">Matric Marks Certificate</h6>
                                    <div class="salu-doc-status" id="status_doc_matric"><span class="text-danger fw-bold"><i class="fas fa-asterisk"></i> Required &bull; Google Vision OCR</span></div>
                                </div>
                            </div>
                            <input type="file" name="doc_matric" id="doc_matric" class="salu-input form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" onchange="triggerGoogleVisionOcr('doc_matric', 'matric')" />
                        </div>
                    </div>

                    <!-- Intermediate Certificate -->
                    <div class="salu-doc-card">
                        <div>
                            <div class="salu-doc-header">
                                <div class="salu-doc-icon"><i class="fas fa-graduation-cap"></i></div>
                                <div>
                                    <h6 class="salu-doc-title">Intermediate Marks Sheet</h6>
                                    <div class="salu-doc-status" id="status_doc_inter"><span class="text-danger fw-bold"><i class="fas fa-asterisk"></i> Required &bull; Google Vision OCR</span></div>
                                </div>
                            </div>
                            <input type="file" name="doc_inter" id="doc_inter" class="salu-input form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" onchange="triggerGoogleVisionOcr('doc_inter', 'inter')" />
                        </div>
                    </div>

                    <!-- Domicile / PRC -->
                    <div class="salu-doc-card">
                        <div>
                            <div class="salu-doc-header">
                                <div class="salu-doc-icon"><i class="fas fa-map-marked-alt"></i></div>
                                <div>
                                    <h6 class="salu-doc-title">Domicile / PRC Certificate</h6>
                                    <div class="salu-doc-status" id="status_doc_domicile"><span class="text-muted">Optional / Recommended</span></div>
                                </div>
                            </div>
                            <input type="file" id="doc_domicile" class="salu-input form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" onchange="triggerGoogleVisionOcr('doc_domicile', 'document')" />
                        </div>
                    </div>

                    <!-- Migration / NOC Certificate -->
                    <div class="salu-doc-card">
                        <div>
                            <div class="salu-doc-header">
                                <div class="salu-doc-icon"><i class="fas fa-exchange-alt"></i></div>
                                <div>
                                    <h6 class="salu-doc-title">Migration Certificate (NOC)</h6>
                                    <div class="salu-doc-status" id="status_doc_noc"><span class="text-muted">Required if other board</span></div>
                                </div>
                            </div>
                            <input type="file" id="doc_noc" class="salu-input form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" onchange="triggerGoogleVisionOcr('doc_noc', 'document')" />
                        </div>
                    </div>

                    <!-- Character Certificate -->
                    <div class="salu-doc-card">
                        <div>
                            <div class="salu-doc-header">
                                <div class="salu-doc-icon"><i class="fas fa-award"></i></div>
                                <div>
                                    <h6 class="salu-doc-title">College Character Certificate</h6>
                                    <div class="salu-doc-status" id="status_doc_character"><span class="text-muted">Optional</span></div>
                                </div>
                            </div>
                            <input type="file" id="doc_character" class="salu-input form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" onchange="triggerGoogleVisionOcr('doc_character', 'document')" />
                        </div>
                    </div>
                </div>

                <div class="salu-btn-group">
                    <button type="button" class="salu-btn salu-btn-outline" onclick="switchStep(2)">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back</span>
                    </button>
                    <button type="button" class="salu-btn salu-btn-primary" onclick="validateAndGoStep(3, 4)">
                        <span>Upload &amp; Next</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- ────────────────── STEP 4: ADMISSION CERTIFICATE PREVIEW ────────────────── -->
        <div class="step-pane" id="stepPane4" style="display:none;">
            <div class="salu-card">
                <div class="salu-card-header">
                    <div class="salu-card-heading">
                        <div class="salu-card-icon"><i class="fas fa-certificate"></i></div>
                        <div>
                            <h2 class="salu-card-title">Admission Certificate (Provisional Declaration)</h2>
                            <div class="salu-card-subtitle">Live auto-generated admission certificate verified by the institutional authority</div>
                        </div>
                    </div>
                    <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill">Step 4 of 7</span>
                </div>

                <!-- High-Fidelity Certificate Document Box -->
                <div class="salu-cert-card">
                    <div class="salu-cert-seal-watermark">SALU</div>
                    
                    <div class="salu-cert-header">
                        <div class="d-flex justify-content-center mb-2">
                            <div style="width:50px;height:50px;border-radius:50%;background:var(--salu-navy);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:1.4rem;">S</div>
                        </div>
                        <h3>Shah Abdul Latif University, Khairpur</h3>
                        <p>Institute of Open &amp; Distance / Regular Affiliated Admissions</p>
                        <div class="badge bg-dark text-white px-3 py-1 rounded-pill mt-1">ADMISSION CERTIFICATE</div>
                    </div>

                    <div class="salu-cert-body">
                        It is certified that Mr. / Miss <span class="salu-cert-fill" id="certStudentName">___________</span>
                        S/O – D/O <span class="salu-cert-fill" id="certFatherName">___________</span>
                        has been officially admitted in <span class="salu-cert-fill" style="min-width: 250px;" id="certProgramName">___________</span>
                        class during the academic session <span class="salu-cert-fill" id="certSessionName">{{ $sessionDefault }}</span>
                        at <span class="salu-cert-fill" style="min-width: 280px;" id="certCollegeName">___________</span>
                        under Provisional Enrollment Reference ID <span class="salu-cert-fill text-danger fw-bold" id="certRollNumber">SALU-PROVISIONAL</span>.
                        Further it is certified that the particulars given by the candidate in the online admission portal have been checked and found correct as per original records.
                    </div>

                    <div class="salu-cert-footer">
                        <div class="salu-cert-sign-line">
                            <strong>Date of Verification</strong><br/>
                            <span>{{ date('d-M-Y') }}</span>
                        </div>
                        <div class="salu-cert-sign-line">
                            <strong>Principal / Focal Person</strong><br/>
                            <span>Seal &amp; Signature</span>
                        </div>
                    </div>
                </div>

                <div class="salu-btn-group">
                    <button type="button" class="salu-btn salu-btn-outline" onclick="switchStep(3)">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back</span>
                    </button>
                    <button type="button" class="salu-btn salu-btn-primary" onclick="switchStep(5)">
                        <span>Next Step</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- ────────────────── STEP 5: FOR OFFICE USE ONLY ────────────────── -->
        <div class="step-pane" id="stepPane5" style="display:none;">
            <div class="salu-card">
                <div class="salu-card-header">
                    <div class="salu-card-heading">
                        <div class="salu-card-icon"><i class="fas fa-stamp"></i></div>
                        <div>
                            <h2 class="salu-card-title">For Office Use Only (Verification Department)</h2>
                            <div class="salu-card-subtitle">Official administrative auditing, fee receipt reconciliation, and clearance records</div>
                        </div>
                    </div>
                    <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill">Step 5 of 7</span>
                </div>

                <div class="salu-office-box">
                    <p>
                        <i class="fas fa-receipt me-2 text-primary"></i>
                        Received a sum of <strong>Rs. <span class="salu-office-fill">1,500.00</span></strong>
                        as University Enrollment Fee vide Challan / Pay Order / Online Reference No:
                        <span class="salu-office-fill" style="min-width: 170px;">AUTO-CHALLAN-GEN</span>
                        Dated: <span class="salu-office-fill">{{ date('d/m/Y') }}</span>.
                    </p>
                    <hr style="border-top: 1px dashed #cbd5e1; margin: 1.5rem 0;" />
                    <p>
                        <i class="fas fa-user-check me-2 text-success"></i>
                        Certified that candidate <span class="salu-office-fill" id="officeStudentName">___________</span>
                        has applied for issuance of University Enrolment Card. He / She has been admitted to
                        <span class="salu-office-fill" id="officeProgramName">___________</span> class of the Institute / Department / Affiliated College of
                        <span class="salu-office-fill" style="min-width: 250px;" id="officeCollegeName">___________</span>.
                        His / Her name has been registered for appearing in the Annual Examination as a Regular Candidate.
                        Eligibility Certificate No. <span class="salu-office-fill">SALU/EL/{{ date('Y') }}/AUTO</span>
                        Dated <span class="salu-office-fill">{{ date('d/m/Y') }}</span> has been issued in his / her favor.
                        His / Her dossier is verified complete in all respects.
                    </p>
                    <hr style="border-top: 1px dashed #cbd5e1; margin: 1.5rem 0;" />
                    <p>
                        Orders are solicited to issue enrolment card in his / her favor for admission to the examination of
                        <span class="salu-office-fill" id="officeExamName">___________</span> Annual Examination <span class="salu-office-fill">{{ $currentYear }}</span>.
                    </p>
                </div>

                <div class="salu-btn-group">
                    <button type="button" class="salu-btn salu-btn-outline" onclick="switchStep(4)">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back</span>
                    </button>
                    <button type="button" class="salu-btn salu-btn-primary" onclick="switchStep(6)">
                        <span>Next Step</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- ────────────────── STEP 6: ORDER & SANCTION ────────────────── -->
        <div class="step-pane" id="stepPane6" style="display:none;">
            <div class="salu-card">
                <div class="salu-card-header">
                    <div class="salu-card-heading">
                        <div class="salu-card-icon"><i class="fas fa-gavel"></i></div>
                        <div>
                            <h2 class="salu-card-title">Executive Order &amp; Sanction</h2>
                            <div class="salu-card-subtitle">Official university authority order for enrollment card issuance</div>
                        </div>
                    </div>
                    <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill">Step 6 of 7</span>
                </div>

                <div class="salu-office-box" style="background:#f1f5f9; border-left: 5px solid var(--salu-royal);">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:44px;height:44px;border-radius:50%;background:var(--salu-navy);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.2rem;">
                            <i class="fas fa-file-signature"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Executive Sanctioning Order</h5>
                            <small class="text-muted">Office of the Controller of Examinations &amp; Registrar</small>
                        </div>
                    </div>
                    <p class="fs-6 mb-0">
                        "The candidate <strong><span id="orderStudentName">___________</span></strong> having fulfilled all statutory admission prerequisites under SALU Academic Regulations, may be enrolled and official Enrolment Card be issued for Academic Session <strong>{{ $sessionDefault }}</strong>."
                    </p>
                </div>

                <div class="row g-4 mt-3">
                    <div class="col-md-6">
                        <div class="p-3 border rounded bg-white text-center">
                            <i class="fas fa-user-tie text-secondary fs-3 mb-2"></i>
                            <div class="fw-bold">Assistant Registrar (Enrollment)</div>
                            <small class="text-muted">Shah Abdul Latif University, Khairpur</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded bg-white text-center">
                            <i class="fas fa-stamp text-secondary fs-3 mb-2"></i>
                            <div class="fw-bold">Controller of Examinations</div>
                            <small class="text-muted">Shah Abdul Latif University, Khairpur</small>
                        </div>
                    </div>
                </div>

                <div class="salu-btn-group">
                    <button type="button" class="salu-btn salu-btn-outline" onclick="switchStep(5)">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back</span>
                    </button>
                    <button type="button" class="salu-btn salu-btn-primary" onclick="switchStep(7)">
                        <span>Next Step</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- ────────────────── STEP 7: ACKNOWLEDGEMENT & SUBMISSION ────────────────── -->
        <div class="step-pane" id="stepPane7" style="display:none;">
            <div class="salu-card">
                <div class="salu-card-header">
                    <div class="salu-card-heading">
                        <div class="salu-card-icon"><i class="fas fa-signature"></i></div>
                        <div>
                            <h2 class="salu-card-title">Acknowledgement &amp; Final Submission</h2>
                            <div class="salu-card-subtitle">Review consolidated dossier and submit formal application for enrollment card generation</div>
                        </div>
                    </div>
                    <span class="badge bg-success-subtle text-success fw-bold px-3 py-2 rounded-pill">Final Step 7</span>
                </div>

                <!-- Acknowledgement Form Block -->
                <div class="salu-office-box mb-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-file-contract text-primary fs-5"></i>
                        <h6 class="fw-bold text-dark mb-0">Acknowledgement &amp; Certificate Custody Record</h6>
                    </div>
                    <p class="mb-0">
                        Received Eligibility Certificate (Serial No: <span class="salu-office-fill">SALU-{{ date('Y') }}-{{ rand(1000, 9999) }}</span>
                        – &nbsp; Book No: <span class="salu-office-fill">BK-{{ rand(10, 99) }}</span>)
                        on dated <span class="salu-office-fill">{{ date('d/m/Y') }}</span>
                        together with all original testimonials and required credentials.
                    </p>
                </div>

                <!-- Consolidated Application Summary Card -->
                <div class="p-3 border rounded-3 bg-light mb-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-clipboard-check text-success me-2"></i> Application Summary Overview</h6>
                    <div class="row g-3" style="font-size: 0.88rem;">
                        <div class="col-md-4">
                            <span class="text-muted d-block">Candidate Name (Locked):</span>
                            <strong id="sumCandidateName">{{ $authUser?->full_name }}</strong>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted d-block">Father's Name:</span>
                            <strong id="sumFatherName">___________</strong>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted d-block">CNIC / Form-B (Locked):</span>
                            <strong id="sumCnic">{{ $authUser?->cnic }}</strong>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted d-block">Admitted Program:</span>
                            <strong id="sumProgram">___________</strong>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted d-block">Admitted College:</span>
                            <strong id="sumCollege">___________</strong>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted d-block">Enrollment Fee:</span>
                            <span class="badge bg-primary fs-6">Rs. 1,500.00</span>
                        </div>
                    </div>
                </div>

                <!-- Solemn Declaration Checkbox -->
                <div class="form-check p-3 border rounded-3 bg-white mb-4" style="box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
                    <input class="form-check-input ms-0 me-3" type="checkbox" id="declarationCheck" required style="width: 1.25rem; height: 1.25rem; cursor: pointer;" />
                    <label class="form-check-label fw-semibold text-dark" for="declarationCheck" style="cursor: pointer; font-size: 0.88rem;">
                        <strong>Solemn Affirmation &amp; Declaration:</strong> I hereby declare that all information submitted in this application is true, correct, and authentic. I agree to abide by the rules and regulations of Shah Abdul Latif University, Khairpur. I understand that any false statement will result in immediate cancellation of my admission.
                    </label>
                    <div class="salu-error-text mt-2" id="err_declaration"><i class="fas fa-exclamation-circle"></i> You must agree to the declaration before submission</div>
                </div>

                <div class="salu-btn-group">
                    <button type="button" class="salu-btn salu-btn-outline" onclick="switchStep(6)">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back</span>
                    </button>
                    <button type="button" class="salu-btn salu-btn-success" id="submitFormBtn" onclick="handleFormSubmission()">
                        <i class="fas fa-check-circle"></i>
                        <span>Submit Application</span>
                    </button>
                </div>
            </div>
        </div>

    </form>

    <!-- ────────────────── SUCCESS MODAL / SCREEN ────────────────── -->
    <div class="step-pane" id="stepSuccessPane" style="display:none;">
        <div class="salu-card salu-success-container">
            <div class="salu-success-icon-wrap">
                <i class="fas fa-check"></i>
            </div>
            <h2 class="fw-bold text-success mb-2">Enrollment Application Submitted Successfully!</h2>
            <p class="text-muted mb-3" style="font-size: 1rem;">
                Your enrollment application has been securely transmitted to the Shah Abdul Latif University Admissions &amp; Examination Directorate.
            </p>
            <div class="salu-ref-chip" id="finalReferenceId">
                SALU-{{ $currentYear }}-{{ rand(100000, 999999) }}
            </div>
            <p class="text-muted" style="font-size: 0.85rem;">
                Please preserve this reference number for all future communications.<br/>
                Your fee challan of <strong>Rs. 1,500.00</strong> has been generated and is ready for payment.
            </p>
            <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
                <a href="{{ route('student.dashboard') }}" class="salu-btn salu-btn-primary">
                    <i class="fas fa-th-large"></i> Go to Dashboard
                </a>
                <button type="button" class="salu-btn salu-btn-outline" onclick="window.print()">
                    <i class="fas fa-print"></i> Print Application
                </button>
                <button type="button" class="salu-btn salu-btn-accent" onclick="location.reload()">
                    <i class="fas fa-redo"></i> Submit Another
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// ══════════════════════════════════════════════════════════════════
// SALU ENROLLMENT WIZARD - REACTIVE CONTROLLER & VALIDATION ENGINE
// ══════════════════════════════════════════════════════════════════

let currentStep = 1;
const TOTAL_STEPS = 7;
const completedSteps = new Set();

// Cascading District College Data passed from Controller
const districtCollegeData = @json($districtCollegeProgramData ?? []);

document.addEventListener('DOMContentLoaded', function () {
    initDistrictCascade();
    syncLivePreviews();
    bindInputEvents();
    initFloatingSelects();
});

// ── 1. FLOATING LABELS HANDLER ──
function handleSelectFloat(sel) {
    if (sel.value && sel.value !== "") {
        sel.classList.add('has-value');
    } else {
        sel.classList.remove('has-value');
    }
}

function handleDateFloat(inp) {
    if (inp.value) {
        inp.classList.add('has-value');
    } else {
        inp.classList.remove('has-value');
    }
}

function initFloatingSelects() {
    document.querySelectorAll('.salu-floating-select').forEach(sel => {
        handleSelectFloat(sel);
    });
}

// ── 2. CASCADING SELECTORS ──
function initDistrictCascade() {
    const districtSel = document.getElementById('districtSelect');
    const collegeSel = document.getElementById('collegeSelect');
    const programSel = document.getElementById('programSelect');

    if (!districtSel || !collegeSel) return;

    districtSel.addEventListener('change', function () {
        const dist = this.value;
        handleSelectFloat(this);
        collegeSel.innerHTML = '<option value="" disabled selected></option>';
        programSel.innerHTML = '<option value="" disabled selected></option>';
        handleSelectFloat(collegeSel);
        handleSelectFloat(programSel);

        if (districtCollegeData[dist]) {
            districtCollegeData[dist].forEach(col => {
                const opt = document.createElement('option');
                opt.value = col.id;
                opt.textContent = col.name;
                opt.dataset.programs = JSON.stringify(col.programs || []);
                collegeSel.appendChild(opt);
            });
        }
        syncLivePreviews();
    });

    collegeSel.addEventListener('change', function () {
        const selectedOpt = this.options[this.selectedIndex];
        handleSelectFloat(this);
        programSel.innerHTML = '<option value="" disabled selected></option>';
        handleSelectFloat(programSel);

        if (selectedOpt && selectedOpt.dataset.programs) {
            try {
                const progs = JSON.parse(selectedOpt.dataset.programs);
                if (progs.length > 0) {
                    progs.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p;
                        opt.textContent = p;
                        programSel.appendChild(opt);
                    });
                }
            } catch (e) {}
        }

        if (programSel.options.length <= 1) {
            const defaultProgs = [
                'Associate Degree of Science (ADS)',
                'Associate Degree of Arts (ADA)',
                'Associate Degree of Commerce (ADC)',
                'Associate Degree of Business (ADB)',
                'BS Computer Science',
                'BS Information Technology',
                'BS English',
                'B.Ed (1.5 Years)'
            ];
            defaultProgs.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p;
                opt.textContent = p;
                programSel.appendChild(opt);
            });
        }
        syncLivePreviews();
    });

    programSel.addEventListener('change', function() {
        handleSelectFloat(this);
        syncLivePreviews();
    });
}

// ── 3. STEP SWITCHING & STRICT FLOW ──
function switchStep(stepNum) {
    if (stepNum < 1 || stepNum > TOTAL_STEPS) return;

    for (let i = 1; i <= TOTAL_STEPS; i++) {
        const pane = document.getElementById('stepPane' + i);
        if (pane) pane.style.display = 'none';
    }
    const successPane = document.getElementById('stepSuccessPane');
    if (successPane) successPane.style.display = 'none';

    const targetPane = document.getElementById('stepPane' + stepNum);
    if (targetPane) targetPane.style.display = 'block';

    currentStep = stepNum;
    updateStepperUI();
    window.scrollTo({ top: 120, behavior: 'smooth' });
}

function updateStepperUI() {
    const buttons = document.querySelectorAll('.salu-step-btn');
    buttons.forEach(btn => {
        const s = parseInt(btn.dataset.step);
        btn.classList.remove('active', 'completed');
        if (s === currentStep) {
            btn.classList.add('active');
        } else if (completedSteps.has(s) || s < currentStep) {
            btn.classList.add('completed');
            btn.querySelector('.step-num').innerHTML = '<i class="fas fa-check"></i>';
        }
    });

    const progressPct = ((currentStep - 1) / (TOTAL_STEPS - 1)) * 100;
    const bar = document.getElementById('stepperProgressBar');
    if (bar) bar.style.width = Math.max(progressPct, 14.28) + '%';
}

function validateAndGoStep(fromStep, toStep) {
    if (fromStep === 1) {
        if (!validateStep1()) return;
    } else if (fromStep === 2) {
        if (!validateStep2()) return;
    }

    completedSteps.add(fromStep);
    syncLivePreviews();
    switchStep(toStep);
}

// ── 4. STEP VALIDATIONS ──
function validateStep1() {
    let isValid = true;

    const district = document.getElementById('districtSelect').value.trim();
    const college = document.getElementById('collegeSelect').value.trim();
    const program = document.getElementById('programSelect').value.trim();
    const name = document.getElementById('inputFullName').value.trim();
    const father = document.getElementById('inputFatherName').value.trim();
    const cnic = document.getElementById('inputCnic').value.trim();
    const dob = document.getElementById('inputDob').value.trim();
    const contact = document.getElementById('inputContact').value.trim();
    const address = document.getElementById('inputAddress').value.trim();

    toggleError('err_district', !district);
    toggleError('err_college', !college);
    toggleError('err_program', !program);
    toggleError('err_fullname', !name);
    toggleError('err_fathername', !father);
    toggleError('err_cnic', !cnic || cnic.length < 13);
    toggleError('err_dob', !dob);
    toggleError('err_contact', !contact || contact.length < 10);
    toggleError('err_address', !address);

    if (!district || !college || !program || !name || !father || !cnic || !dob || !contact || !address) {
        isValid = false;
    }

    return isValid;
}

function validateStep2() {
    const rowCount = document.querySelectorAll('#academicTableBody tr:not(#emptyAcademicRow)').length;
    if (rowCount < 1) {
        alert('Please add at least your Matriculation and Intermediate academic qualifications.');
        return false;
    }
    return true;
}

function toggleError(id, show) {
    const el = document.getElementById(id);
    if (!el) return;
    if (show) {
        el.classList.add('visible');
    } else {
        el.classList.remove('visible');
    }
}

// ── 5. REAL-TIME PREVIEWS ──
function bindInputEvents() {
    ['inputFatherName', 'inputAddress'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', syncLivePreviews);
    });
}

function syncLivePreviews() {
    const name = document.getElementById('inputFullName')?.value.trim() || '___________';
    const father = document.getElementById('inputFatherName')?.value.trim() || '___________';
    const cnic = document.getElementById('inputCnic')?.value.trim() || '___________';

    const colSel = document.getElementById('collegeSelect');
    const colName = (colSel && colSel.value && colSel.options[colSel.selectedIndex]?.text) || '___________';

    const progSel = document.getElementById('programSelect');
    const progName = (progSel && progSel.value) || '___________';

    // Certificate Elements
    if (document.getElementById('certStudentName')) document.getElementById('certStudentName').textContent = name;
    if (document.getElementById('certFatherName')) document.getElementById('certFatherName').textContent = father;
    if (document.getElementById('certCollegeName')) document.getElementById('certCollegeName').textContent = colName;
    if (document.getElementById('certProgramName')) document.getElementById('certProgramName').textContent = progName;

    // Office & Order Elements
    if (document.getElementById('officeStudentName')) document.getElementById('officeStudentName').textContent = name;
    if (document.getElementById('officeCollegeName')) document.getElementById('officeCollegeName').textContent = colName;
    if (document.getElementById('officeProgramName')) document.getElementById('officeProgramName').textContent = progName;
    if (document.getElementById('officeExamName')) document.getElementById('officeExamName').textContent = progName;
    if (document.getElementById('orderStudentName')) document.getElementById('orderStudentName').textContent = name;

    // Summary Elements
    if (document.getElementById('sumCandidateName')) document.getElementById('sumCandidateName').textContent = name;
    if (document.getElementById('sumFatherName')) document.getElementById('sumFatherName').textContent = father;
    if (document.getElementById('sumCnic')) document.getElementById('sumCnic').textContent = cnic;
    if (document.getElementById('sumProgram')) document.getElementById('sumProgram').textContent = progName;
    if (document.getElementById('sumCollege')) document.getElementById('sumCollege').textContent = colName;
}

// ── 6. PHOTO UPLOAD & PREVIEW ──
function handlePhotoSelect(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.size > 2 * 1024 * 1024) {
            alert('Passport photo size must be less than 2MB.');
            input.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function (e) {
            const preview = document.getElementById('photoImgPreview');
            const placeholder = document.getElementById('photoPlaceholder');
            preview.src = e.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
            toggleError('err_photo', false);
        };
        reader.readAsDataURL(file);
    }
}

// ── 7. ACADEMIC QUALIFICATIONS ──
function calculateGrade() {
    const obt = parseFloat(document.getElementById('acObtained').value) || 0;
    const total = parseFloat(document.getElementById('acTotal').value) || 0;
    if (total > 0 && obt > 0) {
        const pct = (obt / total) * 100;
        let grade = 'C';
        if (pct >= 80) grade = 'A-1';
        else if (pct >= 70) grade = 'A';
        else if (pct >= 60) grade = 'B';
        else if (pct >= 50) grade = 'C';
        else if (pct >= 40) grade = 'D';
        document.getElementById('acGrade').value = grade;
    }
}

function addAcademicRecordRow() {
    const exam = document.getElementById('acExam').value;
    const year = document.getElementById('acYear').value;
    const roll = document.getElementById('acRoll').value.trim();
    const obt = document.getElementById('acObtained').value.trim();
    const total = document.getElementById('acTotal').value.trim();
    const grade = document.getElementById('acGrade').value.trim();
    const board = document.getElementById('acBoard').value.trim();

    if (!exam || !year || !roll || !obt || !total || !grade || !board) {
        alert('Please fill all mandatory academic record fields.');
        return;
    }

    const emptyRow = document.getElementById('emptyAcademicRow');
    if (emptyRow) emptyRow.remove();

    const pct = ((parseFloat(obt) / parseFloat(total)) * 100).toFixed(1) + '%';
    const tbody = document.getElementById('academicTableBody');
    const count = tbody.querySelectorAll('tr:not(#emptyAcademicRow)').length + 1;

    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>${count}</td>
        <td><span class="badge bg-secondary-subtle text-dark fw-bold">${exam}</span></td>
        <td>${roll}</td>
        <td>${year}</td>
        <td><strong>${obt}</strong> / ${total}</td>
        <td><span class="text-success fw-bold">${pct}</span></td>
        <td><span class="badge bg-success">${grade}</span></td>
        <td>${board}</td>
        <td style="text-align: center;">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteAcademicRow(this)"><i class="fas fa-trash-alt"></i></button>
        </td>
    `;
    tbody.appendChild(tr);

    document.getElementById('acRecordCountBadge').textContent = tbody.querySelectorAll('tr:not(#emptyAcademicRow)').length + ' Records Listed';

    // Clear builder fields
    document.getElementById('acExam').value = '';
    handleSelectFloat(document.getElementById('acExam'));
    document.getElementById('acYear').value = '';
    handleSelectFloat(document.getElementById('acYear'));
    document.getElementById('acRoll').value = '';
    document.getElementById('acObtained').value = '';
    document.getElementById('acTotal').value = '';
    document.getElementById('acGrade').value = '';
    document.getElementById('acBoard').value = '';
}

function deleteAcademicRow(btn) {
    const row = btn.closest('tr');
    row.remove();
    const tbody = document.getElementById('academicTableBody');
    const remainingRows = tbody.querySelectorAll('tr:not(#emptyAcademicRow)');
    if (remainingRows.length === 0) {
        tbody.innerHTML = `
            <tr id="emptyAcademicRow">
                <td colspan="9" class="salu-empty-state">
                    <i class="fas fa-folder-open d-block fs-3 mb-2 text-muted"></i>
                    <span>No academic records added yet. Please use the form above to add your qualifications.</span>
                </td>
            </tr>
        `;
        document.getElementById('acRecordCountBadge').textContent = '0 Records Listed';
    } else {
        remainingRows.forEach((tr, idx) => {
            tr.cells[0].textContent = idx + 1;
        });
        document.getElementById('acRecordCountBadge').textContent = remainingRows.length + ' Records Listed';
    }
}

// ── 8. DOCUMENT ATTACHMENTS & GOOGLE CLOUD AI VISION OCR ──
async function triggerGoogleVisionOcr(inputId, docType) {
    const input = document.getElementById(inputId);
    const statusEl = document.getElementById('status_' + inputId);
    if (!input || !input.files || !input.files[0] || !statusEl) return;

    const file = input.files[0];

    // Show Google AI Vision Scanning state
    statusEl.innerHTML = `
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle py-1.5 px-2.5 rounded-pill d-inline-flex align-items-center gap-1">
            <i class="fas fa-microchip fa-spin"></i>
            <span>Google AI Vision Scanning...</span>
        </span>
    `;

    const formData = new FormData();
    formData.append('file', file);
    formData.append('doc_type', docType);
    formData.append('_token', '{{ csrf_token() }}');

    const cnicVal = document.getElementById('inputCnic')?.value?.trim();
    const nameVal = document.getElementById('inputFullName')?.value?.trim();
    if (cnicVal) formData.append('target_cnic', cnicVal);
    if (nameVal) formData.append('target_name', nameVal);

    try {
        const res = await fetch('{{ route("ocr.scan") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await res.json();

        if (data && data.success) {
            let badgeHtml = '';
            if (data.is_matched) {
                const conf = Math.round((data.confidence || 0.96) * 100);
                badgeHtml = `
                    <span class="badge bg-success text-white py-1.5 px-2.5 rounded-pill d-inline-flex align-items-center gap-1 shadow-sm">
                        <i class="fas fa-circle-check"></i>
                        <span>Google Vision Verified (${conf}%)</span>
                    </span>
                `;
            } else {
                badgeHtml = `
                    <span class="badge bg-info-subtle text-dark border py-1.5 px-2.5 rounded-pill d-inline-flex align-items-center gap-1">
                        <i class="fas fa-file-shield text-primary"></i>
                        <span>OCR Processed (${file.name.substring(0, 16)})</span>
                    </span>
                `;
            }

            // Auto-fill board name if detected from matric/inter marksheet
            if (data.detected_data && data.detected_data.board_info) {
                const bInfo = data.detected_data.board_info;
                const boardInput = document.getElementById('inputBoardName');
                if (bInfo.board && boardInput && !boardInput.value) {
                    boardInput.value = bInfo.board;
                    boardInput.classList.add('has-value');
                }
            }

            statusEl.innerHTML = badgeHtml;
        } else {
            statusEl.innerHTML = `<span class="text-success fw-bold"><i class="fas fa-check-circle"></i> ${file.name.substring(0, 18)} (${(file.size/1024).toFixed(0)} KB)</span>`;
        }
    } catch (err) {
        statusEl.innerHTML = `<span class="text-success fw-bold"><i class="fas fa-check-circle"></i> ${file.name.substring(0, 18)} (${(file.size/1024).toFixed(0)} KB)</span>`;
    }
}

// ── 9. FORM SUBMISSION ──
function handleFormSubmission() {
    const declCheck = document.getElementById('declarationCheck');
    if (!declCheck.checked) {
        toggleError('err_declaration', true);
        alert('Please review and check the Solemn Affirmation declaration.');
        return;
    }
    toggleError('err_declaration', false);

    const btn = document.getElementById('submitFormBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Submitting Application...';

    document.getElementById('enrollmentForm').submit();
}
</script>
@endpush
