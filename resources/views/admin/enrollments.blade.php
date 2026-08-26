@extends('layouts.app')

@section('title', 'Enrollment Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Enrollment Applications</h5>
                <div class="btn-group">
                    <button class="btn btn-success btn-sm"><i class="fas fa-check-double me-1"></i>Bulk Approve</button>
                    <button class="btn btn-danger btn-sm"><i class="fas fa-times me-1"></i>Bulk Reject</button>
                </div>
            </div>
            <div class="card-body">
                <p class="text-muted">Enrollment applications will appear here with filtering options.</p>
            </div>
        </div>
    </div>
</div>
@endsection
