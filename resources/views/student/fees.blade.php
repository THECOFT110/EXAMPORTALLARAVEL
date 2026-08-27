@extends('layouts.app')

@section('title', 'Fee Challans & Online Payments')

@php
    $title = 'Fee Management';
@endphp

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">
            <i class="fas fa-receipt text-primary me-2"></i>Fee Challans &amp; Payment History
        </h4>
        <p class="text-muted small mb-0">View generated bank challans, verify fee status, and submit online payment Transaction IDs</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-4 d-flex align-items-center p-3 mb-4" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);">
        <i class="fas fa-circle-check fa-lg me-2 text-success"></i>
        <div class="flex-grow-1 text-success-emphasis fw-semibold">{{ session('success') }}</div>
    </div>
@endif

@php
    $user = auth()->user();
    $allFees = \App\Models\Fee::whereHas('enrollment', fn($q) => $q->where('user_id', $user->id))
        ->with('enrollment')
        ->orderByDesc('created_at')
        ->get();
@endphp

@if($allFees->isEmpty())
    <div class="card salu-overview-card p-5 text-center">
        <div class="salu-service-icon-box salu-icon-green mx-auto mb-3">
            <i class="fas fa-receipt"></i>
        </div>
        <h5 class="fw-bold text-dark mb-2">No Fee Challans Issued</h5>
        <p class="text-muted small mb-0">You currently have no outstanding or paid fee challans.</p>
    </div>
@else
    <div class="card salu-overview-card mb-4">
        <div class="card-header salu-overview-header d-flex justify-content-between align-items-center py-3 px-4">
            <h5 class="mb-0 fw-bold text-white"><i class="fas fa-list-check text-warning me-2"></i>Issued Fee Invoices</h5>
            <span class="badge bg-white text-dark fw-bold px-3 py-2 rounded-pill">
                Total: {{ $allFees->count() }}
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="small text-muted text-uppercase">
                        <th class="ps-4">Challan No</th>
                        <th>Program / Purpose</th>
                        <th>Amount (PKR)</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Payment Details</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allFees as $fee)
                        @php
                            $statusBadge = match($fee->status) {
                                'VERIFIED' => 'bg-success text-white',
                                'PAID' => 'bg-info text-dark',
                                'UNPAID' => 'bg-warning text-dark',
                                'EXPIRED' => 'bg-danger text-white',
                                default => 'bg-secondary text-white'
                            };
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold text-dark font-monospace">{{ $fee->challan_number }}</span>
                            </td>
                            <td>
                                <strong class="d-block text-dark small">{{ $fee->enrollment->program ?? 'Academic Fee' }}</strong>
                                <span class="text-muted" style="font-size:0.75rem;">Session: {{ $fee->enrollment->session ?? 'Current' }}</span>
                            </td>
                            <td>
                                <span class="fw-bold text-dark">PKR {{ number_format($fee->amount, 0) }}</span>
                            </td>
                            <td>
                                <span class="small text-muted">{{ $fee->due_date ? \Carbon\Carbon::parse($fee->due_date)->format('d M Y') : 'N/A' }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $statusBadge }} px-3 py-2 rounded-pill fw-bold">
                                    {{ $fee->status }}
                                </span>
                            </td>
                            <td>
                                @if($fee->transaction_id)
                                    <span class="d-block small text-dark fw-bold">TID: <code>{{ $fee->transaction_id }}</code></span>
                                    <span class="text-muted" style="font-size:0.75rem;">Via {{ $fee->payment_method ?? 'Online' }}</span>
                                @else
                                    <span class="text-muted small">&mdash;</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('enrollment.challan-pdf', $fee->id) }}" target="_blank" class="btn salu-btn-pill-outline btn-sm" title="Download Bank Challan PDF">
                                        <i class="fas fa-print me-1"></i> Print PDF
                                    </a>
                                    @if($fee->status === 'UNPAID')
                                        <button type="button" class="btn salu-btn-pill-green btn-sm" data-bs-toggle="modal" data-bs-target="#payModal{{ $fee->id }}">
                                            <i class="fas fa-bolt me-1"></i> Pay / Submit TID
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- PAYMENT MODAL -->
                        @if($fee->status === 'UNPAID')
                            <div class="modal fade" id="payModal{{ $fee->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content rounded-4 border-0 shadow">
                                        <div class="modal-header bg-primary text-white rounded-top-4 p-4">
                                            <h5 class="modal-title fw-bold"><i class="fas fa-credit-card me-2"></i>Submit Fee Payment</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form method="POST" action="{{ route('payment.submit', $fee->id) }}">
                                            @csrf
                                            <div class="modal-body p-4">
                                                <div class="p-3 bg-light rounded-3 mb-3 border">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span class="text-muted small">Challan Number:</span>
                                                        <strong class="font-monospace">{{ $fee->challan_number }}</strong>
                                                    </div>
                                                    <div class="d-flex justify-content-between">
                                                        <span class="text-muted small">Payable Amount:</span>
                                                        <strong class="text-success fs-6">PKR {{ number_format($fee->amount, 0) }}</strong>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small">Select Payment Channel <span class="text-danger">*</span></label>
                                                    <select name="payment_method" class="form-select" required>
                                                        <option value="JazzCash">JazzCash (Account: 0300-1234567 / Till: 98765)</option>
                                                        <option value="EasyPaisa">EasyPaisa (Account: 0300-7654321)</option>
                                                        <option value="BankTransfer">HBL Bank Branch Online Deposit</option>
                                                        <option value="ONLINE">Other 1Link / Microfinance App</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small">Transaction ID / Reference Number (TID) <span class="text-danger">*</span></label>
                                                    <input name="transaction_id" class="form-control font-monospace" placeholder="e.g. JC-98765432 or Deposit Slip No" required />
                                                    <small class="text-muted">Enter the transaction receipt number sent via SMS or bank counter.</small>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light rounded-bottom-4 p-3">
                                                <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn salu-btn-pill-green px-4">
                                                    <i class="fas fa-check-circle me-1"></i> Submit Payment
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
