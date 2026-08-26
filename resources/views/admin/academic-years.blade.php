@extends('layouts.app')

@section('title', 'Academic Years')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Academic Years & Enrollment Windows</h5>
                <button class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Add Academic Year</button>
            </div>
            <div class="card-body">
                <p class="text-muted">Academic years and enrollment window management.</p>
            </div>
        </div>
    </div>
</div>
@endsection
