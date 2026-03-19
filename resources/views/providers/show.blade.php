@extends('layouts.app')

@section('content')
<div class="provider-page pb-5">

    {{-- Hero Banner --}}
    <div class="provider-hero-banner" style="background: linear-gradient(135deg, #1F7A63 0%, #2DA884 100%); height: 200px; border-radius: 0 0 32px 32px; position: relative; overflow: hidden;">
        <div style="position: absolute; inset: 0; background: url('https://images.unsplash.com/photo-1600880292203-757bb62b4baf?auto=format&fit=crop&q=60&w=1200') center/cover; opacity: 0.1;"></div>
    </div>

    <div class="container-fluid px-4 px-lg-5" style="margin-top: -60px;">

        {{-- ===== HERO CARD ===== --}}
        <div class="card border-0 shadow-soft bg-white mb-4" style="border-radius: 24px;">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-column flex-md-row align-items-start gap-4">
                    
                    {{-- Avatar --}}
                    <div class="avatar-hero-wrap flex-shrink-0">
                        <img src="{{ $provider->avatar_url }}" alt="{{ $provider->name }}"
                             class="rounded-circle border border-white shadow-sm"
                             style="width: 130px; height: 130px; object-fit: cover; border-width: 4px !important; margin-top: -50px;">
                        <span class="avail-dot {{ $provider->availabilities->count() > 0 ? 'avail-green' : 'avail-red' }}"></span>
                    </div>

                    {{-- Info --}}
                    <div class="flex-grow-1 mt-1 mt-md-0" style="margin-top: 12px;">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <h2 class="font-weight-bold text-dark mb-0 ls-tight" style="font-size: 1.85rem;">
                                {{ $provider->business_name ?? $provider->name }}
                            </h2>
                            @if($provider->availabilities->count() > 0)
                                <span class="badge rounded-pill px-3 py-1 font-weight-bold" style="background: rgba(31,122,99,0.1); color: #1F7A63; font-size: 0.75rem;">
                                    <i class="ph-fill ph-check-circle mr-1"></i> Available Today
                                </span>
                            @else
                                <span class="badge rounded-pill px-3 py-1 font-weight-bold" style="background: rgba(239,68,68,0.1); color: #EF4444; font-size: 0.75rem;">
                                    <i class="ph-fill ph-x-circle mr-1"></i> Busy
                                </span>
                            @endif
                        </div>

                        <div class="font-weight-bold mb-2" style="color: #1F7A63; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.07em;">
                            {{ $provider->specialization ?? ($provider->business_category ?? 'Professional Service') }}
                        </div>

                        <div class="d-flex flex-wrap align-items-center gap-3 text-muted" style="font-size: 0.9rem;">
                            <span class="font-weight-bold" style="color: #F59E0B;">
                                <i class="ph-fill ph-star mr-1"></i>
                                {{ $provider->average_rating > 0 ? number_format($provider->average_rating, 1) : 'New' }}
                                <span class="text-muted font-weight-normal ml-1">({{ $provider->reviews_count }} reviews)</span>
                            </span>
                            @if($provider->city)
                                <span><i class="ph ph-map-pin mr-1 opacity-75"></i>{{ $provider->city }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Desktop Scroll-to-Book --}}
                    <div class="d-none d-lg-flex flex-column align-items-end gap-3 pt-2 flex-shrink-0">
                        <button class="btn btn-brand-primary rounded-pill px-5 py-3 font-weight-bold shadow-sm" onclick="scrollToBooking()">
                            <i class="ph-fill ph-calendar-plus mr-2"></i> Book Appointment
                        </button>
                    </div>
                </div>

                {{-- ===== QUICK INFO STRIP ===== --}}
                <div class="row mt-4 pt-4 border-top border-light g-3 quick-info-strip">
                    <div class="col-6 col-md-3">
                        <div class="quick-info-card text-center">
                            <div class="qi-icon"><i class="ph-fill ph-briefcase"></i></div>
                            <div class="qi-value">{{ $provider->services->count() }}</div>
                            <div class="qi-label">Services</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="quick-info-card text-center">
                            <div class="qi-icon" style="color: #F59E0B;"><i class="ph-fill ph-star"></i></div>
                            <div class="qi-value">{{ $provider->average_rating > 0 ? number_format($provider->average_rating, 1) : 'N/A' }}</div>
                            <div class="qi-label">Rating</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="quick-info-card text-center">
                            <div class="qi-icon"><i class="ph-fill ph-chat-dots"></i></div>
                            <div class="qi-value">{{ $provider->reviews_count }}</div>
                            <div class="qi-label">Reviews</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="quick-info-card text-center">
                            <div class="qi-icon" style="color: #1F7A63;"><i class="ph-fill ph-seal-check"></i></div>
                            <div class="qi-value text-success font-weight-bold" style="font-size: 1.1rem;">✔</div>
                            <div class="qi-label">Verified</div>
                        </div>
                    </div>
                </div>

                {{-- Mobile Book Button --}}
                <div class="mt-4 d-block d-lg-none">
                    <button class="btn btn-brand-primary w-100 rounded-pill py-3 font-weight-bold" onclick="scrollToBooking()">
                        <i class="ph-fill ph-calendar-plus mr-2"></i> Book Appointment
                    </button>
                </div>
            </div>
        </div>

        {{-- ===== MAIN LAYOUT ===== --}}
        <div class="row" style="gap: 0;">

            {{-- LEFT — Tabs Content --}}
            <div class="col-lg-7 pr-lg-4 mb-4">

                {{-- Tab Nav --}}
                <ul class="nav nav-tabs border-0 mb-4 gap-2" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link saas-tab active px-4 py-2 font-weight-bold rounded-pill" id="services-tab"
                                data-toggle="tab" data-target="#services" type="button">Services</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link saas-tab px-4 py-2 font-weight-bold rounded-pill" id="about-tab"
                                data-toggle="tab" data-target="#about" type="button">About</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link saas-tab px-4 py-2 font-weight-bold rounded-pill" id="reviews-tab"
                                data-toggle="tab" data-target="#reviews" type="button">Reviews</button>
                    </li>
                </ul>

                <div class="tab-content">

                    {{-- ===== SERVICES TAB ===== --}}
                    <div class="tab-pane fade show active" id="services" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3 px-1">
                            <h5 class="font-weight-bold text-dark mb-0">Available Services</h5>
                            <span class="badge rounded-pill font-weight-bold px-3 py-2" style="background: rgba(31,122,99,0.1); color: #1F7A63;">{{ $provider->services->count() }} Listed</span>
                        </div>

                        @forelse ($provider->services as $service)
                            <div class="service-row-card mb-3"
                                 id="service-card-{{ $service->id }}"
                                 onclick="selectService('{{ $service->id }}', '{{ addslashes($service->name) }}', '{{ number_format($service->price, 0) }}', '{{ $service->duration }}')"
                                 style="cursor: pointer;">

                                {{-- Left: icon + info --}}
                                <div class="d-flex align-items-center" style="min-width: 0; flex: 1;">
                                    {{-- Icon --}}
                                    <div class="src-icon-col">
                                        <i class="ph ph-scissors"></i>
                                    </div>
                                    {{-- Info --}}
                                    <div class="src-info-col">
                                        <div class="src-name">{{ $service->name }}</div>
                                        @if($service->description)
                                            <div class="src-desc">{{ Str::limit($service->description, 70) }}</div>
                                        @endif
                                        <span class="src-duration">
                                            <i class="ph ph-clock"></i> {{ $service->duration }} mins
                                        </span>
                                    </div>
                                </div>

                                {{-- Right: price + book --}}
                                <div class="src-cta-col">
                                    <div class="src-price">PKR {{ number_format($service->price, 0) }}</div>
                                    <button class="src-book-btn"
                                            onclick="event.stopPropagation(); selectService('{{ $service->id }}', '{{ addslashes($service->name) }}', '{{ number_format($service->price, 0) }}', '{{ $service->duration }}')">Book Now</button>
                                </div>

                            </div>
                        @empty
                            <div class="text-center py-5 card border-0 shadow-soft bg-white" style="border-radius: 16px;">
                                <i class="ph ph-calendar-x d-block mb-3" style="font-size: 3.5rem; color: #CBD5E1;"></i>
                                <h6 class="font-weight-bold text-muted">No Services Available</h6>
                                <p class="small text-muted mb-0">This provider hasn't listed any active services yet.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- ===== ABOUT TAB ===== --}}
                    <div class="tab-pane fade" id="about" role="tabpanel">
                        <div class="card border-0 shadow-soft bg-white p-4 p-md-5 mb-4" style="border-radius: 20px;">
                            <h5 class="font-weight-bold text-dark mb-4">About Provider</h5>
                            <div class="mb-4">
                                <h6 class="font-weight-bold small text-uppercase ls-widest mb-2" style="color: #1F7A63;">
                                    <i class="ph ph-user mr-1"></i> Biography
                                </h6>
                                <p class="text-dark" style="line-height: 1.8;">{{ $provider->bio ?? 'No biography provided yet.' }}</p>
                            </div>
                            <div class="mb-5">
                                <h6 class="font-weight-bold small text-uppercase ls-widest mb-2" style="color: #1F7A63;">
                                    <i class="ph ph-medal mr-1"></i> Expertise
                                </h6>
                                <p class="text-dark" style="line-height: 1.8;">{{ $provider->specialization ?? ($provider->business_category ?? 'General Professional Services') }}</p>
                            </div>

                            {{-- Location & Contact --}}
                            <h6 class="font-weight-bold text-dark mb-4 border-top border-light pt-4">Location &amp; Contact</h6>
                            <div class="row g-3">

                                {{-- Address --}}
                                <div class="col-12 mb-3">
                                    <div class="contact-info-item w-100 m-0">
                                        <div class="cii-icon"><i class="ph-fill ph-map-pin"></i></div>
                                        <div style="min-width: 0; flex: 1;">
                                            <div class="cii-label">Address</div>
                                            <div class="cii-value" title="{{ $provider->address ? $provider->address . ', ' . $provider->city : ($provider->city ?? 'Remote') }}">{{ $provider->address ? $provider->address . ', ' . $provider->city : ($provider->city ?? 'Remote') }}</div>
                                        </div>
                                    </div>
                                </div>

                                @if($provider->phone)
                                {{-- Phone --}}
                                <div class="col-12 mb-3">
                                    <div class="contact-info-item w-100 m-0">
                                        <div class="cii-icon"><i class="ph-fill ph-phone"></i></div>
                                        <div style="min-width: 0; flex: 1;">
                                            <div class="cii-label">Phone</div>
                                            <div class="cii-value" title="{{ $provider->phone }}">{{ $provider->phone }}</div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                {{-- Email --}}
                                <div class="col-12 mb-3">
                                    <div class="contact-info-item w-100 m-0">
                                        <div class="cii-icon"><i class="ph-fill ph-envelope"></i></div>
                                        <div style="min-width: 0; flex: 1;">
                                            <div class="cii-label">Email</div>
                                            <div class="cii-value" title="{{ $provider->user->email ?? '' }}">{{ $provider->user->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- Business Hours --}}
                        <div class="card border-0 shadow-soft bg-white p-4 mb-4" style="border-radius: 20px;">
                            <h5 class="font-weight-bold text-dark mb-4">Business Hours</h5>
                            @if($provider->availabilities->count() > 0)
                                @php
                                    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                    $availByDay = $provider->availabilities->keyBy('day_of_week');
                                @endphp
                                <ul class="list-unstyled mb-0">
                                    @foreach($days as $index => $day)
                                        <li class="d-flex justify-content-between py-2 {{ !$loop->last ? 'border-bottom border-light' : '' }}">
                                            <span class="small {{ isset($availByDay[$index]) ? 'text-dark font-weight-bold' : 'text-muted' }}">{{ $day }}</span>
                                            @if(isset($availByDay[$index]))
                                                <span class="small font-weight-bold" style="color: #1F7A63;">
                                                    {{ \Carbon\Carbon::parse($availByDay[$index]->start_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($availByDay[$index]->end_time)->format('g:i A') }}
                                                </span>
                                            @else
                                                <span class="small text-danger opacity-50 font-weight-bold">Closed</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted small mb-0">No hours specified.</p>
                            @endif
                        </div>
                    </div>

                    {{-- ===== REVIEWS TAB ===== --}}
                    <div class="tab-pane fade" id="reviews" role="tabpanel">
                        <div class="card border-0 shadow-soft bg-white p-4 p-md-5" style="border-radius: 20px;">

                            {{-- Rating Summary --}}
                            <div class="d-flex justify-content-between align-items-center mb-4 pb-4 border-bottom border-light">
                                <div>
                                    <h5 class="font-weight-bold text-dark mb-1">Customer Reviews</h5>
                                    <p class="text-muted small mb-0">{{ $provider->reviews_count }} verified ratings</p>
                                </div>
                                <div class="text-center">
                                    <div class="font-weight-bold text-dark" style="font-size: 2.5rem; line-height: 1;">{{ $provider->average_rating > 0 ? number_format($provider->average_rating,1) : '–' }}</div>
                                    <div class="text-warning d-flex justify-content-center mt-1" style="font-size: 1.1rem;">
                                        @for($i = 0; $i < 5; $i++)
                                            <i class="ph{{ $i < $provider->average_rating ? '-fill' : '' }} ph-star ml-1"></i>
                                        @endfor
                                    </div>
                                </div>
                            </div>

                            @forelse ($provider->reviews as $review)
                                <div class="d-flex mb-4 pb-4 {{ !$loop->last ? 'border-bottom border-light' : '' }}">
                                    <img src="{{ $review->customer->avatar_url }}" class="rounded-circle mr-3 shadow-sm border border-light"
                                         style="width: 48px; height: 48px; object-fit: cover; flex-shrink: 0;">
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <h6 class="font-weight-bold mb-0 text-dark">{{ $review->customer->name }}</h6>
                                            <div class="text-warning d-flex" style="font-size: 0.85rem;">
                                                @for($i = 0; $i < 5; $i++)<i class="ph{{ $i < $review->rating ? '-fill' : '' }} ph-star ml-1"></i>@endfor
                                            </div>
                                        </div>
                                        <div class="text-muted small mb-2 font-weight-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">{{ $review->created_at->format('M d, Y') }}</div>
                                        <p class="text-dark mb-0" style="font-size: 0.95rem; line-height: 1.6;">"{{ $review->review_text }}"</p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <i class="ph-fill ph-star d-block mb-3" style="font-size: 3.5rem; color: #cbd5e1; opacity: 0.5;"></i>
                                    <h6 class="font-weight-bold text-muted">No Reviews Yet</h6>
                                    <p class="text-muted small mb-0">Be the first to book and leave a review.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>{{-- end tab-content --}}
            </div>

            {{-- RIGHT — Sticky Booking Panel --}}
            <div class="col-lg-5 pl-lg-4" id="booking-panel-col">
                <div class="sticky-top" id="booking-sticky" style="top: 80px;">

                    {{-- Booking Panel Card --}}
                    <div class="booking-panel-card overflow-hidden">

                        {{-- Panel Header --}}
                        <div class="booking-panel-header">
                            <div class="d-flex align-items-center">
                                <div class="panel-icon-wrap mr-3 flex-shrink-0">
                                    <i class="ph-fill ph-calendar-check" style="color: #1F7A63; font-size: 1.4rem;"></i>
                                </div>
                                <div>
                                    <h4 class="font-weight-bold text-white mb-1" style="font-size: 1.25rem;">Book Appointment</h4>
                                    <p class="text-white mb-0 opacity-75" id="panelServiceLabel" style="font-size: 0.9rem;">Select a service from the left</p>
                                </div>
                            </div>
                        </div>

                        {{-- Panel Body --}}
                        <div class="p-4 booking-panel-body">

                            {{-- No Service Selected State --}}
                            <div id="noServiceState" class="text-center py-5">
                                <i class="ph ph-cursor-click d-block mb-3 no-service-icon" style="font-size: 3.5rem;"></i>
                                <h6 class="font-weight-bold mb-0 no-service-text">Click a service to begin booking</h6>
                            </div>

                            {{-- Booking Form (hidden until service selected) --}}
                            <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm" class="d-none">
                                @csrf
                                <input type="hidden" name="provider_id" value="{{ $provider->id }}">
                                <input type="hidden" name="service_id" id="panel_service_id">
                                @if(auth()->check())
                                    <input type="hidden" name="customer_name" value="{{ auth()->user()->name }}">
                                    <input type="hidden" name="customer_phone" value="{{ auth()->user()->phone_number }}">
                                @endif

                                {{-- Selected Service Preview --}}
                                <div id="selectedServicePreview" class="p-3 mb-4 rounded-pill d-flex justify-content-between align-items-center" style="background: rgba(31,122,99,0.08); border: 1px solid rgba(31,122,99,0.15);">
                                    <div>
                                        <div class="font-weight-bold text-dark small" id="previewServiceName">–</div>
                                        <div class="text-muted" style="font-size: 0.78rem;" id="previewServiceDuration">–</div>
                                    </div>
                                    <div class="font-weight-bold" style="color: #1F7A63; font-size: 1.1rem;" id="previewServicePrice">PKR 0</div>
                                </div>

                                {{-- Date --}}
                                <div class="mb-4">
                                    <label class="font-weight-bold text-dark small text-uppercase ls-widest mb-2 d-block">
                                        <i class="ph-fill ph-calendar-blank mr-1" style="color: #1F7A63;"></i> 1. Pick a Date
                                    </label>
                                    <input type="date" name="date" id="datePicker"
                                           class="form-control bg-white font-weight-bold"
                                           style="height: 52px; border-radius: 12px; border: 1.5px solid #E5E7EB;"
                                           min="{{ date('Y-m-d') }}" required>
                                </div>

                                {{-- Time Slots --}}
                                <div class="mb-4">
                                    <label class="font-weight-bold text-dark small text-uppercase ls-widest mb-2 d-block">
                                        <i class="ph-fill ph-clock mr-1" style="color: #1F7A63;"></i> 2. Select Time
                                    </label>
                                    <div id="slotsLoader" class="text-center d-none py-3">
                                        <div class="spinner-border spinner-border-sm" style="color: #1F7A63;" role="status"></div>
                                        <span class="small ml-2 text-muted">Loading slots...</span>
                                    </div>
                                    <div id="slotsContainer" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(85px, 1fr)); gap: 8px;">
                                        <div class="text-center text-muted small py-3 w-100" style="grid-column: 1 / -1;">Select a date first</div>
                                    </div>
                                    <input type="hidden" name="time" id="selectedTime" required>
                                </div>

                                {{-- Notes --}}
                                <div class="mb-4">
                                    <label class="font-weight-bold text-dark small text-uppercase ls-widest mb-2 d-block">
                                        <i class="ph-fill ph-note-pencil mr-1" style="color: #1F7A63;"></i> 3. Notes (Optional)
                                    </label>
                                    <textarea name="notes" rows="2" class="form-control bg-white"
                                              style="border-radius: 12px; border: 1.5px solid #E5E7EB; resize: none;"
                                              placeholder="Any extra details for the provider..."></textarea>
                                </div>

                                {{-- Price Summary --}}
                                <div class="d-flex justify-content-between align-items-center p-3 mb-4 rounded-xl" style="background: rgba(31,122,99,0.07);">
                                    <div>
                                        <div class="text-muted small font-weight-bold text-uppercase ls-widest" style="font-size: 0.7rem;">Total</div>
                                        <div class="text-muted small"><i class="ph ph-wallet mr-1"></i> Pay on-site</div>
                                    </div>
                                    <div class="font-weight-bold" style="color: #1F7A63; font-size: 1.4rem;" id="panelPriceLabel">PKR 0</div>
                                </div>

                                <button type="submit" id="submitBtn" disabled
                                        class="btn btn-brand-primary w-100 rounded-pill py-3 font-weight-bold shadow-sm" style="font-size: 1rem;">
                                    <i class="ph-fill ph-calendar-check mr-2"></i> Confirm Appointment
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Availability Preview --}}
                    @if($provider->availabilities->count() > 0)
                    @php
                        $todayIndex = now()->dayOfWeek;
                        $availByDay = $provider->availabilities->keyBy('day_of_week');
                        $nextSlotText = 'Contact provider for availability';
                        foreach(range($todayIndex, $todayIndex + 6) as $offset) {
                            $dayIdx = $offset % 7;
                            if(isset($availByDay[$dayIdx])) {
                                $dayName = $dayIdx === $todayIndex ? 'Today' : ($dayIdx === ($todayIndex+1)%7 ? 'Tomorrow' : \Carbon\Carbon::now()->next($dayIdx)->format('l'));
                                $nextSlotText = $dayName . ' at ' . \Carbon\Carbon::parse($availByDay[$dayIdx]->start_time)->format('g:i A');
                                break;
                            }
                        }
                    @endphp
                    <div class="mt-3 p-3 bg-white d-flex align-items-center" style="border: 1.5px solid #F1F5F9; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: rgba(31,122,99,0.1); margin-right: 14px;">
                            <i class="ph-fill ph-clock" style="color: #1F7A63; font-size: 1.2rem;"></i>
                        </div>
                        <div>
                            <div class="font-weight-bold" style="font-size: 0.65rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 2px;">Next Available</div>
                            <div class="font-weight-bold text-dark" style="font-size: 0.95rem;">{{ $nextSlotText }}</div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>

        </div>{{-- end row --}}
    </div>
