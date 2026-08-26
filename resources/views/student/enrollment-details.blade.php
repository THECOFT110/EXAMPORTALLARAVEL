@extends('layouts.app')

@section('title', 'Enrollment Application Details')

@php
    $title = 'Enrollment Details';
@endphp

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <a href="{{ route('student.enrollments') }}" class="btn salu-btn-pill-outline btn-sm mb-2">
            <i class="fas fa-arrow-left me-1"></i> Back to Enrollments
        </a>
        <h4 class="fw-bold text-dark mb-0">
            <i class="fas fa-file-lines text-primary me-2"></i>Enrollment Application #{{ strtoupper(substr($enrollment->id, 0, 8)) }}
        </h4>
    </div>
    <div class="d-flex flex-wrap gap-2">
        @if($enrollment->status === 'APPROVED')
            <a href="{{ route('enrollment.admit-card-pdf', $enrollment->id) }}" target="_blank" class="btn salu-btn-pill-blue">
                <i class="fas fa-id-card me-1"></i> Download Admit Card
            </a>
            @if($enrollment->results->isNotEmpty())
                <a href="{{ route('enrollment.result-card-pdf', $enrollment->id) }}" target="_blank" class="btn salu-btn-pill-green">
                    <i class="fas fa-award me-1"></i> Download Result Card
                </a>
            @endif
        @endif
    </div>
</div>

