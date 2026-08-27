@extends('layouts.app')

@section('title', 'My Enrollment Applications')

@php
    $title = 'Enrollment Applications';
@endphp

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">
            <i class="fas fa-file-signature text-primary me-2"></i>My Enrollment Applications
        </h4>
        <p class="text-muted small mb-0">Track verification status, assigned roll numbers, and download official documents</p>
    </div>
    <a href="{{ route('enrollment.create') }}" class="btn salu-btn-pill-green">
        <i class="fas fa-plus me-1"></i> New Enrollment Application
    </a>
</div>

@if($enrollments->isEmpty())
    <div class="card salu-overview-card p-5 text-center">
        <div class="salu-service-icon-box salu-icon-orange mx-auto mb-3">
            <i class="fas fa-folder-open"></i>
        </div>
        <h5 class="fw-bold text-dark mb-2">No Enrollment Applications Found</h5>
        <p class="text-muted small mb-4">You have not submitted an enrollment application yet for the active academic session.</p>
        <div>
            <a href="{{ route('enrollment.create') }}" class="btn salu-btn-pill-blue">
                <i class="fas fa-pen-to-square me-1"></i> Apply for Enrollment Now
            </a>
        </div>
    </div>
@else
    <div class="row g-4">
        @foreach($enrollments as $enrollment)
            @php
                $statusBadge = match($enrollment->status) {
                    'APPROVED' => 'bg-success text-white',
                    'REJECTED' => 'bg-danger text-white',
                    'PENDING' => 'bg-warning text-dark',
                    default => 'bg-secondary text-white'
                };
                $latestFee = $enrollment->fees->sortByDesc('created_at')->first();
            @endphp
            <div class="col-12">
                <div class="card salu-overview-card">
                    <div class="card-header salu-overview-header d-flex flex-wrap justify-content-between align-items-center py-3 px-4 gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge {{ $statusBadge }} px-3 py-2 rounded-pill fw-bold fs-6">
                                <i class="fas fa-circle-dot me-1"></i> {{ $enrollment->status }}
                            </span>
                            <span class="text-white fw-bold fs-6">
                                Application #{{ strtoupper(substr($enrollment->id, 0, 8)) }}
                            </span>
                        </div>
                        <span class="text-white-50 small">
                            Submitted on {{ $enrollment->created_at->format('d M Y, h:i A') }}
                        </span>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6 col-lg-3">
                                <div class="salu-metric-box">
                                    <span class="salu-metric-label">PROGRAM</span>
                                    <strong class="salu-metric-value">{{ $enrollment->program }}</strong>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="salu-metric-box">
                                    <span class="salu-metric-label">ACADEMIC SESSION</span>
                                    <strong class="salu-metric-value">{{ $enrollment->session }} ({{ $enrollment->semester }})</strong>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="salu-metric-box">
                                    <span class="salu-metric-label">AFFILIATED COLLEGE / DEPT</span>
                                    <strong class="salu-metric-value">{{ $enrollment->college->name ?? 'SALU Main Campus' }}</strong>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="salu-metric-box">
                                    <span class="salu-metric-label">ASSIGNED ROLL NUMBER</span>
                                    <strong class="salu-metric-value text-primary">
                                        {{ $enrollment->roll_number ?? 'Pending Approval' }}
                                    </strong>
                                </div>
                            </div>
                        </div>

                        @if($enrollment->status === 'REJECTED' && $enrollment->rejection_reason)
                            <div class="alert alert-danger border-0 rounded-3 mb-3 p-3 small">
                                <i class="fas fa-triangle-exclamation me-1"></i> <strong>Rejection Reason:</strong> {{ $enrollment->rejection_reason }}
                            </div>
                        @endif

                        <div class="d-flex flex-wrap align-items-center justify-content-between pt-3 border-top gap-2">
                            <div class="d-flex align-items-center gap-2">
                                @if($latestFee)
                                    <span class="small text-muted">
                                        Fee: <strong>PKR {{ number_format($latestFee->amount, 0) }}</strong>
                                        <span class="badge {{ $latestFee->status === 'VERIFIED' || $latestFee->status === 'PAID' ? 'bg-success' : 'bg-warning text-dark' }} ms-1">
                                            {{ $latestFee->status }}
                                        </span>
                                    </span>
                                @endif
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('enrollment.details', $enrollment->id) }}" class="btn salu-btn-pill-outline btn-sm">
                                    <i class="fas fa-eye me-1"></i> View Details
                                </a>
                                @if($latestFee)
                                    <a href="{{ route('enrollment.challan-pdf', $latestFee->id) }}" target="_blank" class="btn salu-btn-pill-outline btn-sm">
                                        <i class="fas fa-file-pdf text-danger me-1"></i> Challan PDF
                                    </a>
                                @endif
                                @if($enrollment->status === 'APPROVED')
                                    <a href="{{ route('enrollment.admit-card-pdf', $enrollment->id) }}" target="_blank" class="btn salu-btn-pill-blue btn-sm">
                                        <i class="fas fa-id-card me-1"></i> Admit Card PDF
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
