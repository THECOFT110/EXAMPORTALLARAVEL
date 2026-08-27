@extends('layouts.app')

@section('title', $isCollegeAdmin ? 'College Dashboard' : 'Admin Dashboard')

@php
    $isCollegeAdmin = $isCollegeAdmin ?? false;
    $title = $isCollegeAdmin ? 'College Dashboard' : 'Admin Dashboard';
    $dashboardTitle = $isCollegeAdmin ? 'College Operations Center' : 'Administration Dashboard';
    $dashboardDescription = $isCollegeAdmin
        ? 'A focused view of your enrolled students, examination forms, and daily college activity.'
        : 'Monitor enrollment activity, student records, and core portal operations from one place.';
@endphp

@section('content')
<div class="premium-dashboard">
    <section class="dashboard-hero dashboard-hero--college mb-4">
        <div class="dashboard-hero__content">
            <span class="dashboard-kicker"><i class="fas {{ $isCollegeAdmin ? 'fa-building' : 'fa-shield-alt' }} me-2"></i>{{ $isCollegeAdmin ? 'College Administration' : 'Portal Administration' }}</span>
            <h1>{{ $dashboardTitle }}</h1>
            <p>{{ $dashboardDescription }}</p>
            @if($isCollegeAdmin)
                <div class="dashboard-hero__meta">
                    <span><i class="fas fa-university"></i>{{ $collegeName ?? 'College account pending setup' }}</span>
                    <span><i class="fas fa-calendar-alt"></i>{{ now()->format('l, d F Y') }}</span>
                </div>
            @endif
        </div>
        <div class="dashboard-hero__status">
            @if($isCollegeAdmin)
                <span class="status-orb {{ $isExamWindowOpen ? 'status-orb--open' : 'status-orb--closed' }}"></span>
                <div>
                    <small>Examination form window</small>
                    <strong>{{ $isExamWindowOpen ? 'Open for submissions' : 'Currently closed' }}</strong>
                </div>
            @else
                <a href="/hangfire" class="btn btn-light fw-bold"><i class="fas fa-briefcase me-2"></i>Background jobs</a>
            @endif
        </div>
    </section>

    <section class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <article class="executive-stat executive-stat--navy">
                <span class="executive-stat__icon"><i class="fas fa-users"></i></span>
                <div><span>Enrolled students</span><strong>{{ $totalStudents ?? 0 }}</strong><small>Student records in scope</small></div>
            </article>
        </div>
        <div class="col-sm-6 col-xl-3">
            <article class="executive-stat executive-stat--amber">
                <span class="executive-stat__icon"><i class="fas fa-clock"></i></span>
                <div><span>Awaiting review</span><strong>{{ $pendingEnrollments ?? 0 }}</strong><small>Enrollment applications</small></div>
            </article>
        </div>
        <div class="col-sm-6 col-xl-3">
            <article class="executive-stat executive-stat--green">
                <span class="executive-stat__icon"><i class="fas fa-circle-check"></i></span>
                <div><span>Approved enrollments</span><strong>{{ $approvedEnrollments ?? 0 }}</strong><small>Ready student records</small></div>
            </article>
        </div>
        <div class="col-sm-6 col-xl-3">
            <article class="executive-stat executive-stat--wine">
                <span class="executive-stat__icon"><i class="fas fa-file-signature"></i></span>
                <div><span>Exam forms submitted</span><strong>{{ $submittedExamForms ?? 0 }}</strong><small>Official eFormI records</small></div>
            </article>
        </div>
    </section>

    <section class="row g-4">
        <div class="col-xl-8">
            <div class="dashboard-panel h-100">
                <div class="dashboard-panel__header">
                    <div>
                        <span class="dashboard-panel__eyebrow">Latest activity</span>
                        <h2>Recent Enrollment Applications</h2>
                    </div>
                    @if($canViewEnrollments ?? true)
                        <a href="{{ route('admin.enrollments.index') }}" class="btn btn-sm btn-outline-primary fw-bold">View enrollments <i class="fas fa-arrow-right ms-1"></i></a>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table dashboard-table align-middle mb-0">
                        <thead>
                            <tr><th>Student</th><th>Program</th><th>Session</th><th>Status</th><th>Submitted</th></tr>
                        </thead>
                        <tbody>
                        @forelse($recentEnrollments as $enrollment)
                            <tr>
                                <td><strong>{{ $enrollment->user->full_name ?? 'Student' }}</strong><small>{{ $enrollment->user->cnic ?? 'CNIC unavailable' }}</small></td>
                                <td>{{ $enrollment->program }}</td>
                                <td>{{ $enrollment->session }}</td>
                                <td><span class="status-pill status-{{ strtolower($enrollment->status) }}">{{ $enrollment->status }}</span></td>
                                <td>{{ $enrollment->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-5">No enrollment applications are available yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="dashboard-panel dashboard-panel--compact mb-4">
                <div class="dashboard-panel__header">
                    <div><span class="dashboard-panel__eyebrow">At a glance</span><h2>Academic Records</h2></div>
                </div>
                <div class="dashboard-summary-list">
                    <div><span><i class="fas fa-file-alt"></i>Total enrollments</span><strong>{{ $totalEnrollments ?? 0 }}</strong></div>
                    <div><span><i class="fas fa-graduation-cap"></i>Programs</span><strong>{{ $totalPrograms ?? 0 }}</strong></div>
                    <div><span><i class="fas fa-university"></i>Colleges</span><strong>{{ $totalColleges ?? 0 }}</strong></div>
                </div>
            </div>
            
            <!-- Official PDF Reports & Seat Lists for College -->
            @if($isCollegeAdmin && isset($collegeId))
            <div class="dashboard-panel dashboard-panel--compact mb-4 border-start border-4 border-primary">
                <div class="dashboard-panel__header">
                    <div>
                        <span class="dashboard-panel__eyebrow text-primary fw-bold">Official Examination Rosters</span>
                        <h2>Download PDF Lists</h2>
                    </div>
                </div>
                <div class="p-3 bg-light rounded-3 mb-3">
                    <p class="small text-muted mb-2">
                        Official desk attendance and seat lists with <strong>Photo Thumbnails</strong> and designated <strong>Signature Columns</strong>.
                    </p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('api.college.reports.seat-list-pdf', ['collegeId' => $collegeId, 'academicYearId' => $activeYearId, 'gender' => 1]) }}" target="_blank" class="btn btn-outline-info btn-sm text-start fw-bold shadow-sm">
                            <i class="fas fa-mars me-2 text-info"></i> Download Male Seat List (PDF)
                            <span class="badge bg-info text-white float-end">{{ $maleApprovedCount ?? 0 }} Boys</span>
                        </a>
                        <a href="{{ route('api.college.reports.seat-list-pdf', ['collegeId' => $collegeId, 'academicYearId' => $activeYearId, 'gender' => 2]) }}" target="_blank" class="btn btn-outline-danger btn-sm text-start fw-bold shadow-sm">
                            <i class="fas fa-venus me-2 text-danger"></i> Download Female Seat List (PDF)
                            <span class="badge bg-danger text-white float-end">{{ $femaleApprovedCount ?? 0 }} Girls</span>
                        </a>
                        <a href="{{ route('api.college.reports.complete-list-pdf', ['collegeId' => $collegeId, 'academicYearId' => $activeYearId]) }}" target="_blank" class="btn btn-outline-dark btn-sm text-start fw-bold shadow-sm">
                            <i class="fas fa-file-pdf me-2 text-dark"></i> Download Complete Register (PDF)
                            <span class="badge bg-dark text-white float-end">{{ $totalEnrollments }} Total</span>
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <div class="dashboard-panel dashboard-panel--compact">
                <div class="dashboard-panel__header">
                    <div><span class="dashboard-panel__eyebrow">Shortcuts</span><h2>Quick Actions</h2></div>
                </div>
                <div class="dashboard-action-list">
                    @if($isCollegeAdmin)
                        <a href="{{ route('admin.enrollments.index') }}"><i class="fas fa-user-check"></i><span>Review enrollments</span><i class="fas fa-chevron-right"></i></a>
                        <a href="{{ route('admin.exams.index') }}"><i class="fas fa-file-signature"></i><span>Create examination forms</span><i class="fas fa-chevron-right"></i></a>
                        <a href="{{ route('admin.fees.index') }}"><i class="fas fa-money-bill-wave"></i><span>Review fee challans</span><i class="fas fa-chevron-right"></i></a>
                    @else
                        <a href="{{ route('admin.students.index') }}"><i class="fas fa-users"></i><span>Manage students</span><i class="fas fa-chevron-right"></i></a>
                        <a href="{{ route('admin.seats.index') }}"><i class="fas fa-chair"></i><span>Master Seat Allocation</span><i class="fas fa-chevron-right"></i></a>
                        <a href="{{ route('admin.reports.index') }}"><i class="fas fa-chart-column"></i><span>Open reports</span><i class="fas fa-chevron-right"></i></a>
                        <a href="{{ route('admin.settings') }}"><i class="fas fa-gear"></i><span>System settings</span><i class="fas fa-chevron-right"></i></a>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