</div>

@push('styles')
<style>
    /* ===== Provider Page Theme ===== */
    .provider-page {
        --primary: #1F7A63;
        --primary-light: rgba(31,122,99,0.1);
        --primary-hover: #175d4b;
    }

    /* Brand Button */
    .btn-brand-primary {
        background: linear-gradient(135deg, #1F7A63, #2DA884) !important;
        border: none !important;
        color: white !important;
        transition: all 0.2s ease;
    }
    .btn-brand-primary:hover, .btn-brand-primary:focus {
        background: linear-gradient(135deg, #175d4b, #1F7A63) !important;
        color: white !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(31,122,99,0.3) !important;
    }
    .btn-brand-primary:disabled {
        opacity: 0.5;
        transform: none !important;
        box-shadow: none !important;
        cursor: not-allowed;
    }

    /* Soft shadow */
    .shadow-soft { box-shadow: 0 4px 20px rgba(0,0,0,0.05) !important; }

    /* Tabs */
    .saas-tab {
        color: #6B7280 !important;
        background: transparent;
        border: 2px solid transparent !important;
        transition: all 0.2s ease;
    }
    .saas-tab:hover { background: rgba(0,0,0,0.03); color: #1e293b !important; }
    .saas-tab.active {
        color: var(--primary) !important;
        background: rgba(31,122,99,0.08) !important;
        border-color: rgba(31,122,99,0.15) !important;
    }

    /* Service Cards */
    .saas-service-card {
        border: 1.5px solid #F3F4F6 !important;
        transition: all 0.2s ease;
    }
    .saas-service-card:hover {
        border-color: var(--primary) !important;
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(31,122,99,0.1) !important;
    }
    .saas-service-card.active-service {
        border-color: var(--primary) !important;
        background: rgba(31,122,99,0.03) !important;
        box-shadow: 0 4px 16px rgba(31,122,99,0.12) !important;
    }

    /* Service Icon */
    .service-icon-wrap {
        width: 50px; height: 50px;
        background: rgba(31,122,99,0.1);
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        color: #1F7A63; font-size: 1.4rem;
        flex-shrink: 0;
    }

    /* Quick Info Strip */
    .quick-info-strip { margin: 0 -4px; }
    .quick-info-card {
        padding: 12px 8px;
        border-radius: 12px;
        background: #FAFBFC;
        border: 1px solid #F3F4F6;
        transition: all 0.2s ease;
    }
    .quick-info-card:hover { background: rgba(31,122,99,0.05); border-color: rgba(31,122,99,0.2); }
    .qi-icon { font-size: 1.3rem; color: #1F7A63; margin-bottom: 4px; }
    .qi-value { font-size: 1.25rem; font-weight: 800; color: #1e293b; margin-bottom: 2px; }
    .qi-label { font-size: 0.72rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.08em; color: #94A3B8; }

    /* Availability Dot */
    .avatar-hero-wrap { position: relative; display: inline-block; }
    .avail-dot {
        position: absolute; bottom: 8px; right: 8px;
        width: 16px; height: 16px;
        border-radius: 50%; border: 3px solid white;
    }
    .avail-green { background: #22C55E; }
    .avail-red { background: #EF4444; }

    /* Contact Icons */
    .contact-icon-wrap {
        width: 36px; height: 36px;
        background: rgba(31,122,99,0.1);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        color: #1F7A63; font-size: 1.1rem; flex-shrink: 0;
    }

    /* Time Slot Items */
    .slot-item {
        padding: 9px 4px;
        text-align: center;
        border: 1.5px solid #E5E7EB;
        border-radius: 10px;
        cursor: pointer;
        font-size: 0.85rem; font-weight: 700; color: #4B5563;
        background: white;
        transition: all 0.15s ease;
    }
    .slot-item:hover { border-color: rgba(31,122,99,0.4); background: rgba(31,122,99,0.05); color: #1F7A63; }
    .slot-item.active { background: #1F7A63 !important; color: white !important; border-color: #1F7A63; box-shadow: 0 3px 10px rgba(31,122,99,0.25); }
    .slot-item.disabled { opacity: 0.4; text-decoration: line-through; background: #F8FAFC; cursor: not-allowed; }

    /* Inputs focus */
    .provider-page .form-control:focus {
        border-color: #1F7A63 !important;
        box-shadow: 0 0 0 3px rgba(31,122,99,0.15) !important;
    }

    /* Booking Panel */
    .booking-panel-card {
        background: #ffffff;
        border: 1.5px solid #F1F5F9;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }
    .booking-panel-header {
        background: linear-gradient(135deg, #1F7A63, #2DA884);
        padding: 24px;
    }
    .panel-icon-wrap {
        width: 48px; height: 48px;
        background: #ffffff;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .booking-panel-body { background: #ffffff; }
    .no-service-icon { color: #CBD5E1; }
    .no-service-text { color: #64748b; }

    /* Dark Mode Improvements */
    body.dark-mode .booking-panel-card {
        background: #0B1120;
        border-color: #1E293B;
        box-shadow: 0 10px 40px rgba(0,0,0,0.5);
    }
    body.dark-mode .booking-panel-body { background: #0f172a; } /* Slightly lighter navy */
    body.dark-mode .panel-icon-wrap { background: #1e293b; }
    body.dark-mode .no-service-icon { color: #475569; }
    body.dark-mode .no-service-text { color: #94A3B8; }
    
    body.dark-mode .booking-panel-body label {
        color: #f1f5f9 !important; /* Very bright */
        font-weight: 600 !important;
    }

    body.dark-mode .booking-panel-body label i {
        color: #10b981 !important; /* Standard Emerald Green */
    }
    
    body.dark-mode #selectedServicePreview {
        background: rgba(16, 185, 129, 0.08) !important;
        border-color: rgba(16, 185, 129, 0.2) !important;
    }
    
    body.dark-mode #datePicker, body.dark-mode textarea {
        background: #111827 !important; /* Back to standard dark bg */
        border: 1px solid #1f2937 !important;
        color: #f3f4f6 !important;
    }
    
    body.dark-mode #datePicker:focus, body.dark-mode textarea:focus {
        border-color: #10B981 !important;
        background: #0B1120 !important;
    }

    body.dark-mode #panelPriceLabel { color: #10b981 !important; }
    body.dark-mode .text-secondary { color: #94a3b8 !important; }

    body.dark-mode #datePicker::-webkit-calendar-picker-indicator {
        filter: invert(1) brightness(1.2) !important;
        cursor: pointer;
    }

    /* Soft Themed Alerts */
    .alert-warning-soft { background: #fffbeb; color: #92400e; border-radius: 12px; font-weight: 600; font-size: 0.875rem; }
    .alert-danger-soft { background: #fef2f2; color: #991b1b; border-radius: 12px; font-weight: 600; font-size: 0.875rem; }
    
    body.dark-mode .alert-warning-soft { background: rgba(245, 158, 11, 0.1) !important; color: #fbbf24 !important; border: 1px solid rgba(245, 158, 11, 0.2); }
    body.dark-mode .alert-danger-soft { background: rgba(239, 68, 68, 0.1) !important; color: #f87171 !important; border: 1px solid rgba(239, 68, 68, 0.2); }

    body.dark-mode .booking-panel-body textarea::placeholder, 
    body.dark-mode .booking-panel-body input::placeholder {
        color: #94a3b8 !important;
        opacity: 0.8 !important;
    }

    body.dark-mode .booking-panel-body .text-muted,
    body.dark-mode .booking-panel-body #slotsContainer .text-muted {
        color: #94a3b8 !important;
        font-weight: 500;
    }

    /* Fixed Clean Service Cards */
    .service-row-card {
        display: flex;
        align-items: center;
        background: #fff;
        border: 1.5px solid #F1F5F9;
        border-radius: 16px;
        padding: 12px 16px;
        transition: all 0.2s ease;
        min-height: 80px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }
    .service-row-card:hover, .service-row-card.active-service {
        border-color: #1F7A63;
        box-shadow: 0 6px 20px rgba(31,122,99,0.1);
        transform: translateY(-2px);
    }
    .service-row-card.active-service {
        background: #f8fbf9;
    }
    
    .src-icon-col {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, rgba(31,122,99,0.1), rgba(45,168,132,0.1));
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        color: #1F7A63;
        font-size: 1.4rem;
        margin-right: 16px;
        flex-shrink: 0;
    }
    
    .src-info-col {
        flex: 1;
        min-width: 0;
        padding-right: 16px;
    }
    .src-name {
        font-weight: 700;
        color: #1e293b;
        font-size: 1rem;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .src-desc {
        font-size: 0.8rem;
        color: #64748B;
        margin-bottom: 6px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .src-duration {
        display: inline-flex;
        align-items: center;
        background: #f1f5f9;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 50px;
    }
    .src-duration i { margin-right: 4px; }
    
    /* ===== DARK MODE OPTIMIZATIONS ===== */
    body.dark-mode .service-row-card {
        background: #0B1120 !important;
        border-color: #1E293B !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3) !important;
    }
    body.dark-mode .service-row-card:hover {
        border-color: rgba(16, 185, 129, 0.5) !important;
        background: #0F172A !important;
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.15) !important;
        transform: translateY(-2px);
    }
    body.dark-mode .src-icon-col {
        background: rgba(16, 185, 129, 0.1) !important;
        color: #10B981 !important;
    }
    body.dark-mode .src-name {
        color: #F8FAFC !important;
    }
    body.dark-mode .src-desc {
        color: #94A3B8 !important;
    }
    body.dark-mode .src-duration {
        background: #1E293B !important;
        color: #CBD5E1 !important;
    }
    body.dark-mode .src-price {
        color: #10B981 !important;
    }

    body.dark-mode .bg-white {
        background-color: #0B1120 !important;
        border-color: #1E293B !important;
    }
    body.dark-mode .text-dark {
        color: #F8FAFC !important;
    }
    body.dark-mode .border-light {
        border-color: #1E293B !important;
    }
    body.dark-mode .contact-info-item,
    body.dark-mode .quick-info-card,
    body.dark-mode .card.bg-white {
        background: #0B1120 !important;
        border: 1px solid #1E293B !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3) !important;
    }
    body.dark-mode .cii-label { color: #94A3B8 !important; }
    body.dark-mode .cii-value { color: #F8FAFC !important; }
    body.dark-mode .qi-label { color: #94A3B8 !important; }
    body.dark-mode .qi-value { color: #F8FAFC !important; }

    .src-cta-col {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-shrink: 0;
        border-left: 1px solid #f1f5f9;
        padding-left: 16px;
    }
    .src-price {
        font-weight: 800;
        font-size: 1.1rem;
        color: #1e293b;
        min-width: 90px;
        text-align: right;
    }
    .src-book-btn {
        background: #1e293b;
        color: #fff;
        border: none;
        border-radius: 50px;
        padding: 0 18px;
        height: 38px;
        font-weight: 700;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }
    .service-row-card:hover .src-book-btn {
        background: linear-gradient(135deg, #1F7A63, #2DA884);
        box-shadow: 0 4px 12px rgba(31,122,99,0.3);
    }
    .src-book-btn:hover {
        transform: translateY(-1px);
    }

    /* Fixed Clean Contact Items */
    .contact-info-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        width: 100%;
        margin-bottom: 12px;
    }
    .contact-info-item:hover {
        border-color: #1F7A63;
        background: #fff;
        box-shadow: 0 10px 25px rgba(31,122,99,0.08);
        transform: translateY(-2px);
    }
    .cii-icon {
        width: 36px;
        height: 36px;
        background: rgba(31,122,99,0.1);
        color: #1F7A63;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
        margin-right: 14px;
        flex-shrink: 0;
    }
    .cii-label {
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #94a3b8;
        margin-bottom: 2px;
    }
    .cii-value {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
        word-break: break-word;
    }

    /* Helpers */
    .ls-tight { letter-spacing: -0.02em; }
    .ls-widest { letter-spacing: 0.08em; }
    .rounded-xl { border-radius: 12px; }

    /* Dark Mode */
    body.dark-mode .card, body.dark-mode .bg-white { 
        background: var(--bg-card) !important; 
        border-color: var(--border-color) !important;
    }
    body.dark-mode .text-dark, body.dark-mode h5 { color: #ffffff !important; }
    body.dark-mode .border-light { border-color: var(--border-color) !important; }
    
    body.dark-mode .saas-service-card, body.dark-mode .quick-info-card { 
        border-color: var(--border-color) !important; 
    }
    body.dark-mode .quick-info-card { background: var(--bg-secondary) !important; }
    body.dark-mode .qi-value { color: #ffffff !important; }

    body.dark-mode .saas-tab { color: #94a3b8 !important; }
    body.dark-mode .saas-tab:hover { background: rgba(255,255,255,0.03); color: #ffffff !important; }
    body.dark-mode .saas-tab.active {
        background: rgba(30, 124, 98, 0.15) !important;
        color: #10B981 !important;
        border-color: rgba(16, 185, 129, 0.3) !important;
    }

    body.dark-mode .contact-info-item {
        background: var(--bg-secondary) !important;
        border-color: var(--border-color) !important;
    }
    body.dark-mode .contact-info-item:hover { background: #1a1a1a !important; }
    body.dark-mode .cii-value { color: #f8fafc !important; }
    
    body.dark-mode .slot-item { background: var(--bg-secondary); border-color: var(--border-color); color: var(--text-muted); }
    body.dark-mode .slot-item:hover:not(.disabled) { border-color: var(--primary); color: #ffffff; }
    body.dark-mode .slot-item.disabled { background: rgba(255,255,255,0.02); opacity: 0.3; }
    body.dark-mode #bookingForm .form-control, body.dark-mode #datePicker { 
        background: var(--bg-secondary) !important; 
        border-color: var(--border-color) !important; 
        color: var(--text-dark) !important; 
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {

    // Scroll to booking panel
    window.scrollToBooking = function() {
        document.getElementById('booking-panel-col').scrollIntoView({ behavior: 'smooth', block: 'start' });
    };

    // Select Service & Populate Panel
    window.selectService = function(id, name, price, duration) {
        // Highlight selected service card
        $('.saas-service-card').removeClass('active-service');
        $('#service-card-' + id).addClass('active-service');

        // Fill panel
        $('#panel_service_id').val(id);
        $('#panelServiceLabel').text(name + ' • ' + duration + ' mins');
        $('#previewServiceName').text(name);
        $('#previewServiceDuration').text(duration + ' mins');
        $('#previewServicePrice').text('PKR ' + price);
        $('#panelPriceLabel').text('PKR ' + price);

        // Show form, hide placeholder
        $('#noServiceState').addClass('d-none');
        $('#bookingForm').removeClass('d-none');

        // Reset booking inputs
        $('#datePicker').val('');
        $('#slotsContainer').html('<div class="text-center text-muted small py-3 w-100" style="grid-column:1/-1;">Select a date to view availability</div>');
        $('#selectedTime').val('');
        $('#submitBtn').prop('disabled', true);

        // Smooth scroll to sticky panel on mobile
        if(window.innerWidth < 992) {
            document.getElementById('booking-sticky').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Store for slot fetch
        window._currentProviderId = '{{ $provider->id }}';
        window._currentServiceId = id;
    };

    // Fetch slots on date change
    $('#datePicker').on('change', function() {
        const date = $(this).val();
        const serviceId = $('#panel_service_id').val();
        if (!date || !serviceId) return;

        $('#slotsContainer').empty();
        $('#slotsLoader').removeClass('d-none');
        $('#selectedTime').val('');
        $('#submitBtn').prop('disabled', true);

        $.ajax({
            url: "{{ route('api.slots') }}",
            data: { provider_id: '{{ $provider->id }}', service_id: serviceId, date: date },
            success: function(response) {
                $('#slotsLoader').addClass('d-none');

                if (!response.length) {
                    $('#slotsContainer').html('<div class="alert-warning-soft w-100 py-3 text-center" style="grid-column:1/-1;"><i class="ph-fill ph-warning-circle mr-1"></i> No slots for this date.</div>');
                    return;
                }

                response.forEach(slot => {
                    $('#slotsContainer').append(`<div class="slot-item" data-time="${slot.raw_time}">${slot.time}</div>`);
                });
            },
            error: function() {
                $('#slotsLoader').addClass('d-none');
                $('#slotsContainer').html('<div class="alert-danger-soft w-100 py-3 text-center" style="grid-column:1/-1;"><i class="ph-fill ph-x-circle mr-1"></i> Failed to load slots.</div>');
            }
        });
    });

    // Slot selection
    $(document).on('click', '.slot-item', function() {
        if ($(this).hasClass('disabled')) return;
        $('.slot-item').removeClass('active');
        $(this).addClass('active');
        $('#selectedTime').val($(this).data('time'));
        $('#submitBtn').prop('disabled', false);
    });

    // Real-time slot updates via WebSocket
    if (typeof window.Echo !== 'undefined') {
        window.Echo.channel('appointments.slots').listen('BookingCreated', (e) => {
            const bookedDate = e.appointment.start_time.split(' ')[0];
            const bookedTime = e.appointment.start_time.split(' ')[1];
            if ($('#datePicker').val() === bookedDate && '{{ $provider->id }}' == e.appointment.provider_id) {
                const slot = $(`.slot-item[data-time="${bookedTime}"]`);
                if (slot.length) {
                    if (slot.hasClass('active')) {
                        slot.removeClass('active');
                        $('#selectedTime').val('');
                        $('#submitBtn').prop('disabled', true);
                        Swal.fire({ title: 'Slot Taken!', text: 'This slot was just booked. Please choose another time.', icon: 'warning', toast: true, position: 'top-end', timer: 4000 });
                    }
                    slot.addClass('disabled').attr('title', 'Just booked');
                }
            }
        });
    }
});
</script>
@endpush
@endsection