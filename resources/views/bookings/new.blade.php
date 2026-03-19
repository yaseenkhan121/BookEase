@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
                <div class="progress" style="height: 8px; border-radius: 0;">
                    <div id="booking-progress" class="progress-bar bg-primary transition-all" role="progressbar" style="width: 16.6%;"></div>
                </div>

                <div class="card-body p-5">
                    {{-- Wizard Headings --}}
                    <div class="text-center mb-5">
                        <h2 class="h3 font-weight-bold text-dark mb-2" id="step-title">Select a Provider</h2>
                        <p class="text-muted" id="step-subtitle">Step 1 of 6: Choose your preferred professional</p>
                    </div>

                    <form action="{{ route('bookings.store') }}" method="POST" id="booking-wizard">
                        @csrf
                        
                        {{-- Hidden Storage for Wizard State --}}
                        <input type="hidden" name="provider_id" id="provider_id">
                        <input type="hidden" name="service_id" id="service_id">
                        <input type="hidden" name="date" id="selected_date">
                        <input type="hidden" name="time" id="selected_time">

                        {{-- Step 1: Provider Selection --}}
                        <div class="wizard-step" id="step-1">
                            <div class="row row-cols-1 row-cols-md-3 g-4">
                                @foreach($providers as $provider)
                                    <div class="col">
                                        <div class="card h-100 border provider-card cursor-pointer transition-all" data-id="{{ $provider->id }}" data-name="{{ $provider->name }}" data-spec="{{ $provider->specialization }}">
                                            <div class="card-body text-center p-4">
                                                <div class="avatar-container mb-3 mx-auto shadow-sm" style="width: 80px; height: 80px; border-radius: 50%; overflow: hidden;">
                                                    <img src="{{ $provider->avatar_url }}" class="img-fluid w-100 h-100 object-fit-cover" alt="{{ $provider->name }}">
                                                </div>
                                                <h5 class="font-weight-bold mb-1">{{ $provider->name }}</h5>
                                                <span class="badge bg-light-emerald text-emerald rounded-pill small px-3">{{ $provider->specialization ?? 'Specialist' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Step 2: Service Selection --}}
                        <div class="wizard-step d-none" id="step-2">
                            <div id="service-list" class="list-group list-group-flush border rounded-4 overflow-hidden">
                                {{-- Loaded via AJAX --}}
                            </div>
                            <div id="service-loader" class="text-center py-5 d-none">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="mt-2 text-muted">Retrieving available services...</p>
                            </div>
                        </div>

                        {{-- Step 3: Date Selection --}}
                        <div class="wizard-step d-none" id="step-3">
                            <div class="calendar-wrapper mx-auto" style="max-width: 400px;">
                                <input type="date" id="date-picker-input" class="form-control form-control-lg custom-date-input" min="{{ date('Y-m-d') }}">
                                <p class="mt-4 text-center text-muted small"><i class="ph ph-info mr-1"></i> Selection will enable time slot calculation</p>
                            </div>
                        </div>

                        {{-- Step 4: Time Slots --}}
                        <div class="wizard-step d-none" id="step-4">
                            <div id="slots-grid" class="slot-grid">
                                {{-- Loaded via AJAX --}}
                            </div>
                            <div id="slots-loader" class="text-center py-5 d-none">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="mt-2 text-muted">Calculating available time slots...</p>
                            </div>
                        </div>

                        {{-- Step 5: Details --}}
                        <div class="wizard-step d-none" id="step-5">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label font-weight-bold">Full Name</label>
                                    <input type="text" name="customer_name" id="customer_name" class="form-control form-control-lg custom-field" value="{{ auth()->user()->name }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label font-weight-bold">Phone Number</label>
                                    <input type="tel" name="customer_phone" id="customer_phone" class="form-control form-control-lg custom-field" value="{{ auth()->user()->phone_number }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label font-weight-bold">Additional Notes <span class="text-muted font-weight-normal">(Optional)</span></label>
                                    <textarea name="notes" id="booking_notes" class="form-control custom-field" rows="4" placeholder="Mention special requests or medical history..."></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Step 6: Confirmation --}}
                        <div class="wizard-step d-none" id="step-6">
                            <div class="bg-light p-4 rounded-4 border">
                                <div class="row mb-3 pb-3 border-bottom">
                                    <div class="col-6">
                                        <p class="text-muted small mb-1">Provider</p>
                                        <h6 class="font-weight-bold mb-0" id="summary-provider">-</h6>
                                    </div>
                                    <div class="col-6">
                                        <p class="text-muted small mb-1">Service</p>
                                        <h6 class="font-weight-bold mb-0" id="summary-service">-</h6>
                                    </div>
                                </div>
                                <div class="row mb-3 pb-3 border-bottom">
                                    <div class="col-6">
                                        <p class="text-muted small mb-1">Date</p>
                                        <h6 class="font-weight-bold mb-0" id="summary-date">-</h6>
                                    </div>
                                    <div class="col-6">
                                        <p class="text-muted small mb-1">Time</p>
                                        <h6 class="font-weight-bold mb-0" id="summary-time">-</h6>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="text-muted small mb-0 font-weight-bold">Total Price</p>
                                    <h4 class="text-emerald font-weight-bold mb-0" id="summary-price">PKR 0.00</h4>
                                </div>
                            </div>
                            <div class="alert alert-emerald mt-4 d-flex align-items-center">
                                <i class="ph ph-check-circle mr-3" style="font-size: 1.5rem;"></i>
                                <span class="small">By confirming, you agree to the service terms and cancellation policy.</span>
                            </div>
                        </div>

                        {{-- Navigation Buttons --}}
                        <div class="d-flex justify-content-between mt-5">
                            <button type="button" class="btn btn-light px-4 font-weight-bold d-none" id="prev-btn">
                                <i class="ph ph-arrow-left mr-2"></i> Previous
                            </button>
                            <button type="button" class="btn btn-primary px-5 font-weight-bold ml-auto" id="next-btn" disabled>
                                Next Step <i class="ph ph-arrow-right ml-2"></i>
                            </button>
                            <button type="submit" class="btn btn-emerald px-5 font-weight-bold ml-auto d-none" id="confirm-btn">
                                Confirm Booking <i class="ph ph-paper-plane-tilt ml-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .transition-all { transition: all 0.3s ease; }
    .bg-light-emerald { background-color: rgba(16, 185, 129, 0.1); }
    .text-emerald { color: #10B981; }
    .alert-emerald { background-color: #ecfdf5; border: 1px solid #10B981; color: #065f46; border-radius: 12px; }
    .btn-emerald { background: #10B981; color: white; border: none; }
    .btn-emerald:hover { background: #059669; color: white; }
    
    .provider-card { border-radius: 15px; border: 1px solid #f1f5f9 !important; }
    .provider-card:hover { transform: translateY(-5px); border-color: #10B981 !important; box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.1); }
    .provider-card.active { border-color: #10B981 !important; background-color: #f0fdf4; }
    
    .cursor-pointer { cursor: pointer; }
    .custom-field { border-radius: 12px; border: 1px solid #e2e8f0; padding: 12px 18px; }
    .custom-field:focus { border-color: #10B981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1); }
    .custom-date-input { border-radius: 50px; text-align: center; font-weight: bold; border: 2px solid #10B981; }
    
    .slot-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 12px; }
    .slot-item { 
        padding: 12px; text-align: center; border: 1px solid #e2e8f0; border-radius: 10px; 
        font-weight: 600; font-size: 0.9rem; transition: 0.2s; background: white;
    }
    .slot-item:hover { border-color: #10B981; background: #f0fdf4; color: #10B981; }
    .slot-item.active { background: #10B981 !important; color: white !important; border-color: #10B981; }

    .service-item { cursor: pointer; padding: 20px; transition: 0.2s; }
    .service-item:hover { background-color: #f8fafc; }
    .service-item.active { background-color: #f0fdf4; border-left: 4px solid #10B981 !important; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = 6;
    
    // UI Elements
    const nextBtn = document.getElementById('next-btn');
    const prevBtn = document.getElementById('prev-btn');
    const confirmBtn = document.getElementById('confirm-btn');
    const progressBar = document.getElementById('booking-progress');
    const stepTitle = document.getElementById('step-title');
    const stepSubtitle = document.getElementById('step-subtitle');
    
    // Step Elements
    const step1 = document.getElementById('step-1');
    const step2 = document.getElementById('step-2');
    const step3 = document.getElementById('step-3');
    const step4 = document.getElementById('step-4');
    const step5 = document.getElementById('step-5');
    const step6 = document.getElementById('step-6');
    
    const titles = [
        "Select a Provider",
        "Select a Service",
        "Pick a Date",
        "Select Available Time",
        "Personal Details",
        "Review & Confirm"
    ];

    const subtitles = [
        "Step 1 of 6: Choose your preferred professional",
        "Step 2 of 6: What service do you need today?",
        "Step 3 of 6: When would you like to visit?",
        "Step 4 of 6: Choose a slot that fits your schedule",
        "Step 5 of 6: Help us know you better",
        "Step 6 of 6: Almost done! Verify your details"
    ];

    function updateStepUI() {
        // Toggle Step Visibility
        [step1, step2, step3, step4, step5, step6].forEach((s, i) => {
            s.classList.toggle('d-none', i + 1 !== currentStep);
        });

        // Update Progress & Text
        progressBar.style.width = (currentStep / totalSteps * 100) + '%';
        stepTitle.textContent = titles[currentStep - 1];
        stepSubtitle.textContent = subtitles[currentStep - 1];

        // Nav Buttons Logic
        prevBtn.classList.toggle('d-none', currentStep === 1);
        nextBtn.classList.toggle('d-none', currentStep === totalSteps);
        confirmBtn.classList.toggle('d-none', currentStep !== totalSteps);

        validateStep();
    }

    function validateStep() {
        let isValid = false;
        switch(currentStep) {
            case 1: isValid = !!document.getElementById('provider_id').value; break;
            case 2: isValid = !!document.getElementById('service_id').value; break;
            case 3: isValid = !!document.getElementById('date-picker-input').value; break;
            case 4: isValid = !!document.getElementById('selected_time').value; break;
            case 5: isValid = !!document.getElementById('customer_name').value && !!document.getElementById('customer_phone').value; break;
            case 6: isValid = true; break;
        }
        nextBtn.disabled = !isValid;
    }

    // --- STEP 1: Provider Selection ---
    document.querySelectorAll('.provider-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.provider-card').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('provider_id').value = this.dataset.id;
            document.getElementById('summary-provider').textContent = this.dataset.name;
            validateStep();
            
            // Auto-advance if feeling proactive (or just wait for Next)
            // goToStep(2); 
        });
    });

    // --- STEP 2: Service Loading ---
    function loadServices(providerId) {
        const list = document.getElementById('service-list');
        const loader = document.getElementById('service-loader');
        list.innerHTML = '';
        list.classList.add('d-none');
        loader.classList.remove('d-none');

        let url = "{{ route('api.provider.services', ':id') }}";
        fetch(url.replace(':id', providerId))
            .then(res => res.json())
            .then(data => {
                loader.classList.add('d-none');
                list.classList.remove('d-none');
                if(data.length === 0) {
                    list.innerHTML = '<div class="p-5 text-center text-muted">No active services found for this provider.</div>';
                    return;
                }
                data.forEach(service => {
                    const item = document.createElement('div');
                    item.className = 'service-item list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0 border-bottom';
                    item.innerHTML = `
                        <div>
                            <h6 class="font-weight-bold mb-1">${service.name}</h6>
                            <p class="text-muted small mb-0"><i class="ph ph-clock mr-1"></i> ${service.duration} mins</p>
                        </div>
                        <h6 class="text-emerald font-weight-bold">PKR ${service.price}</h6>
                    `;
                    item.onclick = function() {
                        document.querySelectorAll('.service-item').forEach(i => i.classList.remove('active'));
                        this.classList.add('active');
                        document.getElementById('service_id').value = service.id;
                        document.getElementById('summary-service').textContent = service.name;
                        document.getElementById('summary-price').textContent = 'PKR ' + service.price;
                        validateStep();
                    };
                    list.appendChild(item);
                });
            });
    }

    // --- STEP 4: Slot Loading ---
    function loadSlots() {
        const grid = document.getElementById('slots-grid');
        const loader = document.getElementById('slots-loader');
        grid.innerHTML = '';
        grid.classList.add('d-none');
        loader.classList.remove('d-none');

        const params = new URLSearchParams({
            provider_id: document.getElementById('provider_id').value,
            service_id: document.getElementById('service_id').value,
            date: document.getElementById('date-picker-input').value
        });

        fetch("{{ route('api.slots') }}?" + params.toString())
            .then(res => res.json())
            .then(data => {
                loader.classList.add('d-none');
                grid.classList.remove('d-none');
                if(data.length === 0) {
                    grid.innerHTML = '<div class="col-12 p-4 text-center text-warning bg-light rounded-4">No available slots for this date.</div>';
                    return;
                }
                data.forEach(slot => {
                    const btn = document.createElement('div');
                    btn.className = 'slot-item cursor-pointer';
                    btn.textContent = slot.time;
                    btn.dataset.time = slot.raw_time;
                    btn.onclick = function() {
                        document.querySelectorAll('.slot-item').forEach(s => s.classList.remove('active'));
                        this.classList.add('active');
                        document.getElementById('selected_time').value = this.dataset.time;
                        document.getElementById('summary-time').textContent = this.textContent;
                        validateStep();
                    };
                    grid.appendChild(btn);
                });
            });
    }

    // Navigation Events
    nextBtn.addEventListener('click', () => {
        if (currentStep === 1) loadServices(document.getElementById('provider_id').value);
        if (currentStep === 3) {
            document.getElementById('selected_date').value = document.getElementById('date-picker-input').value;
            document.getElementById('summary-date').textContent = new Date(document.getElementById('date-picker-input').value).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            loadSlots();
        }
        if (currentStep < totalSteps) {
            currentStep++;
            updateStepUI();
        }
    });

    prevBtn.addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep--;
            updateStepUI();
        }
    });

    // Input monitoring for validation
    document.getElementById('date-picker-input').addEventListener('change', validateStep);
    document.getElementById('customer_name').addEventListener('input', validateStep);
    document.getElementById('customer_phone').addEventListener('input', validateStep);

    updateStepUI();
});
</script>
@endpush
