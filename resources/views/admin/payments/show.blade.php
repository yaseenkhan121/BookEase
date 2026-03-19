@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <div class="mb-4">
        <a href="{{ route('admin.payments.index') }}" class="text-muted small text-decoration-none">
            <i class="ph ph-arrow-left me-1"></i> Back to Payments
        </a>
    </div>

    <div class="row g-4">
        {{-- Payment Details --}}
        <div class="col-md-8">
            <div class="card card-premium p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold text-dark mb-0">Payment #{{ $payment->id }}</h5>
                    <span class="badge bg-{{ $payment->status_badge }} rounded-pill px-3 py-1">
                        {{ ucfirst(str_replace('_', ' ', $payment->payment_status)) }}
                    </span>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="rounded-3 p-3" style="background: var(--bg-body, #f8fafc); border: 1px solid var(--border-color, #e2e8f0);">
                            <small class="text-muted d-block mb-1">Payment Method</small>
                            <span class="fw-bold text-dark">{{ $payment->method_label }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="rounded-3 p-3" style="background: var(--bg-body, #f8fafc); border: 1px solid var(--border-color, #e2e8f0);">
                            <small class="text-muted d-block mb-1">Amount</small>
                            <span class="fw-bold" style="color: var(--primary, #10b981); font-size: 1.2rem;">{{ $payment->formatted_amount }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="rounded-3 p-3" style="background: var(--bg-body, #f8fafc); border: 1px solid var(--border-color, #e2e8f0);">
                            <small class="text-muted d-block mb-1">Transaction Reference</small>
                            <span class="fw-bold text-dark">{{ $payment->transaction_reference ?? 'Not provided' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="rounded-3 p-3" style="background: var(--bg-body, #f8fafc); border: 1px solid var(--border-color, #e2e8f0);">
                            <small class="text-muted d-block mb-1">Submitted</small>
                            <span class="fw-bold text-dark">{{ $payment->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Payment Proof --}}
                @if($payment->payment_proof)
                    <h6 class="fw-bold text-dark mb-3">Payment Proof</h6>
                    <div class="rounded-3 p-3 text-center" style="background: var(--bg-body, #f8fafc); border: 1px solid var(--border-color, #e2e8f0);">
                        @if(in_array(pathinfo($payment->payment_proof, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
                            <img src="{{ Storage::url($payment->payment_proof) }}" alt="Payment Proof" class="img-fluid rounded" style="max-height: 400px;">
                        @else
                            <a href="{{ Storage::url($payment->payment_proof) }}" target="_blank" class="btn btn-light rounded-pill px-4">
                                <i class="ph ph-file-pdf me-1"></i> View PDF Receipt
                            </a>
                        @endif
                    </div>
                @endif

                {{-- Admin Actions --}}
                @if($payment->payment_status === 'verification_pending')
                    <hr class="my-4">
                    <h6 class="fw-bold text-dark mb-3">Admin Verification</h6>
                    <div class="d-flex gap-2">
                        <form action="{{ route('admin.payments.approve', $payment) }}" method="POST" class="flex-grow-1">
                            @csrf
                            <input type="text" name="admin_notes" class="form-control mb-2" placeholder="Admin notes (optional)">
                            <button type="submit" class="btn btn-action btn-success text-white w-100"
                                    onclick="return confirm('Approve this payment and confirm the booking?')">
                                <i class="ph ph-check-circle"></i> Approve Payment
                            </button>
                        </form>
                        <form action="{{ route('admin.payments.reject', $payment) }}" method="POST" class="flex-grow-1">
                            @csrf
                            <input type="text" name="admin_notes" class="form-control mb-2" placeholder="Rejection reason (required)" required>
                            <button type="submit" class="btn btn-action btn-outline-danger w-100"
                                    onclick="return confirm('Reject this payment?')">
                                <i class="ph ph-x-circle"></i> Reject Payment
                            </button>
                        </form>
                    </div>
                @endif

                {{-- Verified Info --}}
                @if($payment->verified_at)
                    <div class="rounded-3 p-3 mt-4" style="background: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.2);">
                        <small class="text-muted">Verified by</small>
                        <p class="fw-bold text-dark mb-1">{{ $payment->verifiedBy->name ?? 'Admin' }}</p>
                        <small class="text-muted">{{ $payment->verified_at->format('M d, Y h:i A') }}</small>
                        @if($payment->admin_notes)
                            <p class="text-muted small mt-2 mb-0">Notes: {{ $payment->admin_notes }}</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Customer & Booking Sidebar --}}
        <div class="col-md-4">
            <div class="card card-premium p-4 mb-3">
                <h6 class="fw-bold text-dark mb-3"><i class="ph ph-user me-1"></i> Customer</h6>
                <p class="fw-bold text-dark mb-1">{{ $payment->customer->name ?? 'N/A' }}</p>
                <p class="text-muted small mb-0">{{ $payment->customer->email ?? '' }}</p>
                <p class="text-muted small mb-0">{{ $payment->customer->phone_number ?? '' }}</p>
            </div>
            <div class="card card-premium p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="ph ph-calendar me-1"></i> Booking</h6>
                <div class="mb-2">
                    <small class="text-muted">Service</small>
                    <p class="fw-bold text-dark mb-0">{{ $payment->booking->service->name ?? 'N/A' }}</p>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Provider</small>
                    <p class="fw-bold text-dark mb-0">{{ $payment->booking->provider->display_name ?? 'N/A' }}</p>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Time</small>
                    <p class="fw-bold text-dark mb-0">
                        {{ \Carbon\Carbon::parse($payment->booking->start_time)->format('M d, h:i A') }}
                    </p>
                </div>
                <div>
                    <small class="text-muted">Booking Status</small>
                    <p class="fw-bold mb-0">
                        <span class="badge bg-{{ $payment->booking->status_color }} rounded-pill">
                            {{ ucfirst($payment->booking->status) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
