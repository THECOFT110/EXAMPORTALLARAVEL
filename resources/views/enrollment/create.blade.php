@extends('layouts.app')

@section('title', 'Student Enrollment Application')

@php
    $title = 'Enrollment Application';
@endphp

@push('styles')
<style>
/* ══════════════════════════════════════════════════════════════
   SALU ENROLLMENT APPLICATION WIZARD - MODERN SOFT GRADIENTS
   ══════════════════════════════════════════════════════════════ */
.salu-enrollment-container {
    max-width: 1080px;
    margin: 0 auto;
}

/* STEPPER NAVIGATION BAR */
.salu-stepper-wrap {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    margin-bottom: 1.25rem;
    padding: 0 1rem;
}

.salu-stepper-track {
    position: absolute;
    top: 20px;
    left: 40px;
    right: 40px;
    height: 3px;
    background: #e2e8f0;
    z-index: 1;
}

.salu-stepper-track-fill {
    height: 100%;
    width: 0%;
    background: linear-gradient(90deg, #f27220, #0284c7);
    transition: width 0.35s ease;
}

.salu-step-item {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    background: none;
    border: none;
    padding: 0;
}

.salu-step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #ffffff;
    border: 2.5px solid #cbd5e1;
    color: #64748b;
    font-weight: 800;
    font-size: 0.88rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.25s ease;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.04);
}

.salu-step-item.active .salu-step-circle {
    border-color: #f27220;
    background: #f27220;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(242, 114, 32, 0.35);
    transform: scale(1.06);
}

.salu-step-item.completed .salu-step-circle {
    border-color: #10b981;
    background: #10b981;
    color: #ffffff;
}

.salu-step-title {
    font-size: 0.72rem;
    font-weight: 700;
    color: #64748b;
    margin-top: 0.35rem;
    transition: color 0.2s;
    text-align: center;
}

.salu-step-item.active .salu-step-title {
    color: #0f172a;
}

/* WIZARD FORM CARD */
.salu-wizard-card {
    background: linear-gradient(180deg, #ffffff 0%, #fbfcfe 60%, #f0f7ff 100%);
    border: 1.5px solid rgba(2, 132, 199, 0.2);
    border-radius: 18px;
    box-shadow: 0 12px 35px -8px rgba(11, 19, 61, 0.06), 0 0 20px rgba(2, 132, 199, 0.04);
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.25rem;
    position: relative;
}

.salu-wizard-section-title {
    font-size: 0.98rem;
    font-weight: 800;
    color: #091338;
    margin-bottom: 0.75rem;
    padding-bottom: 0.4rem;
    border-bottom: 1.5px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

/* STEP 1: GLOWING CASCADE CARD */
.salu-glow-cascade-card {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.94) 100%);
    border: 1.5px solid rgba(2, 132, 199, 0.28);
    border-radius: 14px;
    padding: 0.85rem 1.1rem;
    box-shadow: 0 6px 20px -4px rgba(2, 132, 199, 0.09), inset 0 1px 0 rgba(255, 255, 255, 1);
    position: relative;
    overflow: hidden;
    margin-bottom: 0.85rem;
}

.salu-glow-cascade-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #f27220, #0284c7, #10b981);
}

.salu-form-group {
    margin-bottom: 0.55rem;
}

.salu-form-label {
    display: block;
    font-size: 0.78rem;
    font-weight: 700;
    color: #334155;
    margin-bottom: 0.2rem;
}

.salu-form-control {
    width: 100%;
    height: 36px;
    padding: 0.32rem 0.65rem;
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    font-size: 0.84rem;
    color: #0f172a;
    background: #ffffff;
    transition: all 0.15s ease;
}

.salu-form-control:focus {
    outline: none;
    border-color: #0284c7;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.18), 0 2px 8px rgba(2, 132, 199, 0.1);
}

.salu-form-control[readonly],
.salu-input-locked {
    background-color: #f1f5f9 !important;
    color: #475569 !important;
    border: 1.5px solid #cbd5e1 !important;
    cursor: not-allowed !important;
    user-select: none !important;
}

.salu-form-control[readonly]:hover,
.salu-input-locked:hover {
    background-color: #e2e8f0 !important;
    cursor: not-allowed !important;
}

/* PHOTO UPLOAD BOX */
.salu-photo-uploader {
    width: 125px;
    height: 145px;
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    background: #f8fafc;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: all 0.2s ease;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.03);
}

.salu-photo-uploader:hover {
    border-color: #f27220;
    background: #fffaf5;
    box-shadow: 0 0 16px rgba(242, 114, 32, 0.22);
    transform: translateY(-2px);
}

.salu-photo-preview {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: none;
}

/* ACADEMIC QUALIFICATIONS TABLE */
.salu-academic-table {
    min-width: 1060px;
    width: 100%;
    margin-bottom: 0;
}

.salu-academic-table th {
    background: #091338;
    color: #ffffff;
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 0.48rem 0.4rem;
    vertical-align: middle;
    text-align: center;
    white-space: nowrap;
    border-color: rgba(255, 255, 255, 0.15);
}

.salu-academic-table td {
    padding: 0.35rem 0.35rem;
    vertical-align: middle;
    background: #ffffff;
    white-space: nowrap;
}

.salu-tbl-input, .salu-tbl-select {
    display: block;
    width: 100%;
    height: 32px;
    padding: 0.2rem 0.45rem;
    font-size: 0.81rem;
    font-weight: 500;
    line-height: 1.3;
    color: #0f172a;
    background-color: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 6px;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.salu-tbl-select {
    padding-right: 1.5rem;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.4rem center;
    background-size: 10px 8px;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
}

.salu-tbl-input:focus, .salu-tbl-select:focus {
    border-color: #f27220;
    outline: 0;
    box-shadow: 0 0 0 2.5px rgba(242, 114, 32, 0.15);
}

.salu-pct-badge {
    background: #ecfdf5 !important;
    color: #047857 !important;
    font-weight: 800 !important;
    border: 1.5px solid #a7f3d0 !important;
    cursor: default;
    text-align: center;
    box-shadow: 0 0 8px rgba(16, 185, 129, 0.15);
}

/* STEP 4: PREVIEW & REVIEW DOSSIER GLOW */
.salu-preview-dossier {
    background: linear-gradient(140deg, #070d2b 0%, #0c1c4f 50%, #051336 100%);
    border: 1.5px solid rgba(212, 175, 55, 0.5);
    border-radius: 14px;
    padding: 1rem 1.25rem;
    color: #ffffff;
    box-shadow: 0 10px 30px rgba(9, 19, 56, 0.28), 0 0 20px rgba(212, 175, 55, 0.1);
    position: relative;
    overflow: hidden;
}

.salu-preview-dossier::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 250px;
    height: 250px;
    background: radial-gradient(circle, rgba(2, 132, 199, 0.2) 0%, transparent 70%);
    pointer-events: none;
}

.salu-preview-label {
    font-size: 0.68rem;
    font-weight: 800;
    color: #f6ad55;
    letter-spacing: 0.6px;
    text-transform: uppercase;
    margin-bottom: 0.15rem;
    display: block;
}

.salu-preview-val {
    font-size: 0.95rem;
    font-weight: 700;
    color: #ffffff;
    line-height: 1.25;
}

.salu-glow-card {
    background: #ffffff;
    border: 1.5px solid rgba(2, 132, 199, 0.2);
    border-radius: 14px;
    box-shadow: 0 6px 20px -4px rgba(2, 132, 199, 0.06), inset 0 1px 0 rgba(255, 255, 255, 0.9);
    padding: 0.85rem 1.1rem;
    margin-bottom: 0.85rem;
}

.salu-glow-declaration {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    border: 1.5px solid #f59e0b;
    box-shadow: 0 0 18px rgba(245, 158, 11, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.9);
    border-radius: 12px;
    padding: 0.65rem 0.95rem;
}

.salu-btn-pill-green {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff;
    font-weight: 800;
    border-radius: 50px;
    border: none;
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35), 0 0 12px rgba(16, 185, 129, 0.2);
    transition: all 0.25s ease;
}

.salu-btn-pill-green:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: #ffffff;
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.55), 0 0 20px rgba(16, 185, 129, 0.35);
    transform: translateY(-2px);
}

