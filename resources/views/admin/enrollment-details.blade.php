@extends('layouts.app')

@section('title', 'Enrollment Details')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Enrollment Details #{{ $id }}</h5>
                <div class="btn-group">
                    <button class="btn btn-success btn-sm"><i class="fas fa-check me-1"></i>Approve</button>
                    <button class="btn btn-danger btn-sm"><i class="fas fa-times me-1"></i>Reject</button>
                </div>
            </div>
            <div class="card-body">
                <p class="text-muted">Full enrollment application details will appear here.</p>
            </div>
        </div>
    </div>
</div>
@endsection
