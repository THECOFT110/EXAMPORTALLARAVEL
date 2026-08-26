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
    max-width: 1040px;
    margin: 0 auto;
}

/* STEPPER NAVIGATION BAR */
.salu-stepper-wrap {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    margin-bottom: 2rem;
    padding: 0 1rem;
}

.salu-stepper-track {
    position: absolute;
    top: 24px;
    left: 40px;
    right: 40px;
    height: 4px;
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
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #ffffff;
    border: 3px solid #cbd5e1;
    color: #64748b;
    font-weight: 800;
    font-size: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.25s ease;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}

.salu-step-item.active .salu-step-circle {
    border-color: #f27220;
    background: #f27220;
    color: #ffffff;
    box-shadow: 0 6px 16px rgba(242, 114, 32, 0.4);
    transform: scale(1.08);
}

.salu-step-item.completed .salu-step-circle {
    border-color: #10b981;
    background: #10b981;
    color: #ffffff;
}

.salu-step-title {
    font-size: 0.78rem;
    font-weight: 700;
    color: #64748b;
    margin-top: 0.5rem;
    transition: color 0.2s;
    text-align: center;
}

.salu-step-item.active .salu-step-title {
    color: #0f172a;
}

/* WIZARD FORM CARD */
.salu-wizard-card {
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    border: 1.5px solid rgba(212, 175, 55, 0.3);
    border-radius: 24px;
    box-shadow: 0 10px 35px rgba(11, 19, 61, 0.06);
    padding: 2.5rem;
    margin-bottom: 2rem;
}

.salu-wizard-section-title {
    font-size: 1.15rem;
    font-weight: 800;
    color: #091338;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.salu-form-group {
    margin-bottom: 1.25rem;
}

.salu-form-label {
    display: block;
    font-size: 0.84rem;
    font-weight: 700;
    color: #334155;
    margin-bottom: 0.4rem;
}

.salu-form-control {
    width: 100%;
    padding: 0.65rem 0.9rem;
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    font-size: 0.9rem;
    color: #0f172a;
    background: #ffffff;
    transition: all 0.2s ease;
}

.salu-form-control:focus {
    outline: none;
    border-color: #f27220;
    box-shadow: 0 0 0 3px rgba(242, 114, 32, 0.15);
}

.salu-form-control[readonly] {
    background: #f1f5f9;
    color: #475569;
    cursor: not-allowed;
}

/* PHOTO UPLOAD BOX */
.salu-photo-uploader {
    width: 160px;
    height: 190px;
    border: 2px dashed #cbd5e1;
    border-radius: 14px;
    background: #f8fafc;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: all 0.2s ease;
}

.salu-photo-uploader:hover {
    border-color: #f27220;
    background: #fffaf5;
}

