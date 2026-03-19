@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    {{-- Header with Stats --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Payment Verification</h2>
            <p class="text-muted small mb-0">Review and verify customer payment submissions</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card card-premium p-3 text-center">
                <small class="text-muted">Pending Review</small>
                <h3 class="fw-bold mb-0" style="color: #f59e0b;">{{ $stats['pending'] }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-premium p-3 text-center">
                <small class="text-muted">Verified</small>
                <h3 class="fw-bold mb-0" style="color: #10b981;">{{ $stats['paid'] }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-premium p-3 text-center">
                <small class="text-muted">Total Payments</small>
                <h3 class="fw-bold text-dark mb-0">{{ $stats['total'] }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-premium p-3 text-center">
                <small class="text-muted">Revenue</small>
                <h3 class="fw-bold mb-0" style="color: var(--primary, #10b981);">PKR {{ number_format($stats['revenue'], 0) }}</h3>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card card-premium p-3 mb-4">
        <form class="d-flex gap-2 flex-wrap" method="GET">
            <select name="status" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                <option value="">Pending Verification</option>
                <option value="verification_pending" {{ request('status') === 'verification_pending' ? 'selected' : '' }}>Verification Pending</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
            </select>
            <select name="method" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                <option value="">All Methods</option>
                <option value="easypaisa" {{ request('method') === 'easypaisa' ? 'selected' : '' }}>Easypaisa</option>
                <option value="jazzcash" {{ request('method') === 'jazzcash' ? 'selected' : '' }}>JazzCash</option>
                <option value="bank_transfer" {{ request('method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
            </select>
        </form>
    </div>

    {{-- Payments Table --}}
    <div class="card card-premium overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-muted small fw-bold">ID</th>
                        <th class="text-muted small fw-bold">Customer</th>
                        <th class="text-muted small fw-bold">Service</th>
                        <th class="text-muted small fw-bold">Method</th>
                        <th class="text-muted small fw-bold">Amount</th>
                        <th class="text-muted small fw-bold">Reference</th>
                        <th class="text-muted small fw-bold">Status</th>
                        <th class="text-muted small fw-bold">Date</th>
                        <th class="text-muted small fw-bold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)
                        <tr>
                            <td class="text-dark fw-bold">#{{ $payment->id }}</td>
                            <td class="text-dark">{{ $payment->customer->name ?? 'N/A' }}</td>
                            <td class="text-dark">{{ $payment->booking->service->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge rounded-pill px-2 py-1"
                                      style="background: rgba(16, 185, 129, 0.1); color: var(--primary, #10b981); font-size: 0.75rem;">
                                    {{ $payment->method_label }}
                                </span>
                            </td>
                            <td class="fw-bold" style="color: var(--primary, #10b981);">{{ $payment->formatted_amount }}</td>
                            <td class="text-dark small">{{ $payment->transaction_reference ?? '—' }}</td>
                            <td>
                                <span class="badge bg-{{ $payment->status_badge }} rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                                    {{ ucfirst(str_replace('_', ' ', $payment->payment_status)) }}
                                </span>
                            </td>
                            <td class="text-muted small">{{ $payment->created_at->format('M d, h:i A') }}</td>
                            <td class="text-center">
                                @if($payment->payment_status === 'verification_pending')
                                    <div class="d-flex gap-1 justify-content-center">
                                        {{-- Approve --}}
                                        <form action="{{ route('admin.payments.approve', $payment) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-action btn-success text-white"
                                                    onclick="return confirm('Approve this payment?')">
                                                <i class="ph ph-check"></i> Approve
                                            </button>
                                        </form>
                                        {{-- Reject --}}
                                        <button type="button" class="btn btn-action btn-outline-danger"
                                                onclick="rejectPayment({{ $payment->id }})">
                                            <i class="ph ph-x"></i> Reject
                                        </button>
                                    </div>

                                    @if($payment->payment_proof)
                                        <a href="{{ Storage::url($payment->payment_proof) }}" target="_blank" class="btn btn-link btn-sm p-0 mt-1" style="font-size: 0.7rem;">
                                            <i class="ph ph-image me-1"></i>View Proof
                                        </a>
                                    @endif
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="ph ph-check-circle" style="font-size: 2rem; color: var(--primary, #10b981);"></i>
                                <p class="text-muted small mt-2 mb-0">No payments pending verification</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="p-3 border-top">
                {{ $payments->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark">Reject Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <label class="form-label fw-bold small text-dark">Reason for rejection <span class="text-danger">*</span></label>
                    <textarea name="admin_notes" class="form-control" rows="3" required placeholder="Explain why this payment is being rejected..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn rounded-pill fw-bold" style="background: #ef4444; color: #fff; border: none;">
                        Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function rejectPayment(paymentId) {
        const form = document.getElementById('rejectForm');
        form.action = `/admin/payments/${paymentId}/reject`;
        new bootstrap.Modal(document.getElementById('rejectModal')).show();
    }
</script>
@endsection
