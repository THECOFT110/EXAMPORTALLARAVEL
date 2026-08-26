@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>System Audit Logs</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">All user actions and system events logged here.</p>
            </div>
        </div>
    </div>
</div>
@endsection
