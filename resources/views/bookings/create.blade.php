@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- COLLAPSING HEADER BLOCK --}}
    <div class="collapsing-header bg-slate-50 mb-4" style="margin-top: -24px; z-index: 1010; padding-top: 24px;">
        <div class="d-flex justify-content-between align-items-center mb-0">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ $provider->avatar_url }}" class="rounded-circle shadow-sm show-on-scroll flex-shrink-0" style="width: 40px; height: 40px; object-fit: cover;" alt="{{ $provider->name }}">
                <h4 class="font-weight-bold shrink-on-scroll mb-0">Schedule Appointment</h4>
            </div>
            <div class="show-on-scroll-flex align-items-center gap-3">
                <div class="text-end d-none d-md-block">
                    <span class="d-block text-slate-500 small fw-bold text-uppercase">Booking with</span>
                    <span class="fw-bold text-slate-800">{{ $provider->name }}</span>
                </div>
            </div>
        </div>
        <p class="text-muted small mb-0 mt-2 hide-on-scroll">Complete the form below to request an appointment with {{ $provider->name }}.</p>
    </div>
    {{-- END COLLAPSING HEADER --}}

    <div class="row">
        <div class="col-lg-8">
            <div class="card card-premium p-4">
                <form action="{{ route('bookings.store') }}" method="POST" id="booking-form">
                    @csrf
                    <input type="hidden" name="service_id" value="{{ $service->id }}">
                    <input type="hidden" name="provider_id" value="{{ $provider->id }}">

                    {{-- Step 1: Date Selection --}}
                    <div class="calendar-container mb-4">
                        <label class="form-label font-weight-bold text-dark">
                            <i class="ph ph-calendar-blank mr-1 text-primary"></i> 1. Pick a Date
                        </label>
                        <input type="date" name="date" id="date-picker" class="form-control custom-input" 
                               min="{{ date('Y-m-d') }}" required>
                    </div>

                    {{-- Step 2: Time Slots (Dynamic) --}}
                    <div class="time-selection mb-4">
                        <label class="form-label font-weight-bold text-dark">
                            <i class="ph ph-clock mr-1 text-primary"></i> 2. Select Available Time
                        </label>
                        <div id="slots-loader" class="text-center d-none my-3">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            <span class="small ml-2 text-muted">Checking availability...</span>
                        </div>
                        <div id="slots-container" class="slot-grid">
                            <div class="alert alert-light border text-center py-4 w-100">
                                <i class="ph ph-calendar-check d-block mb-2 text-muted" style="font-size: 1.5rem;"></i>
                                <span class="small text-muted">Please select a date to view available time slots.</span>
                            </div>
                        </div>
                        {{-- Hidden input to store the selected time --}}
                        <input type="hidden" name="time" id="selected-time" required>
                    </div>

                    {{-- Step 3: Notes --}}
                    <div class="mt-4">
                        <label class="form-label font-weight-bold text-dark">
                            <i class="ph ph-note-pencil mr-1 text-primary"></i> 3. Additional Notes
                        </label>
                        <textarea name="notes" class="form-control custom-input" rows="3" placeholder="Tell us anything important (e.g., gate codes, specific requests)..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-4 py-3 font-weight-bold shadow-sm" id="submit-btn" disabled>
                        Confirm Appointment
                    </button>
                </form>
            </div>
        </div>

        {{-- Sidebar Summary --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius: 15px; position: sticky; top: 20px;">
                <h5 class="font-weight-bold mb-3">Booking Summary</h5>
                <div class="p-3 rounded mb-4" style="background-color: #F8FAFC; border: 1px solid #E2E8F0;">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ $provider->avatar_url }}" class="rounded-circle mr-3" width="45" height="45" style="object-fit: cover;">
                        <div>
                            <p class="mb-0 font-weight-bold text-dark">{{ $provider->name }}</p>
                            <span class="badge badge-pill badge-success-light">{{ ucfirst($provider->role) }}</span>
                        </div>
                    </div>
                    <div class="pt-2 border-top">
                        <p class="mb-1 small text-muted text-uppercase font-weight-bold" style="letter-spacing: 0.5px;">Service</p>
                        <p class="mb-0 font-weight-bold">{{ $service->name }}</p>
                    </div>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted"><i class="ph ph-hourglass mr-1"></i> Duration</span>
                    <span class="font-weight-medium">{{ $service->duration_minutes }} mins</span>
                </div>
                <div class="d-flex justify-content-between mb-4">
                    <span class="text-muted"><i class="ph ph-money mr-1"></i> Total Price</span>
                    <span class="font-weight-bold text-success" style="font-size: 1.2rem;">PKR {{ number_format($service->price, 0) }}</span>
                </div>

                <div class="alert alert-info py-2 px-3 border-0 small mb-3" style="background-color: #EFF6FF; color: #1E40AF; border-radius: 10px;">
                    <i class="ph ph-info mr-1"></i> Your request will be sent to the provider for approval.
                </div>

                <div class="alert alert-warning py-2 px-3 border-0 small mb-0" style="background-color: #FFFBEB; color: #B45309; border-radius: 10px;">
                    <i class="ph ph-wallet mr-1"></i> Payment is handled physically at the service location.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Slot Grid Styling */
    .slot-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px; width: 100%; }
    .slot-item { 
        padding: 10px; text-align: center; border: 1px solid var(--border-color); 
        border-radius: 10px; cursor: pointer; transition: 0.22s ease; background: white;
        font-size: 0.88rem; font-weight: 600; color: var(--text-muted);
    }
    .slot-item:hover { border-color: var(--primary); background: rgba(30, 124, 98, 0.05); color: var(--primary); }
    .slot-item.active { background: var(--primary-gradient) !important; color: white !important; border-color: var(--primary); box-shadow: var(--primary-glow); }
    
    .badge-success-light { background: #D1FAE5; color: #065F46; font-size: 0.7rem; }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    const datePicker = $('#date-picker');
    const slotsContainer = $('#slots-container');
    const slotsLoader = $('#slots-loader');
    const timeInput = $('#selected-time');
    const submitBtn = $('#submit-btn');

    datePicker.on('change', function() {
        const date = $(this).val();
        if(!date) return;

        // Reset UI
        slotsContainer.empty();
        slotsLoader.removeClass('d-none');
        timeInput.val('');
        submitBtn.prop('disabled', true);

        // AJAX Request to fetch slots
        $.ajax({
            url: "{{ route('api.slots') }}",
            data: {
                provider_id: "{{ $provider->id }}",
                service_id: "{{ $service->id }}",
                date: date
            },
            success: function(response) {
                slotsLoader.addClass('d-none');
                
                if(response.length === 0) {
                    slotsContainer.html('<div class="alert alert-warning w-100 small">No available slots for this date. Please try another day.</div>');
                    return;
                }

                response.forEach(slot => {
                    const slotBtn = $(`<div class="slot-item" data-time="${slot.raw_time}">${slot.time}</div>`);
                    slotsContainer.append(slotBtn);
                });
            },
            error: function() {
                slotsLoader.addClass('d-none');
                slotsContainer.html('<div class="alert alert-danger w-100 small">Error loading slots. Please refresh.</div>');
            }
        });
    });

    // Handle Slot Selection
    $(document).on('click', '.slot-item', function() {
        if($(this).hasClass('disabled')) return;
        
        $('.slot-item').removeClass('active');
        $(this).addClass('active');
        timeInput.val($(this).data('time'));
        submitBtn.prop('disabled', false);
    });

    // Real-time Slot Disabling
    if (typeof window.Echo !== 'undefined') {
        window.Echo.channel('appointments.slots')
            .listen('BookingCreated', (e) => {
                const bookedDate = e.appointment.start_time.split(' ')[0];
                const bookedTime = e.appointment.start_time.split(' ')[1]; // Assuming H:i:s
                
                // Compare with current view
                if (datePicker.val() === bookedDate && "{{ $provider->id }}" == e.appointment.provider_id) {
                    const slotToDisable = $(`.slot-item[data-time="${bookedTime}"]`);
                    if (slotToDisable.length) {
                        slotToDisable.addClass('disabled')
                                     .css({'text-decoration': 'line-through', 'opacity': '0.5', 'cursor': 'not-allowed'})
                                     .attr('title', 'Just booked by another user');
                        
                        if (slotToDisable.hasClass('active')) {
                            slotToDisable.removeClass('active');
                            timeInput.val('');
                            submitBtn.prop('disabled', true);
                            Swal.fire({
                                title: 'Slot Taken!',
                                text: 'This slot was just booked by someone else. Please select another time.',
                                icon: 'warning',
                                toast: true,
                                position: 'top-end'
                            });
                        }
                    }
                }
            });
    }
});
</script>
@endpush