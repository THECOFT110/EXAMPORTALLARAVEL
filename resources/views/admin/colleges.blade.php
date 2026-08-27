@extends('layouts.app')

@section('title', 'College Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-building me-2"></i>College Management</h5>
                <a href="{{ route('admin.colleges.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Add College
                </a>
            </div>
            <div class="card-body">
                <p class="text-muted">Affiliated colleges list with management options.</p>
            </div>
        </div>
    </div>
</div>
@endsection