.salu-photo-preview {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: none;
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
            <span class="salu-step-title">College &amp; Program</span>
        </button>

        <button type="button" class="salu-step-item" onclick="goToStep(2)">
            <div class="salu-step-circle" id="circle2">2</div>
            <span class="salu-step-title">Personal Information</span>
        </button>

        <button type="button" class="salu-step-item" onclick="goToStep(3)">
            <div class="salu-step-circle" id="circle3">3</div>
            <span class="salu-step-title">Academic History</span>
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

            <!-- STEP 1: COLLEGE & PROGRAM SELECTION -->
            <div class="salu-step-pane active" id="stepPane1">
                <h4 class="salu-wizard-section-title">
                    <i class="fas fa-building-columns text-primary"></i> Step 1: Affiliated College &amp; Academic Program
                </h4>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="salu-form-group">
                            <label class="salu-form-label">Admitted College / Department <span class="text-danger">*</span></label>
                            <select name="college_id" id="inpCollege" class="salu-form-control" required>
                                <option value="">-- Select Affiliated College / Department --</option>
                                @foreach($colleges ?? [] as $col)
                                    <option value="{{ $col->id }}" {{ old('college_id') == $col->id ? 'selected' : '' }}>
                                        {{ $col->name }} ({{ $col->district }})
                                    </option>
                                @endforeach
                            </select>
                            @error('college_id')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="salu-form-group">
                            <label class="salu-form-label">Degree Program <span class="text-danger">*</span></label>
                            <select name="program" id="inpProgram" class="salu-form-control" required>
                                <option value="">-- Select Degree Program --</option>
                                <optgroup label="Faculty of Natural Sciences">
                                    <option value="BS (4 Years) Computer Science">BS (4 Years) Computer Science</option>
                                    <option value="BS (4 Years) Information Technology">BS (4 Years) Information Technology</option>
                                    <option value="BS (4 Years) Software Engineering">BS (4 Years) Software Engineering</option>
                                    <option value="BS (4 Years) Chemistry">BS (4 Years) Chemistry</option>
                                    <option value="BS (4 Years) Mathematics">BS (4 Years) Mathematics</option>
                                    <option value="BS (4 Years) Physics">BS (4 Years) Physics</option>
                                    <option value="Associate Degree in Science (ADS)">Associate Degree in Science (ADS)</option>
                                </optgroup>
                                <optgroup label="Faculty of Management &amp; Commerce">
                                    <option value="BBA (Hons) Business Administration">BBA (Hons) Business Administration</option>
                                    <option value="Associate Degree in Commerce (ADC)">Associate Degree in Commerce (ADC)</option>
                                    <option value="BS (4 Years) Public Administration">BS (4 Years) Public Administration</option>
                                </optgroup>
                                <optgroup label="Faculty of Arts &amp; Humanities">
                                    <option value="BS (4 Years) English Literature & Linguistics">BS (4 Years) English Literature &amp; Linguistics</option>
                                    <option value="Associate Degree in Arts (ADA)">Associate Degree in Arts (ADA)</option>
                                    <option value="BS (4 Years) Sindhi">BS (4 Years) Sindhi</option>
                                    <option value="BS (4 Years) International Relations">BS (4 Years) International Relations</option>
                                </optgroup>
                                <optgroup label="Faculty of Law &amp; Education">
                                    <option value="LLB (5 Years) Law">LLB (5 Years) Law</option>
                                    <option value="B.Ed (Hons) Education (4 Years)">B.Ed (Hons) Education (4 Years)</option>
                                </optgroup>
                            </select>
                            @error('program')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="salu-form-group">
                            <label class="salu-form-label">Academic Session</label>
                            <input name="session" id="inpSession" value="{{ $activeYear->name ?? '2024-2025' }}" class="salu-form-control" readonly />
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="salu-form-group">
                            <label class="salu-form-label">Enrolled Semester <span class="text-danger">*</span></label>
                            <select name="semester" id="inpSemester" class="salu-form-control" required>
                                <option value="1st Semester" selected>1st Semester</option>
                                <option value="2nd Semester">2nd Semester</option>
                                <option value="3rd Semester">3rd Semester</option>
                                <option value="4th Semester">4th Semester</option>
                                <option value="5th Semester">5th Semester</option>
                                <option value="6th Semester">6th Semester</option>
                                <option value="7th Semester">7th Semester</option>
                                <option value="8th Semester">8th Semester</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="button" class="btn salu-btn-pill-blue px-4" onclick="validateAndNext(1)">
                        Proceed to Personal Info <i class="fas fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 2: PERSONAL PARTICULARS & PASSPORT PHOTO -->
            <div class="salu-step-pane" id="stepPane2">
                <h4 class="salu-wizard-section-title">
                    <i class="fas fa-user-circle text-primary"></i> Step 2: Personal Particulars &amp; Photograph
                </h4>

                <div class="row g-4 mb-4">
                    <div class="col-md-8">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="salu-form-group">
                                    <label class="salu-form-label">Full Name <span class="text-danger">*</span></label>
                                    <input name="full_name" id="inpFullName" value="{{ auth()->user()->full_name }}" class="salu-form-control" readonly />
                                    <small class="text-muted" style="font-size:0.7rem;"><i class="fas fa-lock me-1"></i>From verified portal profile</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="salu-form-group">
                                    <label class="salu-form-label">Father's Name <span class="text-danger">*</span></label>
                                    <input name="father_name" id="inpFatherName" value="{{ auth()->user()->father_name }}" class="salu-form-control" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="salu-form-group">
                                    <label class="salu-form-label">Relation <span class="text-danger">*</span></label>
                                    <select name="so_do_wo" id="inpRelation" class="salu-form-control" required>
                                        <option value="S/o">S/O (Son of)</option>
                                        <option value="D/o">D/O (Daughter of)</option>
                                        <option value="W/o">W/O (Wife of)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="salu-form-group">
                                    <label class="salu-form-label">Surname / Family Cast</label>
                                    <input name="surname" id="inpSurname" class="salu-form-control" placeholder="e.g. Soomro, Kalhoro, Memon" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="salu-form-group">
                                    <label class="salu-form-label">National CNIC / B-Form <span class="text-danger">*</span></label>
                                    <input name="cnic" id="inpCnic" value="{{ auth()->user()->cnic }}" class="salu-form-control" readonly />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="salu-form-group">
                                    <label class="salu-form-label">Gender <span class="text-danger">*</span></label>
                                    <select name="gender" id="inpGender" class="salu-form-control" required>
                                        <option value="MALE">Male</option>
                                        <option value="FEMALE">Female</option>
                                        <option value="OTHER">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="salu-form-group">
                                    <label class="salu-form-label">Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" name="dob" id="inpDob" value="2004-05-15" class="salu-form-control" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="salu-form-group">
                                    <label class="salu-form-label">Mobile Contact Number <span class="text-danger">*</span></label>
                                    <input name="contact_number" id="inpContact" value="{{ auth()->user()->phone }}" class="salu-form-control" placeholder="0300-0000000" maxlength="12" required />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PHOTO UPLOAD PREVIEW -->
                    <div class="col-md-4 d-flex flex-column align-items-center justify-content-center text-center">
                        <label class="salu-form-label mb-2">Passport Photograph <span class="text-danger">*</span></label>
                        <div class="salu-photo-uploader" onclick="document.getElementById('photoInput').click()">
                            <img id="photoPreviewImg" class="salu-photo-preview" alt="Student Photo" />
                            <div id="photoPlaceholder" class="p-3">
                                <i class="fas fa-camera fa-2x text-muted mb-2"></i>
                                <span class="d-block small text-muted fw-bold">Upload Photo</span>
                                <span class="text-muted" style="font-size:0.65rem;">Passport size (Max 2MB)</span>
                            </div>
                        </div>
                        <input type="file" name="photo" id="photoInput" accept="image/*" class="d-none" onchange="previewStudentPhoto(this)" />
                        <small class="text-muted mt-2" style="font-size:0.7rem;">Clear frontal view with plain background</small>
                    </div>

                    <div class="col-md-6">
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

                    <div class="col-md-6">
                        <div class="salu-form-group">
                            <label class="salu-form-label">Domicile Province <span class="text-danger">*</span></label>
                            <input name="domicile_province" id="inpDomicileProv" value="Sindh" class="salu-form-control" required />
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="salu-form-group">
                            <label class="salu-form-label">Permanent Residential Address <span class="text-danger">*</span></label>
                            <textarea name="address" id="inpAddress" class="salu-form-control" rows="2" placeholder="House No, Street, Mohalla, City/Village" required>House No. 12, Station Road, Khairpur Mirs</textarea>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="button" class="btn salu-btn-pill-outline px-4" onclick="goToStep(1)">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </button>
                    <button type="button" class="btn salu-btn-pill-blue px-4" onclick="validateAndNext(2)">
                        Proceed to Academic History <i class="fas fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 3: PREVIOUS ACADEMIC RECORDS -->
            <div class="salu-step-pane" id="stepPane3">
                <h4 class="salu-wizard-section-title">
                    <i class="fas fa-book-bookmark text-primary"></i> Step 3: Previous Academic Qualifications
                </h4>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="salu-form-group">
                            <label class="salu-form-label">Last Examination Passed <span class="text-danger">*</span></label>
                            <select name="last_exam" id="inpLastExam" class="salu-form-control" required>
                                <option value="HSC / Intermediate (Pre-Engineering)" selected>HSC / Intermediate (Pre-Engineering)</option>
                                <option value="HSC / Intermediate (Pre-Medical)">HSC / Intermediate (Pre-Medical)</option>
                                <option value="HSC / Intermediate (Computer Science / ICS)">HSC / Intermediate (Computer Science / ICS)</option>
                                <option value="HSC / Intermediate (Commerce / I.Com)">HSC / Intermediate (Commerce / I.Com)</option>
                                <option value="HSC / Intermediate (Humanities / Arts)">HSC / Intermediate (Humanities / Arts)</option>
                                <option value="Bachelor Degree (B.A / B.Sc / B.Com)">Bachelor Degree (B.A / B.Sc / B.Com)</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="salu-form-group">
                            <label class="salu-form-label">Intermediate Board / University <span class="text-danger">*</span></label>
                            <select name="name_of_board" id="inpBoard" class="salu-form-control" required>
                                <option value="BISE Sukkur" selected>BISE Sukkur</option>
                                <option value="BISE Larkana">BISE Larkana</option>
                                <option value="BISE Hyderabad">BISE Hyderabad</option>
                                <option value="BISE Mirpurkhas">BISE Mirpurkhas</option>
                                <option value="BIEK Karachi">BIEK Karachi</option>
                                <option value="Shah Abdul Latif University Khairpur">Shah Abdul Latif University Khairpur</option>
                                <option value="Federal Board (FBISE) Islamabad">Federal Board (FBISE) Islamabad</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="salu-form-group">
                            <label class="salu-form-label">Year of Passing <span class="text-danger">*</span></label>
                            <select name="passing_year" id="inpPassYear" class="salu-form-control" required>
                                <option value="2024">2024</option>
                                <option value="2023" selected>2023</option>
                                <option value="2022">2022</option>
                                <option value="2021">2021</option>
                                <option value="2020">2020</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="salu-form-group">
                            <label class="salu-form-label">Division / Grade Obtained <span class="text-danger">*</span></label>
                            <select name="division_obtained" id="inpDivision" class="salu-form-control" required>
                                <option value="A-1 Grade (80% and above)" selected>A-1 Grade (80% and above)</option>
                                <option value="A Grade (70% to 79%)">A Grade (70% to 79%)</option>
                                <option value="B Grade (60% to 69%)">B Grade (60% to 69%)</option>
                                <option value="C Grade (50% to 59%)">C Grade (50% to 59%)</option>
                                <option value="1st Division">1st Division</option>
                                <option value="2nd Division">2nd Division</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="button" class="btn salu-btn-pill-outline px-4" onclick="goToStep(2)">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </button>
                    <button type="button" class="btn salu-btn-pill-blue px-4" onclick="populateReview(); goToStep(4);">
                        Review &amp; Submit <i class="fas fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 4: REVIEW & DECLARATION -->
            <div class="salu-step-pane" id="stepPane4">
                <h4 class="salu-wizard-section-title">
                    <i class="fas fa-clipboard-check text-primary"></i> Step 4: Review Application &amp; Final Declaration
                </h4>

                <div class="p-3 bg-light rounded-4 border mb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <span class="salu-metric-label">STUDENT NAME</span>
                            <strong id="revName" class="salu-metric-value">{{ auth()->user()->full_name }}</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="salu-metric-label">FATHER'S NAME</span>
                            <strong id="revFather" class="salu-metric-value">-</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="salu-metric-label">ADMITTED COLLEGE</span>
                            <strong id="revCollege" class="salu-metric-value text-primary">-</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="salu-metric-label">PROGRAM &amp; SESSION</span>
                            <strong id="revProgram" class="salu-metric-value text-dark">-</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="salu-metric-label">CNIC &amp; CONTACT</span>
                            <strong id="revCnic" class="salu-metric-value">{{ auth()->user()->cnic }}</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="salu-metric-label">DOMICILE DISTRICT</span>
                            <strong id="revDomicile" class="salu-metric-value">-</strong>
                        </div>
                    </div>
                </div>

                <!-- DECLARATION CHECKBOX -->
                <div class="p-3 rounded-3 mb-4" style="background:#fffbeb; border:1.5px solid #fde68a;">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="declarationCheck" required />
                        <label class="form-check-label small fw-bold text-dark" for="declarationCheck">
                            I solemnly declare that all particulars entered in this enrollment application are correct to the best of my knowledge. I agree to abide by the rules and regulations of Shah Abdul Latif University, Khairpur.
                        </label>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="button" class="btn salu-btn-pill-outline px-4" onclick="goToStep(3)">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </button>
                    <button type="submit" class="btn salu-btn-pill-green px-5" id="submitBtn">
                        <i class="fas fa-check-circle me-1"></i> Submit Enrollment Application
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentStep = 1;

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

    function validateAndNext(step) {
        if (step === 1) {
            const college = document.getElementById('inpCollege').value;
            const program = document.getElementById('inpProgram').value;
            if (!college) {
                alert('Please select your Admitted College');
                return;
            }
            if (!program) {
                alert('Please select your Degree Program');
                return;
            }
            goToStep(2);
        } else if (step === 2) {
            const father = document.getElementById('inpFatherName').value;
            const contact = document.getElementById('inpContact').value;
            const address = document.getElementById('inpAddress').value;
            if (!father.trim()) {
                alert("Please enter Father's Name");
                return;
            }
            if (!contact.trim()) {
                alert("Please enter Mobile Contact Number");
                return;
            }
            if (!address.trim()) {
                alert("Please enter Residential Address");
                return;
            }
            goToStep(3);
        }
    }

    function populateReview() {
        const collegeEl = document.getElementById('inpCollege');
        const collegeText = collegeEl.options[collegeEl.selectedIndex]?.text || '-';
        const program = document.getElementById('inpProgram').value;
        const session = document.getElementById('inpSession').value;
        const semester = document.getElementById('inpSemester').value;
        const father = document.getElementById('inpFatherName').value;
        const domicile = document.getElementById('inpDomicileDist').value;

        document.getElementById('revFather').textContent = father;
        document.getElementById('revCollege').textContent = collegeText;
        document.getElementById('revProgram').textContent = program + ' (' + session + ' - ' + semester + ')';
        document.getElementById('revDomicile').textContent = domicile + ', Sindh';
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
