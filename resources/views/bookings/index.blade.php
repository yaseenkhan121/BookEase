@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="display-6 fw-extrabold mb-1" style="color: var(--text-dark);">My Bookings</h2>
            <p class="text-muted font-medium mb-0">Track and manage your scheduled appointments</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('calendar') }}" class="btn btn-action btn-outline-secondary" style="border-radius: 12px; padding: 10px 20px;">
                <i class="ph ph-calendar mr-1"></i> Calendar View
            </a>
            @if(auth()->user()->role === 'customer')
                <a href="{{ route('bookings.new') }}" class="btn btn-action btn-primary shadow-sm" style="background-color: #10B981; border: none; padding: 10px 20px;">
                    <i class="ph ph-plus mr-1"></i> New Appointment
                </a>
            @endif
        </div>
    </div>


    {{-- Appointments Card --}}
    <div class="card card-premium overflow-hidden border-0 shadow-premium" style="border-radius: 24px; background: var(--bg-card);">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr class="text-muted small text-uppercase font-weight-bold border-bottom">
<!-- Checkbox column removed -->
                        <th class="px-4 py-4 border-0" style="letter-spacing: 0.05em; color: #64748b;">SERVICE & MEMBER</th>
                        <th class="py-4 border-0" style="letter-spacing: 0.05em; color: #64748b;">SCHEDULE</th>
                        <th class="py-4 border-0 text-center" style="letter-spacing: 0.05em; color: #64748b;">STATUS</th>
                        <th class="text-right px-4 py-4 border-0" style="letter-spacing: 0.05em; color: #64748b;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($appointments as $app)
                    <tr class="hover-row transition-all border-bottom border-light">
<!-- Checkbox column removed -->
                        <td class="px-4 py-4 mt-2">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center mr-3 flex-shrink-0" style="width: 46px; height: 46px; background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.15);">
                                    <i class="ph ph-briefcase" style="font-size: 1.35rem; color: #10b981;"></i>
                                </div>
                                <div>
                                    <div class="font-weight-bold" style="font-size: 1.1rem; color: var(--text-dark); letter-spacing: -0.01em;">{{ $app->service->name ?? 'Deleted Service' }}</div>
                                    <div style="font-size: 0.9rem; color: #94a3b8; margin-top: 2px;">
                                        {{ auth()->user()->role === 'provider' ? $app->customer->name : $app->provider->business_name ?? $app->provider->owner_name }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4">
                            <div class="font-weight-bold" style="font-size: 1.1rem; color: var(--text-dark); letter-spacing: -0.01em;">{{ \Carbon\Carbon::parse($app->start_time)->format('M d, Y') }}</div>
                            <div style="font-size: 0.9rem; color: #94a3b8; margin-top: 2px;">{{ \Carbon\Carbon::parse($app->start_time)->format('g:i A') }}</div>
                        </td>
                        <td class="text-center py-4">
                            <span class="status-pill status-{{ $app->status }} px-4 py-2 rounded-pill fw-bold">
                                {{ ucfirst($app->status) }}
                            </span>
                        </td>
                        <td class="text-right px-4 py-4">
                            <div class="d-flex justify-content-end gap-3 align-items-center">
                                
                                {{-- Rating Button for Customers --}}
                                @if(auth()->user()->isCustomer() && $app->can_be_rated)
                                    <button type="button" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold d-flex align-items-center shadow-sm btn-rate" 
                                            data-booking-id="{{ $app->id }}" 
                                            data-provider-name="{{ $app->provider->business_name ?? $app->provider->owner_name }}"
                                            onclick="openRatingModal(this)">
                                        <i class="ph-fill ph-star mr-1"></i> Rate Now
                                    </button>
                                @endif

                                <a href="{{ route('bookings.show', $app->id) }}" class="btn-action-icon view-icon" data-toggle="tooltip" title="View Details">
                                    <i class="ph ph-eye"></i>
                                </a>

                                @if(auth()->user()->isProvider())
                                    @if($app->status === 'pending')
                                        <form action="{{ route('provider.bookings.update-status', $app->id) }}" method="POST" class="d-inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="confirmed">
                                            <button class="btn-action-icon action-bg shadow-sm" title="Approve">
                                                <i class="ph ph-check text-success"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('provider.bookings.update-status', $app->id) }}" method="POST" class="d-inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <button class="btn-action-icon action-bg" title="Reject">
                                                <i class="ph ph-x text-danger"></i>
                                            </button>
                                        </form>
                                    @elseif($app->status === 'confirmed')
                                        <form action="{{ route('provider.bookings.update-status', $app->id) }}" method="POST" class="d-inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="completed">
                                            <button class="btn-action-icon action-bg shadow-sm" title="Mark Completed">
                                                <i class="ph ph-check-circle text-primary"></i>
                                            </button>
                                        </form>
                                    @endif
                                @elseif(auth()->user()->isAdmin())
                                    <form action="{{ route('bookings.destroy', $app->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Admin: Permanently delete this appointment?')">
                                        @csrf @method('DELETE')
                                        <button class="btn-action-icon action-bg" title="Delete">
                                            <i class="ph ph-trash text-danger"></i>
                                        </button>
                                    </form>
                                @elseif(auth()->user()->isCustomer() && $app->status === 'pending')
                                    <form action="{{ route('bookings.destroy', $app->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Cancel this appointment?')">
                                        @csrf @method('DELETE')
                                        <button class="btn-action-icon action-bg shadow-sm" title="Cancel">
                                            <i class="ph ph-x" style="color: #64748b;"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="text-muted opacity-50">
                                <i class="ph ph-calendar-blank d-block mb-3" style="font-size: 4rem;"></i>
                                <p class="h5 font-weight-bold">No Bookings Found</p>
                                <p class="mb-0">You don't have any appointments scheduled yet.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($appointments->hasPages())
            <div class="card-footer bg-white border-0 py-4 px-4">
                {{ $appointments->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Rating Modal --}}