<div class="row g-4">
    <!-- LEFT COLUMN: APPLICATION INFORMATION -->
    <div class="col-lg-8">
        <!-- ACADEMIC & PROGRAM DETAILS -->
        <div class="card salu-overview-card mb-4">
            <div class="card-header salu-overview-header py-3 px-4">
                <h5 class="mb-0 fw-bold text-white"><i class="fas fa-graduation-cap text-warning me-2"></i>Academic Program &amp; Affiliation</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <span class="salu-metric-label">ENROLLED PROGRAM</span>
                        <strong class="salu-metric-value fs-6">{{ $enrollment->program }}</strong>
                    </div>
                    <div class="col-md-6">
                        <span class="salu-metric-label">SESSION &amp; SEMESTER</span>
                        <strong class="salu-metric-value fs-6">{{ $enrollment->session }} &bull; {{ $enrollment->semester }}</strong>
                    </div>
                    <div class="col-md-6">
                        <span class="salu-metric-label">COLLEGE / DEPARTMENT</span>
                        <strong class="salu-metric-value">{{ $enrollment->college->name ?? 'SALU Main Campus' }}</strong>
                    </div>
                    <div class="col-md-6">
                        <span class="salu-metric-label">ASSIGNED ROLL NUMBER</span>
                        <strong class="salu-metric-value text-primary fs-6">{{ $enrollment->roll_number ?? 'Pending Approval' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- PERSONAL PARTICULARS -->
        <div class="card salu-overview-card mb-4">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-user-circle text-primary me-2"></i>Personal Particulars</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <span class="salu-metric-label">FULL NAME</span>
                        <strong class="salu-metric-value">{{ $enrollment->user->full_name }}</strong>
                    </div>
                    <div class="col-md-6">
                        <span class="salu-metric-label">FATHER'S NAME</span>
                        <strong class="salu-metric-value">{{ $enrollment->father_name }}</strong>
                    </div>
                    <div class="col-md-4">
                        <span class="salu-metric-label">CNIC / B-FORM</span>
                        <strong class="salu-metric-value">{{ $enrollment->user->cnic }}</strong>
                    </div>
                    <div class="col-md-4">
                        <span class="salu-metric-label">DATE OF BIRTH</span>
                        <strong class="salu-metric-value">{{ $enrollment->dob ? \Carbon\Carbon::parse($enrollment->dob)->format('d M Y') : 'N/A' }}</strong>
                    </div>
                    <div class="col-md-4">
                        <span class="salu-metric-label">GENDER</span>
                        <strong class="salu-metric-value">{{ $enrollment->gender }}</strong>
                    </div>
                    <div class="col-md-6">
                        <span class="salu-metric-label">DOMICILE (DISTRICT &amp; PROVINCE)</span>
                        <strong class="salu-metric-value">{{ $enrollment->domicile_district ?? 'Khairpur' }}, {{ $enrollment->domicile_province ?? 'Sindh' }}</strong>
                    </div>
                    <div class="col-md-6">
                        <span class="salu-metric-label">MOBILE CONTACT</span>
                        <strong class="salu-metric-value">{{ $enrollment->contact_number ?? $enrollment->user->phone }}</strong>
                    </div>
                    <div class="col-12">
                        <span class="salu-metric-label">PERMANENT RESIDENTIAL ADDRESS</span>
                        <strong class="salu-metric-value">{{ $enrollment->address }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- PREVIOUS QUALIFICATION -->
        <div class="card salu-overview-card mb-4">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-book-bookmark text-primary me-2"></i>Previous Educational Records</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <span class="salu-metric-label">PASSING YEAR</span>
                        <strong class="salu-metric-value">{{ $enrollment->passing_year ?? '2023' }}</strong>
                    </div>
                    <div class="col-md-4">
                        <span class="salu-metric-label">DIVISION / GRADE</span>
                        <strong class="salu-metric-value">{{ $enrollment->division_obtained ?? '1st Division' }}</strong>
                    </div>
                    <div class="col-md-4">
                        <span class="salu-metric-label">INTERMEDIATE BOARD</span>
                        <strong class="salu-metric-value">{{ $enrollment->name_of_board ?? 'BISE Sukkur' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN: STATUS & FEES -->
    <div class="col-lg-4">
        <!-- APPLICATION STATUS CARD -->
        <div class="card salu-overview-card mb-4">
            <div class="card-body p-4 text-center">
                <span class="salu-metric-label mb-2">VERIFICATION STATUS</span>
                @php
                    $statusBadge = match($enrollment->status) {
                        'APPROVED' => 'bg-success text-white',
                        'REJECTED' => 'bg-danger text-white',
                        'PENDING' => 'bg-warning text-dark',
                        default => 'bg-secondary text-white'
                    };
                @endphp
                <span class="badge {{ $statusBadge }} fs-5 px-4 py-2 rounded-pill shadow-sm mb-3">
                    {{ $enrollment->status }}
                </span>

                @if($enrollment->status === 'APPROVED')
                    <p class="text-muted small mb-0">Application is officially approved by the Examination Department.</p>
                @elseif($enrollment->status === 'PENDING')
                    <p class="text-muted small mb-0">Application is currently under administrative scrutiny.</p>
                @endif
            </div>
        </div>

        <!-- FEE CHALLANS FOR THIS ENROLLMENT -->
        <div class="card salu-overview-card mb-4">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-receipt text-primary me-2"></i>Fee Challans</h5>
            </div>
            <div class="card-body p-4">
                @if($enrollment->fees->isEmpty())
                    <p class="text-muted small mb-0">No challans generated yet.</p>
                @else
                    <div class="d-flex flex-column gap-3">
                        @foreach($enrollment->fees as $fee)
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark small">{{ $fee->challan_number }}</span>
                                    <span class="badge {{ $fee->status === 'VERIFIED' || $fee->status === 'PAID' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ $fee->status }}
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center text-muted small mb-2">
                                    <span>Amount: <strong>PKR {{ number_format($fee->amount, 0) }}</strong></span>
                                    <span>Due: {{ $fee->due_date ? \Carbon\Carbon::parse($fee->due_date)->format('d M Y') : 'N/A' }}</span>
                                </div>
                                <a href="{{ route('enrollment.challan-pdf', $fee->id) }}" target="_blank" class="btn salu-btn-pill-outline btn-sm w-100">
                                    <i class="fas fa-print me-1"></i> Print Challan PDF
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- SEAT ALLOCATION -->
        @if($enrollment->seat)
            <div class="card salu-overview-card">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-chair text-primary me-2"></i>Exam Center &amp; Seat</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-2">
                        <span class="salu-metric-label">CENTER</span>
                        <strong class="salu-metric-value small">{{ $enrollment->seat->exam_center }}</strong>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <span class="salu-metric-label">ROOM</span>
                            <strong class="salu-metric-value">{{ $enrollment->seat->room_no }}</strong>
                        </div>
                        <div class="col-6">
                            <span class="salu-metric-label">SEAT NO</span>
                            <strong class="salu-metric-value text-primary">{{ $enrollment->seat->seat_no }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