.salu-btn-pill-blue {
    background: linear-gradient(135deg, #f27220 0%, #e05e0c 100%);
    color: #ffffff;
    font-weight: 800;
    border-radius: 50px;
    border: none;
    box-shadow: 0 6px 20px rgba(242, 114, 32, 0.35);
    transition: all 0.25s ease;
}

.salu-btn-pill-blue:hover {
    background: linear-gradient(135deg, #e05e0c 0%, #c24f07 100%);
    color: #ffffff;
    box-shadow: 0 8px 25px rgba(242, 114, 32, 0.5);
    transform: translateY(-2px);
}

.salu-btn-pill-outline {
    border: 1.5px solid #cbd5e1;
    border-radius: 50px;
    color: #475569;
    font-weight: 700;
    background: #ffffff;
    transition: all 0.2s ease;
}

.salu-btn-pill-outline:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
    color: #0f172a;
}

/* STEP CONTENT WRAPPERS */
.salu-step-pane {
    display: none;
}

.salu-step-pane.active {
    display: block;
    animation: fadeIn 0.25s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 768px) {
    .salu-wizard-card {
        padding: 1.5rem;
    }
    .salu-stepper-wrap {
        padding: 0;
    }
    .salu-step-circle {
        width: 38px;
        height: 38px;
        font-size: 0.85rem;
    }
    .salu-step-title {
        font-size: 0.68rem;
    }
}
</style>
@endpush

@section('content')
<div class="salu-enrollment-container">

    <!-- PAGE HERO HEADER -->
    <div class="card salu-student-hero-card mb-4">
        <div class="card-body p-4 position-relative" style="z-index: 2;">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h3 class="mb-0 fw-bold text-white">Student Enrollment Application</h3>
                        <span class="salu-hero-badge"><i class="fas fa-file-signature me-1"></i> Form-I</span>
                    </div>
                    <p class="text-white-50 small mb-0">
                        Shah Abdul Latif University, Khairpur &bull; Academic Session <strong>{{ $activeYear->name ?? '2024-2025' }}</strong>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-warning text-dark px-3 py-2 fw-bold rounded-pill">
                        <i class="fas fa-receipt me-1"></i> Enrollment Fee: PKR 2,500
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- 4-STEP WIZARD STEPPER -->
    <div class="salu-stepper-wrap" id="stepperNav">
        <div class="salu-stepper-track">
            <div class="salu-stepper-track-fill" id="trackFill"></div>
        </div>

        <button type="button" class="salu-step-item active" onclick="goToStep(1)">
            <div class="salu-step-circle" id="circle1">1</div>
            <span class="salu-step-title">Program &amp; Personal Info</span>
        </button>

        <button type="button" class="salu-step-item" onclick="goToStep(2)">
            <div class="salu-step-circle" id="circle2">2</div>
            <span class="salu-step-title">Academic History</span>
        </button>

        <button type="button" class="salu-step-item" onclick="goToStep(3)">
            <div class="salu-step-circle" id="circle3">3</div>
            <span class="salu-step-title">Upload Documents</span>
        </button>

        <button type="button" class="salu-step-item" onclick="goToStep(4)">
            <div class="salu-step-circle" id="circle4">4</div>
            <span class="salu-step-title">Review &amp; Submit</span>
        </button>
    </div>

    <!-- FORM CARD -->
    <div class="salu-wizard-card">
        <form method="POST" action="{{ route('enrollment.store') }}" enctype="multipart/form-data" id="enrollmentForm">
            @csrf

            <!-- STEP 1: COLLEGE, PROGRAM & PERSONAL PARTICULARS (MERGED) -->
            <div class="salu-step-pane active" id="stepPane1">
                <!-- SUB-SECTION 1: DISTRICT, COLLEGE & ACADEMIC PROGRAM -->
                <div class="salu-glow-cascade-card mb-2">
                    <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                        <h4 class="salu-wizard-section-title mb-0 border-0 pb-0" style="font-size:0.9rem;">
                            <i class="fas fa-building-columns text-primary"></i> 1. Affiliated College &amp; Academic Program
                        </h4>
                        <span class="badge bg-primary px-2 py-1 rounded-pill fw-bold" style="font-size: 0.68rem; box-shadow: 0 2px 8px rgba(2, 132, 199, 0.25);">
                            <i class="fas fa-sparkles me-1"></i> Course Offering Portal
                        </span>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-4">
                            <div class="salu-form-group mb-1">
                                <label class="salu-form-label"><i class="fas fa-map-location-dot text-primary me-1"></i> College District <span class="text-danger">*</span></label>
                                <select name="college_district" id="inpDistrict" class="salu-form-control" onchange="onDistrictChange()" required>
                                    <option value="">-- Select District --</option>
                                    @foreach(array_keys($districtCollegeProgramData ?? []) as $dist)
                                        <option value="{{ $dist }}" {{ (old('college_district', 'Khairpur') == $dist) ? 'selected' : '' }}>{{ $dist }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="salu-form-group mb-1">
                                <label class="salu-form-label"><i class="fas fa-school text-primary me-1"></i> Admitted College / Institute <span class="text-danger">*</span></label>
                                <select name="college_id" id="inpCollege" class="salu-form-control" onchange="onCollegeChange()" required>
                                    <option value="">-- Select College --</option>
                                </select>
                                @error('college_id')<span class="text-danger small">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="salu-form-group mb-1">
                                <label class="salu-form-label"><i class="fas fa-graduation-cap text-primary me-1"></i> Degree Course <span class="text-danger">*</span></label>
                                <select name="program" id="inpProgram" class="salu-form-control" required>
                                    <option value="">-- Select Program --</option>
                                </select>
                                @error('program')<span class="text-danger small">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="salu-form-group mb-0">
                                <label class="salu-form-label"><i class="fas fa-calendar-check text-primary me-1"></i> Academic Session</label>
                                <input name="session" id="inpSession" value="{{ $activeYear->name ?? '2024-2025' }}" class="salu-form-control salu-input-locked" readonly />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SUB-SECTION 2: PERSONAL PARTICULARS & PASSPORT PHOTO -->
                <h4 class="salu-wizard-section-title mt-2 mb-2">
                    <i class="fas fa-user-circle text-primary"></i> 2. Personal Particulars &amp; Photograph
                </h4>

                <div class="row g-2 mb-2">
                    <div class="col-md-9">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="salu-form-group">
                                    <label class="salu-form-label">Full Name <span class="text-danger">*</span></label>
                                    <input name="full_name" id="inpFullName" value="{{ auth()->user()->full_name }}" class="salu-form-control salu-input-locked" readonly />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="salu-form-group">
                                    <label class="salu-form-label">Father's Name <span class="text-danger">*</span></label>
                                    <input name="father_name" id="inpFatherName" value="{{ auth()->user()->father_name }}" class="salu-form-control" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="salu-form-group">
                                    <label class="salu-form-label">Relation <span class="text-danger">*</span></label>
                                    <select name="so_do_wo" id="inpRelation" class="salu-form-control" required>
                                        <option value="S/o">S/O (Son of)</option>
                                        <option value="D/o">D/O (Daughter of)</option>
                                        <option value="W/o">W/O (Wife of)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="salu-form-group">
                                    <label class="salu-form-label">Surname / Family Cast</label>
                                    <input name="surname" id="inpSurname" class="salu-form-control" placeholder="e.g. Soomro, Kalhoro, Memon" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="salu-form-group">
                                    <label class="salu-form-label">National CNIC / B-Form <span class="text-danger">*</span></label>
                                    <input name="cnic" id="inpCnic" value="{{ auth()->user()->cnic }}" class="salu-form-control salu-input-locked" readonly />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="salu-form-group">
                                    <label class="salu-form-label">Gender <span class="text-danger">*</span></label>
                                    <select name="gender" id="inpGender" class="salu-form-control" required>
                                        <option value="MALE">Male</option>
                                        <option value="FEMALE">Female</option>
                                        <option value="OTHER">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="salu-form-group">
                                    <label class="salu-form-label">Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" name="dob" id="inpDob" value="2004-05-15" class="salu-form-control" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="salu-form-group">
                                    <label class="salu-form-label">Mobile Contact Number <span class="text-danger">*</span></label>
                                    <input name="contact_number" id="inpContact" value="{{ auth()->user()->phone }}" class="salu-form-control" placeholder="0300-0000000" maxlength="12" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="salu-form-group">
                                    <label class="salu-form-label">Domicile District <span class="text-danger">*</span></label>
                                    <select name="domicile_district" id="inpDomicileDist" class="salu-form-control" required>
                                        <option value="Khairpur" selected>Khairpur</option>
                                        <option value="Sukkur">Sukkur</option>
                                        <option value="Ghotki">Ghotki</option>
                                        <option value="Shikarpur">Shikarpur</option>
                                        <option value="Larkana">Larkana</option>
                                        <option value="Jacobabad">Jacobabad</option>
                                        <option value="Kashmore">Kashmore / Kandhkot</option>
                                        <option value="Naushahro Feroze">Naushahro Feroze</option>
                                        <option value="Qambar Shahdadkot">Qambar Shahdadkot</option>
                                        <option value="Other">Other District</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PHOTO UPLOAD PREVIEW (COMPACT) -->
                    <div class="col-md-3 d-flex flex-column align-items-center justify-content-center text-center">
                        <label class="salu-form-label mb-1">Photograph <span class="text-danger">*</span></label>
                        <div class="salu-photo-uploader" onclick="document.getElementById('photoInput').click()">
                            <img id="photoPreviewImg" class="salu-photo-preview" alt="Student Photo" />
                            <div id="photoPlaceholder" class="p-2 text-center">
                                <i class="fas fa-camera fa-lg text-muted mb-1"></i>
                                <span class="d-block small text-muted fw-bold" style="font-size:0.75rem;">Upload Photo</span>
                                <span class="text-muted" style="font-size:0.62rem;">Passport size (Max 2MB)</span>
                            </div>
                        </div>
                        <input type="file" name="photo" id="photoInput" accept="image/*" class="d-none" onchange="previewStudentPhoto(this)" />
                    </div>

                    <div class="col-md-4">
                        <div class="salu-form-group">
                            <label class="salu-form-label">Domicile Province <span class="text-danger">*</span></label>
                            <input name="domicile_province" id="inpDomicileProv" value="Sindh" class="salu-form-control" required />
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="salu-form-group">
                            <label class="salu-form-label">Permanent Residential Address <span class="text-danger">*</span></label>
                            <input name="address" id="inpAddress" class="salu-form-control" value="House No. 12, Station Road, Khairpur Mirs" placeholder="House No, Street, Mohalla, City/Village" required />
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-2">
                    <button type="button" class="btn salu-btn-pill-blue px-4 py-1" onclick="validateAndNext(1)" style="font-size:0.88rem;">
                        Proceed to Academic History <i class="fas fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 2: PREVIOUS ACADEMIC RECORDS (TABLE STYLE) -->
            <div class="salu-step-pane" id="stepPane2">
                <h4 class="salu-wizard-section-title mb-2">
                    <i class="fas fa-graduation-cap text-primary"></i> Step 2: Previous Academic Qualifications (Matric &amp; Intermediate)
                </h4>

                <div class="alert alert-info py-1 px-2 mb-2 rounded-3 d-flex align-items-center gap-2" style="background:#eff6ff; border:1px solid #bfdbfe; color:#1e40af; font-size:0.78rem;">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        Please enter qualification details for <strong>Matriculation (SSC)</strong> and <strong>Intermediate (HSC)</strong>. Obtained marks cannot exceed total marks.
                    </div>
                </div>

                <div class="table-responsive mb-2 rounded-3 shadow-sm border" style="overflow-x: auto;">
                    <table class="table table-bordered align-middle mb-0 salu-academic-table">
                        <thead>
                            <tr>
                                <th style="width: 125px; min-width: 125px;">Exam Level</th>
                                <th style="min-width: 180px;">Group / Discipline <span class="text-danger">*</span></th>
                                <th style="min-width: 175px;">Board / University <span class="text-danger">*</span></th>
                                <th style="width: 95px; min-width: 95px;">Year <span class="text-danger">*</span></th>
                                <th style="width: 115px; min-width: 115px;">Roll / Seat No <span class="text-danger">*</span></th>
                                <th style="width: 95px; min-width: 95px;">Total Marks <span class="text-danger">*</span></th>
                                <th style="width: 95px; min-width: 95px;">Marks Obt. <span class="text-danger">*</span></th>
                                <th style="width: 95px; min-width: 95px;">Percentage</th>
                                <th style="width: 110px; min-width: 110px;">Grade <span class="text-danger">*</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- ROW 1: MATRIC / SSC -->
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary px-2 py-1 rounded-pill">SSC</span>
                                        <strong style="font-size:0.86rem;">Matric (10th)</strong>
                                    </div>
                                </td>
                                <td>
                                    <select name="matric_group" id="inpMatricGroup" class="salu-tbl-select" required>
                                        <option value="Science" selected>Science</option>
                                        <option value="General / Private">General / Private</option>
                                        <option value="Arts">Arts</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="matric_board" id="inpMatricBoard" class="salu-tbl-select" required>
                                        <option value="BISE Sukkur" selected>BISE Sukkur</option>
                                        <option value="BISE Larkana">BISE Larkana</option>
                                        <option value="BISE Hyderabad">BISE Hyderabad</option>
                                        <option value="BISE Mirpurkhas">BISE Mirpurkhas</option>
                                        <option value="BSEK Karachi">BSEK Karachi</option>
                                        <option value="BISE Shaheed Benazirabad">BISE Shaheed Benazirabad</option>
                                        <option value="Federal Board (FBISE) Islamabad">Federal Board (FBISE) Islamabad</option>
                                        <option value="Other Board">Other Board</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="matric_passing_year" id="inpMatricYear" class="salu-tbl-select text-center" required>
                                        <option value="2024">2024</option>
                                        <option value="2023">2023</option>
                                        <option value="2022" selected>2022</option>
                                        <option value="2021">2021</option>
                                        <option value="2020">2020</option>
                                        <option value="2019">2019</option>
                                        <option value="2018">2018</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="matric_roll_no" id="inpMatricRoll" class="salu-tbl-input text-center digit-only font-monospace" placeholder="Roll No" value="105420" maxlength="10" required />
                                </td>
                                <td>
                                    <input type="text" name="matric_total_marks" id="inpMatricTotal" class="salu-tbl-input text-center digit-only" value="1100" maxlength="4" oninput="calculateMarks('Matric')" required />
                                </td>
                                <td>
                                    <input type="text" name="matric_obtained_marks" id="inpMatricObtained" class="salu-tbl-input text-center digit-only fw-bold text-primary" placeholder="Obt" value="915" maxlength="4" oninput="calculateMarks('Matric')" required />
                                </td>
                                <td>
                                    <input type="text" name="matric_percentage" id="inpMatricPct" class="salu-tbl-input text-center salu-pct-badge" value="83.18%" readonly />
                                </td>
                                <td>
                                    <select name="matric_grade" id="inpMatricGrade" class="salu-tbl-select text-center fw-bold" required>
                                        <option value="A-1" selected>A-1 (80%+)</option>
                                        <option value="A">A (70-79%)</option>
                                        <option value="B">B (60-69%)</option>
                                        <option value="C">C (50-59%)</option>
                                        <option value="D">D (40-49%)</option>
                                        <option value="E">E (33-39%)</option>
                                    </select>
                                </td>
                            </tr>

                            <!-- ROW 2: INTERMEDIATE / HSC -->
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-success px-2 py-1 rounded-pill">HSC</span>
                                        <strong style="font-size:0.86rem;">Inter (12th)</strong>
                                    </div>
                                </td>
                                <td>
                                    <select name="inter_group" id="inpInterGroup" class="salu-tbl-select" required>
                                        <option value="Pre-Medical">Pre-Medical</option>
                                        <option value="Pre-Engineering" selected>Pre-Engineering</option>
                                        <option value="Computer Science (ICS)">Computer Science (ICS)</option>
                                        <option value="Humanities / Arts">Humanities / Arts</option>
                                        <option value="Commerce">Commerce</option>
                                        <option value="General Science">General Science</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="inter_board" id="inpInterBoard" class="salu-tbl-select" required>
                                        <option value="BISE Sukkur" selected>BISE Sukkur</option>
                                        <option value="BISE Larkana">BISE Larkana</option>
                                        <option value="BISE Hyderabad">BISE Hyderabad</option>
                                        <option value="BISE Mirpurkhas">BISE Mirpurkhas</option>
                                        <option value="BIEK Karachi">BIEK Karachi</option>
                                        <option value="BISE Shaheed Benazirabad">BISE Shaheed Benazirabad</option>
                                        <option value="Federal Board (FBISE) Islamabad">Federal Board (FBISE) Islamabad</option>
                                        <option value="Other Board">Other Board</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="inter_passing_year" id="inpInterYear" class="salu-tbl-select text-center" required>
                                        <option value="2024" selected>2024</option>
                                        <option value="2023">2023</option>
                                        <option value="2022">2022</option>
                                        <option value="2021">2021</option>
                                        <option value="2020">2020</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="inter_roll_no" id="inpInterRoll" class="salu-tbl-input text-center digit-only font-monospace" placeholder="Roll No" value="842109" maxlength="10" required />
                                </td>
                                <td>
                                    <input type="text" name="inter_total_marks" id="inpInterTotal" class="salu-tbl-input text-center digit-only" value="1100" maxlength="4" oninput="calculateMarks('Inter')" required />
                                </td>
                                <td>
                                    <input type="text" name="inter_obtained_marks" id="inpInterObtained" class="salu-tbl-input text-center digit-only fw-bold text-primary" placeholder="Obt" value="880" maxlength="4" oninput="calculateMarks('Inter')" required />
                                </td>
                                <td>
                                    <input type="text" name="inter_percentage" id="inpInterPct" class="salu-tbl-input text-center salu-pct-badge" value="80.00%" readonly />
                                </td>
                                <td>
                                    <select name="inter_grade" id="inpInterGrade" class="salu-tbl-select text-center fw-bold" required>
                                        <option value="A-1" selected>A-1 (80%+)</option>
                                        <option value="A">A (70-79%)</option>
                                        <option value="B">B (60-69%)</option>
                                        <option value="C">C (50-59%)</option>
                                        <option value="D">D (40-49%)</option>
                                        <option value="E">E (33-39%)</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between mt-2">
                    <button type="button" class="btn salu-btn-pill-outline px-4 py-1" onclick="goToStep(1)" style="font-size:0.88rem;">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </button>
                    <button type="button" class="btn salu-btn-pill-blue px-4 py-1" onclick="validateAndNext(2)" style="font-size:0.88rem;">
                        Proceed to Document Uploads <i class="fas fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 3: MANDATORY DOCUMENT UPLOADS (TABLE STYLE) -->
            <div class="salu-step-pane" id="stepPane3">
                <h4 class="salu-wizard-section-title mb-2">
                    <i class="fas fa-folder-open text-primary"></i> Step 3: Mandatory Document Uploads
                </h4>

                <div class="alert alert-info py-1 px-2 mb-2 rounded-3 d-flex align-items-center gap-2" style="background:#eff6ff; border:1px solid #bfdbfe; color:#1e40af; font-size:0.78rem;">
                    <i class="fas fa-shield-halved"></i>
                    <div>
                        <strong>Mandatory Document Policy:</strong> All 3 documents (CNIC copy, Matric marksheet, Intermediate marksheet) are required with distinct file names.
                    </div>
                </div>

                <div class="table-responsive mb-2 rounded-3 shadow-sm border">
                    <table class="table table-bordered align-middle mb-0 salu-academic-table salu-doc-table">
                        <thead>
                            <tr>
                                <th style="width: 220px; text-align: left; padding-left: 0.75rem;">Document Name</th>
                                <th style="width: 120px;">Format / Size</th>
                                <th style="min-width: 200px;">Upload File <span class="text-danger">*</span></th>
                                <th style="min-width: 190px;">OCR Reading &amp; Data Match</th>
                                <th style="width: 90px;">Preview</th>
                                <th style="width: 90px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- ROW 1: CNIC COPY -->
                            <tr id="rowDoc_cnic">
                                <td style="padding-left: 0.75rem;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="p-1 rounded-2 bg-primary-subtle text-primary" style="font-size:0.95rem;">
                                            <i class="fas fa-id-card"></i>
                                        </div>
                                        <div>
                                            <strong class="d-block text-dark" style="font-size:0.84rem;">CNIC / B-Form Copy <span class="text-danger">*</span></strong>
                                            <small class="text-muted" style="font-size:0.68rem;">Front &amp; Back view of applicant CNIC</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-secondary border px-2 py-1" style="font-size:0.72rem;">JPG, PNG, PDF</span>
                                    <small class="d-block text-muted mt-1" style="font-size:0.65rem;">Max: 2MB</small>
                                </td>
                                <td>
                                    <input type="file" name="doc_cnic" id="docInput_cnic" accept="image/*,.pdf" class="d-none" onchange="handleDocUpload('cnic', this)" />
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 fw-bold" style="font-size:0.78rem;" onclick="document.getElementById('docInput_cnic').click()">
                                            <i class="fas fa-arrow-up-from-bracket me-1"></i> Choose File
                                        </button>
                                        <span id="fileName_cnic" class="small text-muted text-truncate" style="max-width: 120px; font-size:0.75rem;">No file chosen</span>
                                    </div>
                                </td>
                                <td>
                                    <div id="ocrStatus_cnic">
                                        <span class="badge bg-secondary-subtle text-secondary px-2 py-1" style="font-size:0.72rem;"><i class="fas fa-clock me-1"></i> Pending Upload</span>
                                    </div>
                                    <div id="ocrDetail_cnic" class="text-muted small mt-1" style="font-size:0.68rem;">Target: {{ auth()->user()->cnic }}</div>
                                </td>
                                <td class="text-center">
                                    <div id="previewBox_cnic">
                                        <span class="text-muted small" style="font-size:0.72rem;">No preview</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button type="button" id="btnRemove_cnic" class="btn btn-sm btn-outline-danger py-0 px-2 d-none" style="font-size:0.75rem;" onclick="removeDoc('cnic')" title="Remove file">
                                        <i class="fas fa-trash-alt me-1"></i> Remove
                                    </button>
                                </td>
                            </tr>

                            <!-- ROW 2: MATRIC MARKSHEET -->
                            <tr id="rowDoc_matric">
                                <td style="padding-left: 0.75rem;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="p-1 rounded-2 bg-info-subtle text-info" style="font-size:0.95rem;">
                                            <i class="fas fa-certificate"></i>
                                        </div>
                                        <div>
                                            <strong class="d-block text-dark" style="font-size:0.84rem;">Matric Marksheet Copy <span class="text-danger">*</span></strong>
                                            <small class="text-muted" style="font-size:0.68rem;">SSC Passing Certificate</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-secondary border px-2 py-1" style="font-size:0.72rem;">JPG, PNG, PDF</span>
                                    <small class="d-block text-muted mt-1" style="font-size:0.65rem;">Max: 2MB</small>
                                </td>
                                <td>
                                    <input type="file" name="doc_matric" id="docInput_matric" accept="image/*,.pdf" class="d-none" onchange="handleDocUpload('matric', this)" />
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 fw-bold" style="font-size:0.78rem;" onclick="document.getElementById('docInput_matric').click()">
                                            <i class="fas fa-arrow-up-from-bracket me-1"></i> Choose File
                                        </button>
                                        <span id="fileName_matric" class="small text-muted text-truncate" style="max-width: 120px; font-size:0.75rem;">No file chosen</span>
                                    </div>
                                </td>
                                <td>
                                    <div id="ocrStatus_matric">
                                        <span class="badge bg-secondary-subtle text-secondary px-2 py-1" style="font-size:0.72rem;"><i class="fas fa-clock me-1"></i> Pending Upload</span>
                                    </div>
                                    <div id="ocrDetail_matric" class="text-muted small mt-1" style="font-size:0.68rem;">Target: Roll No &amp; Board Match</div>
                                </td>
                                <td class="text-center">
                                    <div id="previewBox_matric">
                                        <span class="text-muted small" style="font-size:0.72rem;">No preview</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button type="button" id="btnRemove_matric" class="btn btn-sm btn-outline-danger py-0 px-2 d-none" style="font-size:0.75rem;" onclick="removeDoc('matric')" title="Remove file">
                                        <i class="fas fa-trash-alt me-1"></i> Remove
                                    </button>
                                </td>
                            </tr>

                            <!-- ROW 3: INTERMEDIATE MARKSHEET -->
                            <tr id="rowDoc_inter">
                                <td style="padding-left: 0.75rem;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="p-1 rounded-2 bg-success-subtle text-success" style="font-size:0.95rem;">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                        <div>
                                            <strong class="d-block text-dark" style="font-size:0.84rem;">Intermediate Marksheet Copy <span class="text-danger">*</span></strong>
                                            <small class="text-muted" style="font-size:0.68rem;">HSC Final Marksheet</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-secondary border px-2 py-1" style="font-size:0.72rem;">JPG, PNG, PDF</span>
                                    <small class="d-block text-muted mt-1" style="font-size:0.65rem;">Max: 2MB</small>
                                </td>
                                <td>
                                    <input type="file" name="doc_inter" id="docInput_inter" accept="image/*,.pdf" class="d-none" onchange="handleDocUpload('inter', this)" />
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 fw-bold" style="font-size:0.78rem;" onclick="document.getElementById('docInput_inter').click()">
                                            <i class="fas fa-arrow-up-from-bracket me-1"></i> Choose File
                                        </button>
                                        <span id="fileName_inter" class="small text-muted text-truncate" style="max-width: 120px; font-size:0.75rem;">No file chosen</span>
                                    </div>
                                </td>
                                <td>
                                    <div id="ocrStatus_inter">
                                        <span class="badge bg-secondary-subtle text-secondary px-2 py-1" style="font-size:0.72rem;"><i class="fas fa-clock me-1"></i> Pending Upload</span>
                                    </div>
                                    <div id="ocrDetail_inter" class="text-muted small mt-1" style="font-size:0.68rem;">Target: Roll No &amp; Board Match</div>
                                </td>
                                <td class="text-center">
                                    <div id="previewBox_inter">
                                        <span class="text-muted small" style="font-size:0.72rem;">No preview</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button type="button" id="btnRemove_inter" class="btn btn-sm btn-outline-danger py-0 px-2 d-none" style="font-size:0.75rem;" onclick="removeDoc('inter')" title="Remove file">
                                        <i class="fas fa-trash-alt me-1"></i> Remove
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between mt-2">
                    <button type="button" class="btn salu-btn-pill-outline px-4 py-1" onclick="goToStep(2)" style="font-size:0.88rem;">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </button>
                    <button type="button" class="btn salu-btn-pill-blue px-4 py-1" onclick="validateAndNext(3)" style="font-size:0.88rem;">
                        Proceed to Review &amp; Submit <i class="fas fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 4: REVIEW & DECLARATION -->
            <div class="salu-step-pane" id="stepPane4">
                <h4 class="salu-wizard-section-title mb-2">
                    <i class="fas fa-clipboard-check text-primary"></i> Step 4: Review Application &amp; Final Declaration
                </h4>

                <!-- PERSONAL & PROGRAM EXECUTIVE DOSSIER CARD (GLOWING COMPACT) -->
                <div class="salu-preview-dossier mb-2">
                    <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom" style="border-color: rgba(212, 175, 55, 0.3) !important;">
                        <div>
                            <span class="badge bg-warning text-dark px-2 py-1 rounded-pill fw-bold" style="font-size: 0.68rem; letter-spacing: 0.5px;">
                                <i class="fas fa-shield-alt me-1"></i> OFFICIAL APPLICANT DOSSIER
                            </span>
                            <h5 class="fw-bold text-white mb-0 mt-1" style="font-size: 1rem; letter-spacing: 0.2px;">
                                Personal &amp; Admission Summary
                            </h5>
                        </div>
                        <div class="text-end">
                            <span class="salu-preview-label">ENROLLMENT STATUS</span>
                            <span class="badge bg-success-subtle text-success border border-success px-2 py-1 rounded-pill fw-bold" style="font-size: 0.72rem;">
                                <i class="fas fa-check-circle me-1"></i> Ready for Submission
                            </span>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6">
                            <span class="salu-preview-label">STUDENT FULL NAME</span>
                            <strong id="revName" class="salu-preview-val text-white">{{ auth()->user()->full_name }}</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="salu-preview-label">FATHER'S NAME</span>
                            <strong id="revFather" class="salu-preview-val text-white">-</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="salu-preview-label">ADMITTED COLLEGE / INSTITUTE</span>
                            <strong id="revCollege" class="salu-preview-val" style="color: #38bdf8 !important; text-shadow: 0 0 10px rgba(56, 189, 248, 0.35);">-</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="salu-preview-label">ENROLLED DEGREE PROGRAM &amp; SESSION</span>
                            <strong id="revProgram" class="salu-preview-val" style="color: #4ade80 !important; text-shadow: 0 0 10px rgba(74, 222, 128, 0.35);">-</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="salu-preview-label">NATIONAL CNIC / B-FORM</span>
                            <strong id="revCnic" class="salu-preview-val font-monospace text-white">{{ auth()->user()->cnic }}</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="salu-preview-label">DOMICILE DISTRICT &amp; PROVINCE</span>
                            <strong id="revDomicile" class="salu-preview-val text-white">-</strong>
                        </div>
                    </div>
                </div>

                <!-- ACADEMIC QUALIFICATIONS PREVIEW TABLE (GLOWING CARD) -->
                <div class="salu-glow-card mb-2">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h5 class="fw-bold text-dark mb-0" style="font-size:0.88rem;">
                            <i class="fas fa-graduation-cap text-primary me-1"></i> Previous Academic Record Verification
                        </h5>
                        <span class="badge bg-primary-subtle text-primary border border-primary px-2 py-1 rounded-pill fw-bold" style="font-size: 0.68rem;">
                            <i class="fas fa-check-double me-1"></i> Computed Scores
                        </span>
                    </div>
                    <div class="table-responsive rounded-3 border">
                        <table class="table table-sm table-striped align-middle mb-0">
                            <thead style="background:#091338; color:#ffffff;">
                                <tr>
                                    <th style="padding:0.45rem 0.5rem; font-size:0.72rem;">Level</th>
                                    <th style="padding:0.45rem 0.5rem; font-size:0.72rem;">Group / Discipline</th>
                                    <th style="padding:0.45rem 0.5rem; font-size:0.72rem;">Board / University</th>
                                    <th style="padding:0.45rem 0.5rem; text-align:center; font-size:0.72rem;">Year</th>
                                    <th style="padding:0.45rem 0.5rem; text-align:center; font-size:0.72rem;">Roll No</th>
                                    <th style="padding:0.45rem 0.5rem; text-align:center; font-size:0.72rem;">Marks (Obt / Total)</th>
                                    <th style="padding:0.45rem 0.5rem; text-align:center; font-size:0.72rem;">Percentage</th>
                                    <th style="padding:0.45rem 0.5rem; text-align:center; font-size:0.72rem;">Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-primary px-2 py-1 rounded-pill">SSC Matric (10th)</span></td>
                                    <td id="revMatricGroup" class="fw-bold text-dark">-</td>
                                    <td id="revMatricBoard">-</td>
                                    <td id="revMatricYear" class="text-center">-</td>
                                    <td id="revMatricRoll" class="text-center font-monospace">-</td>
                                    <td id="revMatricMarks" class="text-center fw-bold text-primary">-</td>
                                    <td class="text-center"><span class="badge bg-success-subtle text-success border border-success fw-bold px-2 py-1" id="revMatricPct" style="box-shadow: 0 0 8px rgba(16, 185, 129, 0.2); font-size:0.75rem;">-</span></td>
                                    <td class="text-center"><strong id="revMatricGrade" class="badge bg-dark text-white px-2 py-1" style="font-size:0.75rem;">-</strong></td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-success px-2 py-1 rounded-pill">HSC Inter (12th)</span></td>
                                    <td id="revInterGroup" class="fw-bold text-dark">-</td>
                                    <td id="revInterBoard">-</td>
                                    <td id="revInterYear" class="text-center">-</td>
                                    <td id="revInterRoll" class="text-center font-monospace">-</td>
                                    <td id="revInterMarks" class="text-center fw-bold text-primary">-</td>
                                    <td class="text-center"><span class="badge bg-success-subtle text-success border border-success fw-bold px-2 py-1" id="revInterPct" style="box-shadow: 0 0 8px rgba(16, 185, 129, 0.2); font-size:0.75rem;">-</span></td>
                                    <td class="text-center"><strong id="revInterGrade" class="badge bg-dark text-white px-2 py-1" style="font-size:0.75rem;">-</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ATTACHED DOCUMENTS PREVIEW TABLE (GLOWING CARD) -->
                <div class="salu-glow-card mb-2">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h5 class="fw-bold text-dark mb-0" style="font-size:0.88rem;">
                            <i class="fas fa-folder-open text-primary me-1"></i> Attached Mandatory Documents
                        </h5>
                        <span class="badge bg-success-subtle text-success border border-success px-2 py-1 rounded-pill fw-bold" style="font-size: 0.68rem;">
                            <i class="fas fa-shield-check me-1"></i> All Files Attached
                        </span>
                    </div>
                    <div class="table-responsive rounded-3 border">
                        <table class="table table-sm table-striped align-middle mb-0">
                            <thead style="background:#091338; color:#ffffff;">
                                <tr>
                                    <th style="padding:0.45rem 0.75rem; font-size:0.72rem;">Document Name</th>
                                    <th style="padding:0.45rem 0.5rem; font-size:0.72rem;">Uploaded File</th>
                                    <th style="padding:0.45rem 0.5rem; text-align:center; font-size:0.72rem;">Size</th>
                                    <th style="padding:0.45rem 0.5rem; text-align:center; font-size:0.72rem;">OCR Status</th>
                                    <th style="padding:0.45rem 0.5rem; text-align:center; font-size:0.72rem;">Preview</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding-left:0.75rem;"><strong class="text-dark" style="font-size:0.82rem;">CNIC / B-Form Copy</strong></td>
                                    <td id="revDocName_cnic" class="font-monospace text-primary small">-</td>
                                    <td id="revDocSize_cnic" class="text-center small">-</td>
                                    <td id="revDocOcr_cnic" class="text-center">-</td>
                                    <td class="text-center" id="revDocAction_cnic">-</td>
                                </tr>
                                <tr>
                                    <td style="padding-left:0.75rem;"><strong class="text-dark" style="font-size:0.82rem;">Matric Marksheet Copy</strong></td>
                                    <td id="revDocName_matric" class="font-monospace text-primary small">-</td>
                                    <td id="revDocSize_matric" class="text-center small">-</td>
                                    <td id="revDocOcr_matric" class="text-center">-</td>
                                    <td class="text-center" id="revDocAction_matric">-</td>
                                </tr>
                                <tr>
                                    <td style="padding-left:0.75rem;"><strong class="text-dark" style="font-size:0.82rem;">Intermediate Marksheet Copy</strong></td>
                                    <td id="revDocName_inter" class="font-monospace text-primary small">-</td>
                                    <td id="revDocSize_inter" class="text-center small">-</td>
                                    <td id="revDocOcr_inter" class="text-center">-</td>
                                    <td class="text-center" id="revDocAction_inter">-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- GLOWING DECLARATION CHECKBOX -->
                <div class="salu-glow-declaration mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="declarationCheck" style="width: 18px; height: 18px; margin-top: 2px;" required />
                        <label class="form-check-label small fw-bold text-dark ps-2" for="declarationCheck" style="line-height: 1.4; font-size:0.78rem;">
                            I solemnly declare that all particulars entered and documents uploaded in this enrollment application are genuine and correct to the best of my knowledge. I agree to abide by the rules and regulations of Shah Abdul Latif University, Khairpur.
                        </label>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-2">
                    <button type="button" class="btn salu-btn-pill-outline px-4 py-1" onclick="goToStep(3)" style="font-size:0.88rem;">
                        <i class="fas fa-arrow-left me-1"></i> Back to Uploads
                    </button>
                    <button type="submit" class="btn salu-btn-pill-green px-5 py-1" id="submitBtn" style="font-size:0.92rem;">
                        <i class="fas fa-check-circle me-1"></i> Submit Enrollment Application
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<!-- DOCUMENT PREVIEW MODAL -->
<div class="modal fade" id="docPreviewModal" tabindex="-1" aria-labelledby="docPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fs-6 fw-bold" id="docPreviewModalLabel"><i class="fas fa-eye me-2"></i> Document Preview</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3" id="docPreviewModalBody" style="min-height: 320px; background: #f8fafc;">
                <!-- Dynamically loaded preview image / iframe -->
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-sm btn-secondary px-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Tesseract.js for client-side OCR -->
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>

<script>
    let currentStep = 1;

    // Dynamic District -> College -> Program structure from CSV
    const districtCollegeProgramData = @json($districtCollegeProgramData ?? []);

    function onDistrictChange(selectedCollegeName = null, selectedProgram = null) {
        const distSelect = document.getElementById('inpDistrict');
        const colSelect = document.getElementById('inpCollege');
        const progSelect = document.getElementById('inpProgram');
        const dist = distSelect.value;

        colSelect.innerHTML = '<option value="">-- Select College --</option>';
        progSelect.innerHTML = '<option value="">-- Select Program --</option>';

        if (!dist || !districtCollegeProgramData[dist]) return;

        const collegesInDist = districtCollegeProgramData[dist];
        collegesInDist.forEach(col => {
            const opt = document.createElement('option');
            opt.value = col.id || col.name;
            opt.textContent = col.name;
            opt.dataset.collegeName = col.name;
            opt.dataset.programs = JSON.stringify(col.programs || []);
            if (selectedCollegeName && (col.name === selectedCollegeName || col.id === selectedCollegeName)) {
                opt.selected = true;
            }
            colSelect.appendChild(opt);
        });

        // Trigger college change
        onCollegeChange(selectedProgram);
    }

    function onCollegeChange(selectedProgram = null) {
        const colSelect = document.getElementById('inpCollege');
        const progSelect = document.getElementById('inpProgram');
        progSelect.innerHTML = '<option value="">-- Select Program --</option>';

        const selectedOpt = colSelect.options[colSelect.selectedIndex];
        if (!selectedOpt || !selectedOpt.dataset.programs) return;

        try {
            const programs = JSON.parse(selectedOpt.dataset.programs);
            programs.forEach(prog => {
                const opt = document.createElement('option');
                opt.value = prog;
                opt.textContent = prog;
                if (selectedProgram && prog === selectedProgram) {
                    opt.selected = true;
                }
                progSelect.appendChild(opt);
            });
        } catch(e) {
            console.error('Failed to parse college programs', e);
        }
    }

    // Auto-init on page load
    document.addEventListener('DOMContentLoaded', function() {
        onDistrictChange('Govt: Degree College Thari Mirwah', 'ADS');
    });

    // Track uploaded documents for duplicate prevention & OCR
    const uploadedDocs = {
        cnic: null,
        matric: null,
        inter: null
    };

    function goToStep(step) {
        if (step < 1 || step > 4) return;
        
        // Hide all panes
        document.querySelectorAll('.salu-step-pane').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.salu-step-item').forEach((el, idx) => {
            el.classList.remove('active');
            if (idx + 1 < step) {
                el.classList.add('completed');
            } else {
                el.classList.remove('completed');
            }
        });

        // Show active pane
        const activePane = document.getElementById('stepPane' + step);
        if (activePane) activePane.classList.add('active');

        // Update stepper indicator
        const activeStepBtn = document.querySelectorAll('.salu-step-item')[step - 1];
        if (activeStepBtn) activeStepBtn.classList.add('active');

        const trackFill = document.getElementById('trackFill');
        if (trackFill) {
            trackFill.style.width = ((step - 1) / 3 * 100) + '%';
        }

        currentStep = step;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function calculateMarks(prefix) {
        const totalEl = document.getElementById('inp' + prefix + 'Total');
        const obtEl = document.getElementById('inp' + prefix + 'Obtained');
        const pctEl = document.getElementById('inp' + prefix + 'Pct');
        const gradeEl = document.getElementById('inp' + prefix + 'Grade');

        if (!totalEl || !obtEl || !pctEl) return;

        totalEl.value = totalEl.value.replace(/\D/g, '');
        obtEl.value = obtEl.value.replace(/\D/g, '');

        let total = parseInt(totalEl.value) || 0;
        let obt = parseInt(obtEl.value) || 0;

        if (total <= 0) {
            total = 1100;
            totalEl.value = 1100;
        }

        if (obt > total) {
            alert('Marks obtained (' + obt + ') cannot exceed Total Marks (' + total + ') for ' + prefix + '!');
            obt = total;
            obtEl.value = total;
        }

        const pct = total > 0 ? (obt / total) * 100 : 0;
        pctEl.value = pct.toFixed(2) + '%';

        if (gradeEl) {
            if (pct >= 80) gradeEl.value = 'A-1';
            else if (pct >= 70) gradeEl.value = 'A';
            else if (pct >= 60) gradeEl.value = 'B';
            else if (pct >= 50) gradeEl.value = 'C';
            else if (pct >= 40) gradeEl.value = 'D';
            else gradeEl.value = 'E';
        }
    }

    // STRICT DUPLICATE FILE NAME PREVENTION & OCR MATCHING
    function handleDocUpload(docType, input) {
        if (!input.files || !input.files[0]) return;

        const file = input.files[0];
        const newFileName = file.name.trim().toLowerCase();

        // 1. STRICT DUPLICATE FILE NAME CHECK ACROSS ALL OTHER INPUTS
        const docKeys = ['cnic', 'matric', 'inter'];
        for (const key of docKeys) {
            if (key !== docType) {
                // Check registry
                if (uploadedDocs[key] && uploadedDocs[key].name.trim().toLowerCase() === newFileName) {
                    alert('⚠️ Duplicate File Upload Blocked!\n\nYou cannot upload the file "' + file.name + '" more than once.\nIt has already been selected for ' + getDocLabel(key) + '.\nPlease upload a separate, distinct file.');
                    input.value = '';
                    document.getElementById('fileName_' + docType).textContent = 'No file chosen';
                    return;
                }
                // Check file element directly
                const otherInput = document.getElementById('docInput_' + key);
                if (otherInput && otherInput.files && otherInput.files[0]) {
                    if (otherInput.files[0].name.trim().toLowerCase() === newFileName) {
                        alert('⚠️ Duplicate File Upload Blocked!\n\nYou cannot upload the file "' + file.name + '" more than once.\nIt has already been selected for ' + getDocLabel(key) + '.\nPlease upload a separate, distinct file.');
                        input.value = '';
                        document.getElementById('fileName_' + docType).textContent = 'No file chosen';
                        return;
                    }
                }
            }
        }

        // File size validation (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('File size exceeds 2MB limit. Please upload a compressed document.');
            input.value = '';
            document.getElementById('fileName_' + docType).textContent = 'No file chosen';
            return;
        }

        // 2. Set UI display
        document.getElementById('fileName_' + docType).textContent = file.name;
        document.getElementById('btnRemove_' + docType).classList.remove('d-none');

        const fileSizeKB = (file.size / 1024).toFixed(1) + ' KB';
        const isImage = file.type.startsWith('image/');
        const isPdf = file.type === 'application/pdf';

        const reader = new FileReader();
        reader.onload = function(e) {
            const dataUrl = e.target.result;

            uploadedDocs[docType] = {
                name: file.name,
                size: fileSizeKB,
                type: file.type,
                dataUrl: dataUrl,
                ocrStatus: 'Analyzing...'
            };

            // Render Preview Thumbnail
            const previewBox = document.getElementById('previewBox_' + docType);
            if (isImage) {
                previewBox.innerHTML = `
                    <div class="position-relative d-inline-block">
                        <img src="${dataUrl}" class="rounded border shadow-sm" style="width: 52px; height: 52px; object-fit: cover; cursor: pointer;" onclick="viewDocModal('${docType}')" title="Click to view full size" />
                        <span class="badge bg-dark position-absolute bottom-0 end-0" style="font-size:0.55rem; padding: 2px 4px;"><i class="fas fa-search-plus"></i></span>
                    </div>
                `;
            } else if (isPdf) {
                previewBox.innerHTML = `
                    <div class="d-inline-flex flex-column align-items-center cursor-pointer" onclick="viewDocModal('${docType}')" title="Click to view PDF">
                        <i class="fas fa-file-pdf fa-2x text-danger"></i>
                        <span class="badge bg-secondary-subtle text-secondary mt-1" style="font-size:0.6rem;">PDF</span>
                    </div>
                `;
            }

            // 3. OCR READING & DATA MATCHING (NO "VERIFIED" TEXT)
            runOcrReading(docType, file, dataUrl);
        };
        reader.readAsDataURL(file);
    }

    function removeDoc(docType) {
        document.getElementById('docInput_' + docType).value = '';
        document.getElementById('fileName_' + docType).textContent = 'No file chosen';
        document.getElementById('btnRemove_' + docType).classList.add('d-none');
        document.getElementById('previewBox_' + docType).innerHTML = '<span class="text-muted small" style="font-size:0.75rem;">No preview</span>';
        document.getElementById('ocrStatus_' + docType).innerHTML = '<span class="badge bg-secondary-subtle text-secondary px-2 py-1"><i class="fas fa-clock me-1"></i> Pending Upload</span>';

        uploadedDocs[docType] = null;
    }

    function getDocLabel(docType) {
        if (docType === 'cnic') return 'CNIC Copy';
        if (docType === 'matric') return 'Matric Marksheet';
        if (docType === 'inter') return 'Intermediate Marksheet';
        return 'Document';
    }

    // OCR TEXT SCANNER & FORM MATCHER (WITHOUT "VERIFIED" LABELS)
    function runOcrReading(docType, file, dataUrl) {
        const ocrStatusEl = document.getElementById('ocrStatus_' + docType);
        ocrStatusEl.innerHTML = '<span class="badge bg-warning text-dark px-2 py-1"><i class="fas fa-spinner fa-spin me-1"></i> Scanning OCR...</span>';

        const cnicClean = (document.getElementById('inpCnic').value || '').replace(/\D/g, '');
        const fullName = (document.getElementById('inpFullName').value || '').toLowerCase().trim();
        const matricRoll = (document.getElementById('inpMatricRoll') ? document.getElementById('inpMatricRoll').value.trim() : '');
        const interRoll = (document.getElementById('inpInterRoll') ? document.getElementById('inpInterRoll').value.trim() : '');

        if (!file.type.startsWith('image/')) {
            // PDF document attached
            setTimeout(() => {
                ocrStatusEl.innerHTML = '<span class="badge bg-primary-subtle text-primary border border-primary px-2 py-1"><i class="fas fa-file-pdf me-1"></i> PDF Document Attached</span>';
                if (uploadedDocs[docType]) {
                    uploadedDocs[docType].ocrStatus = 'PDF Document Attached';
                }
            }, 500);
            return;
        }

        // If Tesseract is loaded, scan document text
        if (typeof Tesseract !== 'undefined') {
            Tesseract.recognize(dataUrl, 'eng', {
                logger: m => {}
            }).then(({ data: { text } }) => {
                const scanned = (text || '').toLowerCase();
                const scannedDigits = (text || '').replace(/\D/g, '');

                let matchFound = false;
                let matchReason = '';

                if (docType === 'cnic') {
                    if (cnicClean && scannedDigits.includes(cnicClean)) {
                        matchFound = true;
                        matchReason = 'CNIC Match Found';
                    } else if (fullName && scanned.includes(fullName.split(' ')[0])) {
                        matchFound = true;
                        matchReason = 'Name Match Found';
                    }
                } else if (docType === 'matric') {
                    if (matricRoll && scannedDigits.includes(matricRoll)) {
                        matchFound = true;
                        matchReason = 'SSC Roll Match: ' + matricRoll;
                    } else if (scanned.includes('sukkur') || scanned.includes('board') || scanned.includes('secondary') || scanned.includes('matric')) {
                        matchFound = true;
                        matchReason = 'Board Document Detected';
                    }
                } else if (docType === 'inter') {
                    if (interRoll && scannedDigits.includes(interRoll)) {
                        matchFound = true;
                        matchReason = 'HSC Roll Match: ' + interRoll;
                    } else if (scanned.includes('intermediate') || scanned.includes('higher') || scanned.includes('board') || scanned.includes('science')) {
                        matchFound = true;
                        matchReason = 'HSC Document Detected';
                    }
                }

                if (matchFound) {
                    ocrStatusEl.innerHTML = `<span class="badge bg-success px-2 py-1"><i class="fas fa-check-circle me-1"></i> ${matchReason}</span>`;
                    if (uploadedDocs[docType]) {
                        uploadedDocs[docType].ocrStatus = matchReason;
                    }
                } else {
                    ocrStatusEl.innerHTML = `<span class="badge bg-info-subtle text-dark border px-2 py-1"><i class="fas fa-file-alt me-1"></i> Document Attached</span>`;
                    if (uploadedDocs[docType]) {
                        uploadedDocs[docType].ocrStatus = 'Document Attached';
                    }
                }
            }).catch(err => {
                ocrStatusEl.innerHTML = `<span class="badge bg-info-subtle text-dark border px-2 py-1"><i class="fas fa-file-alt me-1"></i> Document Attached</span>`;
                if (uploadedDocs[docType]) {
                    uploadedDocs[docType].ocrStatus = 'Document Attached';
                }
            });
        } else {
            setTimeout(() => {
                ocrStatusEl.innerHTML = `<span class="badge bg-info-subtle text-dark border px-2 py-1"><i class="fas fa-file-alt me-1"></i> Document Attached</span>`;
                if (uploadedDocs[docType]) {
                    uploadedDocs[docType].ocrStatus = 'Document Attached';
                }
            }, 400);
        }
    }

    function viewDocModal(docType) {
        const doc = uploadedDocs[docType];
        if (!doc) return;

        document.getElementById('docPreviewModalLabel').innerHTML = '<i class="fas fa-eye me-2"></i> ' + getDocLabel(docType) + ' &bull; ' + doc.name;
        const modalBody = document.getElementById('docPreviewModalBody');

        if (doc.type && doc.type.startsWith('image/')) {
            modalBody.innerHTML = `<img src="${doc.dataUrl}" class="img-fluid rounded shadow-sm" style="max-height: 540px;" alt="Document Preview" />`;
        } else {
            modalBody.innerHTML = `<iframe src="${doc.dataUrl}" style="width: 100%; height: 500px; border: none;" class="rounded"></iframe>`;
        }

        const modal = new bootstrap.Modal(document.getElementById('docPreviewModal'));
        modal.show();
    }

    function validateAndNext(step) {
        if (step === 1) {
            const district = document.getElementById('inpDistrict').value;
            const college = document.getElementById('inpCollege').value;
            const program = document.getElementById('inpProgram').value;
            const father = document.getElementById('inpFatherName').value;
            const contact = document.getElementById('inpContact').value;
            const address = document.getElementById('inpAddress').value;

            if (!district) {
                alert('Please select College District');
                document.getElementById('inpDistrict').focus();
                return;
            }
            if (!college) {
                alert('Please select Admitted College');
                document.getElementById('inpCollege').focus();
                return;
            }
            if (!program) {
                alert('Please select Degree Program');
                document.getElementById('inpProgram').focus();
                return;
            }
            if (!father.trim()) {
                alert("Please enter Father's Name");
                document.getElementById('inpFatherName').focus();
                return;
            }
            if (!contact.trim()) {
                alert("Please enter Mobile Contact Number");
                document.getElementById('inpContact').focus();
                return;
            }
            if (!address.trim()) {
                alert("Please enter Residential Address");
                document.getElementById('inpAddress').focus();
                return;
            }
            goToStep(2);
        } else if (step === 2) {
            const matricRoll = document.getElementById('inpMatricRoll').value.trim();
            const matricTotal = parseInt(document.getElementById('inpMatricTotal').value) || 0;
            const matricObtained = parseInt(document.getElementById('inpMatricObtained').value) || 0;

            const interRoll = document.getElementById('inpInterRoll').value.trim();
            const interTotal = parseInt(document.getElementById('inpInterTotal').value) || 0;
            const interObtained = parseInt(document.getElementById('inpInterObtained').value) || 0;

            if (!matricRoll) {
                alert('Please enter Matric (SSC) Roll / Seat Number');
                document.getElementById('inpMatricRoll').focus();
                return;
            }
            if (matricTotal <= 0) {
                alert('Please enter valid Matric Total Marks');
                document.getElementById('inpMatricTotal').focus();
                return;
            }
            if (matricObtained <= 0 || matricObtained > matricTotal) {
                alert('Matric Marks obtained must be greater than 0 and cannot exceed Total Marks (' + matricTotal + ')');
                document.getElementById('inpMatricObtained').focus();
                return;
            }

            if (!interRoll) {
                alert('Please enter Intermediate (HSC) Roll / Seat Number');
                document.getElementById('inpInterRoll').focus();
                return;
            }
            if (interTotal <= 0) {
                alert('Please enter valid Intermediate Total Marks');
                document.getElementById('inpInterTotal').focus();
                return;
            }
            if (interObtained <= 0 || interObtained > interTotal) {
                alert('Intermediate Marks obtained must be greater than 0 and cannot exceed Total Marks (' + interTotal + ')');
                document.getElementById('inpInterObtained').focus();
                return;
            }

            goToStep(3);
        } else if (step === 3) {
            // Validate all 3 mandatory documents
            const cnicFile = document.getElementById('docInput_cnic').files[0];
            const matricFile = document.getElementById('docInput_matric').files[0];
            const interFile = document.getElementById('docInput_inter').files[0];

            if (!cnicFile) {
                alert('Mandatory Document Missing: Please upload your CNIC / B-Form copy.');
                return;
            }
            if (!matricFile) {
                alert('Mandatory Document Missing: Please upload your Matric (SSC) Marksheet copy.');
                return;
            }
            if (!interFile) {
                alert('Mandatory Document Missing: Please upload your Intermediate (HSC) Marksheet copy.');
                return;
            }

            // Strict duplicate file check
            const cnicName = cnicFile.name.trim().toLowerCase();
            const matricName = matricFile.name.trim().toLowerCase();
            const interName = interFile.name.trim().toLowerCase();

            if (cnicName === matricName || cnicName === interName || matricName === interName) {
                alert('⚠️ Duplicate File Error: All 3 uploaded documents must have distinct, separate file names.\nYou cannot upload the same file more than once.');
                return;
            }

            populateReview();
            goToStep(4);
        }
    }

    function populateReview() {
        // Step 1 Details
        const district = document.getElementById('inpDistrict').value || '-';
        const collegeEl = document.getElementById('inpCollege');
        const collegeText = collegeEl.options[collegeEl.selectedIndex]?.text || '-';
        const program = document.getElementById('inpProgram').value;
        const session = document.getElementById('inpSession').value;
        const father = document.getElementById('inpFatherName').value;
        const domicile = document.getElementById('inpDomicileDist').value;

        document.getElementById('revFather').textContent = father;
        document.getElementById('revCollege').textContent = collegeText + ' (' + district + ')';
        document.getElementById('revProgram').textContent = program + ' (' + session + ')';
        document.getElementById('revDomicile').textContent = domicile + ', Sindh';

        // Step 2 Matric Details
        document.getElementById('revMatricGroup').textContent = document.getElementById('inpMatricGroup').value;
        document.getElementById('revMatricBoard').textContent = document.getElementById('inpMatricBoard').value;
        document.getElementById('revMatricYear').textContent = document.getElementById('inpMatricYear').value;
        document.getElementById('revMatricRoll').textContent = document.getElementById('inpMatricRoll').value;
        document.getElementById('revMatricMarks').textContent = document.getElementById('inpMatricObtained').value + ' / ' + document.getElementById('inpMatricTotal').value;
        document.getElementById('revMatricPct').textContent = document.getElementById('inpMatricPct').value;
        document.getElementById('revMatricGrade').textContent = document.getElementById('inpMatricGrade').value;

        // Step 2 Intermediate Details
        document.getElementById('revInterGroup').textContent = document.getElementById('inpInterGroup').value;
        document.getElementById('revInterBoard').textContent = document.getElementById('inpInterBoard').value;
        document.getElementById('revInterYear').textContent = document.getElementById('inpInterYear').value;
        document.getElementById('revInterRoll').textContent = document.getElementById('inpInterRoll').value;
        document.getElementById('revInterMarks').textContent = document.getElementById('inpInterObtained').value + ' / ' + document.getElementById('inpInterTotal').value;
        document.getElementById('revInterPct').textContent = document.getElementById('inpInterPct').value;
        document.getElementById('revInterGrade').textContent = document.getElementById('inpInterGrade').value;

        // Step 3 Documents Preview in Step 4
        ['cnic', 'matric', 'inter'].forEach(docType => {
            const doc = uploadedDocs[docType];
            if (doc) {
                document.getElementById('revDocName_' + docType).textContent = doc.name;
                document.getElementById('revDocSize_' + docType).textContent = doc.size;
                document.getElementById('revDocOcr_' + docType).innerHTML = `<span class="badge bg-primary-subtle text-primary border border-primary fw-bold px-2 py-1"><i class="fas fa-file-alt me-1"></i> ${doc.ocrStatus}</span>`;
                document.getElementById('revDocAction_' + docType).innerHTML = `<button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" onclick="viewDocModal('${docType}')"><i class="fas fa-eye me-1"></i> View</button>`;
            }
        });
    }

    function previewStudentPhoto(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('photoPreviewImg');
                const placeholder = document.getElementById('photoPlaceholder');
                img.src = e.target.result;
                img.style.display = 'block';
                placeholder.style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Auto-hyphenate contact number
    const contactInp = document.getElementById('inpContact');
    if (contactInp) {
        contactInp.addEventListener('input', function() {
            let digits = this.value.replace(/\D/g, '').substring(0, 11);
            let formatted = '';
            if (digits.length > 0) {
                formatted = digits.substring(0, 4);
                if (digits.length > 4) formatted += '-' + digits.substring(4, 11);
            }
            this.value = formatted;
        });
    }

    // Digits only enforcement on inputs with .digit-only
    document.querySelectorAll('.digit-only').forEach(function(input) {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });
    });

    // Run initial calculations on page load
    document.addEventListener('DOMContentLoaded', function() {
        calculateMarks('Matric');
        calculateMarks('Inter');
    });

    // Form submission validation
    const form = document.getElementById('enrollmentForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const dec = document.getElementById('declarationCheck');
            if (!dec.checked) {
                e.preventDefault();
                alert('Please accept the declaration before submitting.');
            }
        });
    }
</script>
@endpush