<div class="modal fade" id="ratingModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex justify-content-between">
                <h5 class="modal-title font-weight-bold" style="font-size: 1.35rem;">Rate Experience</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <form id="ratingForm">
                    @csrf
                    <input type="hidden" name="booking_id" id="modal-booking-id">
                    
                    <div class="text-center mb-5">
                        <p class="text-muted mb-3">How was your appointment with <br><span class="font-weight-bold text-dark" id="modal-provider-name">Provider Name</span>?</p>
                        
                        {{-- Star Selection --}}
                        <div class="star-rating d-flex justify-content-center gap-3">
                            @foreach([1,2,3,4,5] as $star)
                                <label class="mb-0 cursor-pointer p-1">
                                    <input type="radio" name="rating" value="{{ $star }}" class="d-none">
                                    <i class="ph ph-star star-icon" data-value="{{ $star }}" style="font-size: 2.5rem; transition: all 0.2s ease;"></i>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="small font-weight-bold text-uppercase text-muted pl-1 mb-2">Review Details (Optional)</label>
                        <textarea name="review_text" class="form-control bg-light border-0" rows="4" placeholder="Share your experience with others..." style="border-radius: 16px; font-size: 0.95rem; resize: none;"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block py-3 font-weight-bold" style="border-radius: 16px; font-size: 1rem;" id="submitRatingBtn">
                        Submit Review
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Theme overrides for Bookings Table */
    .table-responsive {
        --primary: #1F7A63 !important;
        --primary-light: rgba(31, 122, 99, 0.15) !important;
        --primary-hover: #175d4b !important;
    }
    .table-responsive .text-primary { color: var(--primary) !important; }
    .table-responsive .bg-primary-subtle { background-color: var(--primary-light) !important; color: var(--primary) !important; }
    .btn-primary { background-color: #1F7A63 !important; border-color: #1F7A63 !important; color: white !important; }
    .btn-primary:hover { background-color: #175d4b !important; border-color: #175d4b !important; }
    
    .avatar-sm { background: var(--primary-light); }
    /* Enhanced Status Pills */
    .status-pill {
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        border: 1px solid transparent;
        transition: all 0.2s ease;
    }
    
    .status-pending { background: rgba(245, 158, 11, 0.1); color: #d97706; border-color: rgba(245, 158, 11, 0.2); }
    .status-confirmed { background: rgba(16, 185, 129, 0.1); color: #059669; border-color: rgba(16, 185, 129, 0.2); }
    .status-completed { background: rgba(59, 130, 246, 0.1); color: #2563eb; border-color: rgba(59, 130, 246, 0.2); }
    .status-rejected { background: rgba(239, 68, 68, 0.1); color: #dc2626; border-color: rgba(239, 68, 68, 0.2); }
    .status-cancelled { background: rgba(148, 163, 184, 0.1); color: #475569; border-color: rgba(148, 163, 184, 0.2); }

    /* Dark Mode Vibrancy */
    body.dark-mode .status-pending { background: rgba(245, 158, 11, 0.2) !important; color: #FBBF24 !important; border-color: rgba(251, 191, 36, 0.3) !important; }
    body.dark-mode .status-confirmed { background: rgba(16, 185, 129, 0.2) !important; color: #34D399 !important; border-color: rgba(52, 211, 153, 0.3) !important; }
    body.dark-mode .status-completed { background: rgba(59, 130, 246, 0.2) !important; color: #60A5FA !important; border-color: rgba(96, 165, 250, 0.3) !important; }
    body.dark-mode .status-rejected { background: rgba(239, 68, 68, 0.2) !important; color: #F87171 !important; border-color: rgba(248, 113, 113, 0.3) !important; }
    body.dark-mode .status-cancelled { background: rgba(148, 163, 184, 0.2) !important; color: #94A3B8 !important; border-color: rgba(148, 163, 184, 0.3) !important; }

    /* Decorative Action Buttons */
    .btn-action-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none !important;
        border: none;
        outline: none !important;
    }
    
    .view-icon {
        background: rgba(59, 130, 246, 0.08);
        color: #3b82f6;
    }
    .view-icon:hover {
        background: #3b82f6;
        color: #fff !important;
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 6px 15px rgba(59, 130, 246, 0.25);
    }
    
    .action-bg {
        background: rgba(148, 163, 184, 0.1);
        color: #64748b;
    }
    .action-bg:hover {
        background: #ef4444;
        color: #fff !important;
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 6px 15px rgba(239, 68, 68, 0.25);
    }

    body.dark-mode .view-icon {
        background: rgba(96, 165, 250, 0.12);
        color: #60a5fa;
    }
    body.dark-mode .action-bg {
        background: rgba(255, 255, 255, 0.05);
        color: #94a3b8;
    }
    body.dark-mode .action-bg:hover {
        background: #dc2626;
        color: #fff !important;
    }

    /* Hover Row Polishing */
    .hover-row:hover { background-color: rgba(31, 122, 99, 0.03) !important; }
    body.dark-mode .hover-row:hover { background-color: rgba(255,255,255,0.02) !important; }
    .shadow-premium { box-shadow: 0 10px 40px rgba(0,0,0,0.03) !important; }
    
    /* Star Rating Styles */
    .star-icon { cursor: pointer; color: #e2e8f0; }
    .star-icon.active { color: #f59e0b; }
    .star-icon.hover { color: #fbbf24; }
    .cursor-pointer { cursor: pointer; }

    /* Modal Dark Mode Support */
    body.dark-mode .modal-content {
        background: #111827;
        border: 1px solid #374151;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }
    body.dark-mode .modal-header .close {
        color: #F9FAFB;
        text-shadow: none;
        opacity: 0.8;
    }
    body.dark-mode .modal-header .close:hover { opacity: 1; }
    body.dark-mode #modal-provider-name { color: #10B981 !important; }
    body.dark-mode .modal-body textarea {
        background: #1F2937 !important;
        border: 1px solid #374151 !important;
        color: #F9FAFB !important;
    }
</style>
@endpush

@push('scripts')
<script>
    function openRatingModal(btn) {
        const bookingId = btn.getAttribute('data-booking-id');
        const providerName = btn.getAttribute('data-provider-name');
        
        document.getElementById('modal-booking-id').value = bookingId;
        document.getElementById('modal-provider-name').innerText = providerName;
        
        // Reset form
        $('#ratingForm')[0].reset();
        $('.star-icon').removeClass('active ph-fill').addClass('ph');
        
        $('#ratingModal').modal('show');
    }

    $(document).ready(function() {
        // Star Interaction
        $('.star-icon').on('mouseenter', function() {
            const val = $(this).data('value');
            $('.star-icon').each(function() {
                if ($(this).data('value') <= val) {
                    $(this).addClass('hover');
                }
            });
        }).on('mouseleave', function() {
            $('.star-icon').removeClass('hover');
        }).on('click', function() {
            const val = $(this).data('value');
            $(`input[name="rating"][value="${val}"]`).prop('checked', true);
            $('.star-icon').removeClass('active ph-fill').addClass('ph');
            $('.star-icon').each(function() {
                if ($(this).data('value') <= val) {
                    $(this).addClass('active ph-fill').removeClass('ph');
                }
            });
        });

        // Submit Logic
        $('#ratingForm').on('submit', function(e) {
            e.preventDefault();
            
            const rating = $('input[name="rating"]:checked').val();
            if (!rating) {
                Swal.fire('Required', 'Please select a star rating.', 'warning');
                return;
            }

            const btn = $('#submitRatingBtn');
            btn.prop('disabled', true).html('<i class="ph ph-circle-notch animate-spin mr-2"></i> Submitting...');

            $.ajax({
                url: "{{ route('reviews.store') }}",
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('#ratingModal').modal('hide');
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    // Remove the rate button from the row
                    const bookingId = $('#modal-booking-id').val();
                    $(`.btn-rate[data-booking-id="${bookingId}"]`).fadeOut();
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Submit Review');
                    const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Something went wrong.';
                    Swal.fire('Error', msg, 'error');
                }
            });
        });
    });
</script>
@endpush
@endsection