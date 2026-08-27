@extends('layouts.app')

@section('title', 'SuperAdmin Control Center')

@php
    $title = 'SuperAdmin Control Center';
@endphp

@section('content')
<div class="premium-dashboard">
    <section class="dashboard-hero dashboard-hero--executive mb-4">
        <div class="dashboard-hero__content">
            <span class="dashboard-kicker"><i class="fas fa-user-shield me-2"></i>SuperAdmin workspace</span>
            <h1>University Control Center</h1>
            <p>Live oversight of admissions, examination activity, fee collection, and portal access.</p>
            <div class="dashboard-hero__meta">
                <span><i class="fas fa-calendar-days"></i>Academic session: {{ $activeAcademicYear->name ?? 'Not configured' }}</span>
                <span><i class="fas fa-clock"></i>{{ now()->format('l, d F Y') }}</span>
            </div>
        </div>
        <div class="window-control">
            <small>Enrollment admissions</small>
            <strong><span class="status-orb {{ ($currentWindow->is_open ?? false) ? 'status-orb--open' : 'status-orb--closed' }}"></span>{{ ($currentWindow->is_open ?? false) ? 'Window is open' : 'Window is closed' }}</strong>
            <form method="post" action="{{ route('admin.enrollment-window.toggle') }}">
                @csrf
                <input type="hidden" name="open" value="{{ ($currentWindow->is_open ?? false) ? '0' : '1' }}">
                <button type="submit" class="btn {{ ($currentWindow->is_open ?? false) ? 'btn-outline-light' : 'btn-warning' }} btn-sm fw-bold">
                    <i class="fas {{ ($currentWindow->is_open ?? false) ? 'fa-lock' : 'fa-unlock' }} me-1"></i>
                    {{ ($currentWindow->is_open ?? false) ? 'Close admissions' : 'Open admissions' }}
                </button>
            </form>
        </div>
    </section>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4"><i class="fas fa-circle-check me-2"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4"><i class="fas fa-triangle-exclamation me-2"></i>{{ session('error') }}</div>
    @endif

    <section class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3"><article class="executive-stat executive-stat--navy"><span class="executive-stat__icon"><i class="fas fa-file-alt"></i></span><div><span>Total enrollments</span><strong>{{ $totalEnrollments ?? 0 }}</strong><small>{{ $pendingEnrollments ?? 0 }} pending review</small></div></article></div>
        <div class="col-sm-6 col-xl-3"><article class="executive-stat executive-stat--wine"><span class="executive-stat__icon"><i class="fas fa-file-signature"></i></span><div><span>Exam forms</span><strong>{{ $totalExamForms ?? 0 }}</strong><small>{{ ($isExamWindowOpen ?? false) ? 'Submission window open' : 'Submission window closed' }}</small></div></article></div>
        <div class="col-sm-6 col-xl-3"><article class="executive-stat executive-stat--green"><span class="executive-stat__icon"><i class="fas fa-money-bill-wave"></i></span><div><span>Verified revenue</span><strong>PKR {{ number_format($totalRevenue ?? 0, 0) }}</strong><small>{{ $paidFeesCount ?? 0 }} paid challans</small></div></article></div>
        <div class="col-sm-6 col-xl-3"><article class="executive-stat executive-stat--amber"><span class="executive-stat__icon"><i class="fas fa-users"></i></span><div><span>Portal students</span><strong>{{ $students ?? 0 }}</strong><small>{{ $collegeAdmins ?? 0 }} college accounts</small></div></article></div>
    </section>

    <section class="dashboard-shortcuts mb-4">
        <a href="{{ route('admin.enrollments.index') }}"><i class="fas fa-file-alt"></i><span>Enrollment<br /><small>Review applications</small></span></a>
        <a href="{{ route('admin.exams.index') }}"><i class="fas fa-file-signature"></i><span>Exam forms<br /><small>Manage exam window</small></span></a>
        <a href="{{ route('admin.fees.index') }}"><i class="fas fa-money-bill"></i><span>Fee verification<br /><small>Review challans</small></span></a>
        <a href="{{ route('admin.colleges.index') }}"><i class="fas fa-building"></i><span>College accounts<br /><small>{{ $totalColleges }} affiliated colleges</small></span></a>
        <a href="{{ route('admin.users.index') }}"><i class="fas fa-user-gear"></i><span>User access<br /><small>{{ $totalUsers }} portal users</small></span></a>
    </section>

    <section class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="dashboard-panel h-100">
                <div class="dashboard-panel__header">
                    <div><span class="dashboard-panel__eyebrow">Priority queue</span><h2>Pending Enrollment Approvals</h2></div>
                    <a href="{{ route('admin.enrollments.index', ['status' => 'PENDING']) }}" class="btn btn-sm btn-outline-primary fw-bold">View all <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
                <div class="table-responsive">
                    <table class="table dashboard-table align-middle mb-0">
                        <thead><tr><th>Candidate</th><th>Program</th><th>College</th><th>Submitted</th><th class="text-end">Action</th></tr></thead>
                        <tbody>
                        @forelse($pendingEnrollmentsList ?? [] as $enrollment)
                            <tr>
                                <td><strong>{{ $enrollment->user->full_name ?? 'Student' }}</strong><small>{{ $enrollment->user->cnic }}</small></td>
                                <td>{{ $enrollment->program }} <small>Session {{ $enrollment->session }}</small></td>
                                <td>{{ $enrollment->college->name ?? 'Main campus' }}</td>
                                <td>{{ $enrollment->created_at->format('d M Y') }}</td>
                                <td class="text-end">
                                    <form method="post" action="{{ route('admin.enrollments.approve', $enrollment->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm fw-bold">Approve <i class="fas fa-check ms-1"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-5">All enrollment applications have been reviewed.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="dashboard-panel dashboard-panel--compact mb-4">
                <div class="dashboard-panel__header"><div><span class="dashboard-panel__eyebrow">System status</span><h2>Submission Windows</h2></div></div>
                <div class="dashboard-summary-list">
                    <div><span><i class="fas fa-door-open"></i>Enrollment admissions</span><strong class="{{ ($currentWindow->is_open ?? false) ? 'text-success' : 'text-danger' }}">{{ ($currentWindow->is_open ?? false) ? 'Open' : 'Closed' }}</strong></div>
                    <div><span><i class="fas fa-file-signature"></i>Examination forms</span><strong class="{{ ($isExamWindowOpen ?? false) ? 'text-success' : 'text-danger' }}">{{ ($isExamWindowOpen ?? false) ? 'Open' : 'Closed' }}</strong></div>
                    <div><span><i class="fas fa-calendar-days"></i>Closing date</span><strong>{{ isset($currentExamWindow->end_date) ? $currentExamWindow->end_date->format('d M') : 'Not set' }}</strong></div>
                </div>
                <a href="{{ route('admin.exams.index') }}" class="dashboard-panel__footer-link">Manage examination window <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="dashboard-panel dashboard-panel--compact">
                <div class="dashboard-panel__header"><div><span class="dashboard-panel__eyebrow">Access overview</span><h2>Portal Accounts</h2></div></div>
                <div class="dashboard-summary-list">
                    <div><span><i class="fas fa-user-shield"></i>University admins</span><strong>{{ $admins ?? 0 }}</strong></div>
                    <div><span><i class="fas fa-building"></i>College admins</span><strong>{{ $collegeAdmins ?? 0 }}</strong></div>
                    <div><span><i class="fas fa-circle-exclamation"></i>Unpaid challans</span><strong>{{ $unpaidFeesCount ?? 0 }}</strong></div>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-panel">
        <div class="dashboard-panel__header">
            <div><span class="dashboard-panel__eyebrow">Access activity</span><h2>Recently Registered Users</h2></div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary fw-bold">Manage users <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="table-responsive">
            <table class="table dashboard-table align-middle mb-0">
                <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Verification</th><th>Registered</th></tr></thead>
                <tbody>
                @forelse($recentUsers ?? [] as $user)
                    <tr>
                        <td><strong>{{ $user->full_name }}</strong><small>{{ $user->cnic }}</small></td>
                        <td>{{ $user->email }}</td>
                        <td><span class="status-pill status-{{ $user->role === 'STUDENT' ? 'approved' : 'draft' }}">{{ str_replace('_', ' ', $user->role) }}</span></td>
                        <td><span class="status-pill {{ $user->is_verified ? 'status-approved' : 'status-pending' }}">{{ $user->is_verified ? 'Verified' : 'Pending' }}</span></td>
                        <td>{{ $user->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-5">No user accounts have been registered yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
