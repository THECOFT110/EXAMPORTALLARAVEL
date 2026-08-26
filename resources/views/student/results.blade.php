@extends('layouts.app')

@section('title', 'Examination Results & Transcripts')

@php
    $title = 'Academic Results';
@endphp

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">
            <i class="fas fa-graduation-cap text-primary me-2"></i>Semester Examination Results
        </h4>
        <p class="text-muted small mb-0">Official academic transcripts, GPA metrics, and course score cards</p>
    </div>
</div>

@php
    $user = auth()->user();
    $enrollments = \App\Models\Enrollment::where('user_id', $user->id)
        ->with(['results' => fn($q) => $q->whereNotNull('published_at'), 'college'])
        ->get();
@endphp

@if($enrollments->isEmpty() || $enrollments->every(fn($e) => $e->results->isEmpty()))
    <div class="card salu-overview-card p-5 text-center">
        <div class="salu-service-icon-box salu-icon-pink mx-auto mb-3">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <h5 class="fw-bold text-dark mb-2">No Published Results Yet</h5>
        <p class="text-muted small mb-0">Your semester examination results have not been published by the Controller of Examinations yet.</p>
    </div>
@else
    @foreach($enrollments as $enrollment)
        @if($enrollment->results->isNotEmpty())
            @php
                $totalMarks = $enrollment->results->sum('total_marks');
                $obtainedMarks = $enrollment->results->sum('marks');
                $percentage = $totalMarks > 0 ? round(($obtainedMarks / $totalMarks) * 100, 2) : 0;
            @endphp
            <div class="card salu-overview-card mb-4">
                <div class="card-header salu-overview-header d-flex flex-wrap justify-content-between align-items-center py-3 px-4 gap-2">
                    <div>
                        <h5 class="mb-0 fw-bold text-white">{{ $enrollment->program }}</h5>
                        <span class="text-white-50 small">Roll No: <strong>{{ $enrollment->roll_number }}</strong> &bull; Session: {{ $enrollment->session }}</span>
                    </div>
                    <div>
                        <a href="{{ route('enrollment.result-card-pdf', $enrollment->id) }}" target="_blank" class="btn salu-btn-pill-green btn-sm">
                            <i class="fas fa-download me-1"></i> Official Result Card PDF
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- SUMMARY METRICS -->
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-md-3">
                            <div class="salu-metric-box">
                                <span class="salu-metric-label">TOTAL MARKS</span>
                                <strong class="salu-metric-value">{{ $totalMarks }}</strong>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="salu-metric-box">
                                <span class="salu-metric-label">MARKS OBTAINED</span>
                                <strong class="salu-metric-value text-success">{{ $obtainedMarks }}</strong>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="salu-metric-box">
                                <span class="salu-metric-label">PERCENTAGE</span>
                                <strong class="salu-metric-value text-primary">{{ $percentage }}%</strong>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="salu-metric-box">
                                <span class="salu-metric-label">OVERALL STATUS</span>
                                <strong class="salu-metric-value text-success">
                                    <i class="fas fa-circle-check me-1"></i> PASS
                                </strong>
                            </div>
                        </div>
                    </div>

                    <!-- COURSES TABLE -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="small text-muted text-uppercase">
                                    <th class="ps-3">Course Code</th>
                                    <th>Subject Title</th>
                                    <th class="text-center">Total Marks</th>
                                    <th class="text-center">Marks Obtained</th>
                                    <th class="text-center">Grade</th>
                                    <th class="text-end pe-3">Published Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($enrollment->results as $result)
                                    <tr>
                                        <td class="ps-3">
                                            <span class="fw-bold font-monospace text-primary">{{ $result->subject_code }}</span>
                                        </td>
                                        <td>
                                            <strong class="text-dark small">{{ $result->subject_name }}</strong>
                                        </td>
                                        <td class="text-center">{{ $result->total_marks }}</td>
                                        <td class="text-center fw-bold text-dark">{{ $result->marks }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold">
                                                {{ $result->grade }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-3 small text-muted">
                                            {{ $result->published_at ? \Carbon\Carbon::parse($result->published_at)->format('d M Y') : 'N/A' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endif
@endsection
