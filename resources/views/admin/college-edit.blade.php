@extends('layouts.app')

@section('title', 'Edit College')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit College #{{ $id }}</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">College edit form will appear here.</p>
            </div>
        </div>
    </div>
</div>
@endsection
