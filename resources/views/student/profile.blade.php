@extends('layouts.app')

@section('title', 'Student Profile & Settings')

@php
    $title = 'My Profile';
    $user = auth()->user()->load('college');
@endphp

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">
            <i class="fas fa-user-gear text-primary me-2"></i>Student Profile &amp; Account Settings
        </h4>
        <p class="text-muted small mb-0">Manage your verified university credentials and academic affiliation</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-4 d-flex align-items-center p-3 mb-4" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);">
        <i class="fas fa-circle-check fa-lg me-2 text-success"></i>
        <div class="flex-grow-1 text-success-emphasis fw-semibold">{{ session('success') }}</div>
    </div>
@endif

<div class="row g-4">
    <!-- LEFT: PROFILE CARD -->
    <div class="col-lg-4">
        <div class="card salu-overview-card text-center p-4">
            <div class="salu-hero-avatar-wrap mx-auto mb-3" style="width: 84px; height: 84px;">
                <div class="salu-hero-avatar fs-2 text-warning">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
            <h5 class="fw-bold text-dark mb-1">{{ $user->full_name }}</h5>
            <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill mb-3 fw-bold">
                <i class="fas fa-shield-halved me-1"></i> Verified {{ $user->role }}
            </span>
            <div class="p-3 bg-light rounded-3 text-start small">
                <div class="mb-2">
                    <span class="text-muted d-block" style="font-size:0.7rem;">CNIC:</span>
                    <strong>{{ $user->cnic }}</strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted d-block" style="font-size:0.7rem;">EMAIL:</span>
                    <strong>{{ $user->email }}</strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted d-block" style="font-size:0.7rem;">MOBILE:</span>
                    <strong>{{ $user->phone }}</strong>
                </div>
                <div>
                    <span class="text-muted d-block" style="font-size:0.7rem;">AFFILIATED CAMPUS:</span>
                    <strong>{{ $user->college->name ?? 'SALU Main Campus' }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: EDIT PARTICULARS -->
    <div class="col-lg-8">
        <div class="card salu-overview-card">
            <div class="card-header salu-overview-header py-3 px-4">
                <h5 class="mb-0 fw-bold text-white"><i class="fas fa-id-card text-warning me-2"></i>Account Particulars</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Full Name</label>
                        <input class="form-control" value="{{ $user->full_name }}" readonly />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Father's Name</label>
                        <input class="form-control" value="{{ $user->father_name }}" readonly />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">National CNIC / B-Form</label>
                        <input class="form-control" value="{{ $user->cnic }}" readonly />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Registered Email Address</label>
                        <input class="form-control" value="{{ $user->email }}" readonly />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Mobile Phone Number</label>
                        <input class="form-control" value="{{ $user->phone }}" readonly />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Affiliated Institution</label>
                        <input class="form-control" value="{{ $user->college->name ?? 'SALU Khairpur Main Campus' }}" readonly />
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top text-muted small">
                    <i class="fas fa-info-circle me-1 text-primary"></i>
                    Personal particulars are verified against university admission records. To update CNIC or Full Name, please visit the Controller of Examinations office.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
