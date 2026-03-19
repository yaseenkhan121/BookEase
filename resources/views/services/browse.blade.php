@extends('layouts.app')

@section('title', 'Browse Services')
@section('header', 'Discover Professional Services')

@section('content')

{{-- COLLAPSING HEADER BLOCK --}}
<div class="collapsing-header bg-slate-50 mb-4" style="margin-top: -24px; z-index: 1010; padding-top: 24px;">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="display-6 fw-extrabold text-slate-900 mb-0 shrink-on-scroll">Discover Services</h2>
        <p class="text-slate-500 font-medium mb-0 hide-on-scroll d-none d-md-block">Find the perfect professional for your needs</p>
    </div>

    {{-- Search and Filter Bar --}}
    <div class="card card-premium border-0 shadow-sm mt-4 hide-on-scroll" style="border-radius: 16px;">
        <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="position-relative flex-grow-1" style="max-width: 500px;">
                <i class="ph ph-magnifying-glass position-absolute text-slate-400" style="top: 50%; left: 16px; transform: translateY(-50%); font-size: 1.25rem;"></i>
                <input type="text" class="form-control hover-scale" placeholder="Search services..." style="padding-left: 48px; border-radius: 12px; height: 48px; background: #f8fafc; border: 1px solid #e2e8f0;">
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-light hover-scale d-flex align-items-center gap-2 fw-bold" style="border-radius: 12px; height: 48px; border: 1px solid #e2e8f0;">
                    <i class="ph ph-sliders"></i> Filters
                </button>
            </div>
        </div>
    </div>
</div>
{{-- END COLLAPSING HEADER --}}

<div class="row g-4">

    {{-- Services Grid --}}
    @forelse ($services as $service)
        <div class="col-md-6 col-xl-4 d-flex align-items-stretch">
            <div class="card card-premium w-100 border-0 shadow-sm hover-scale overflow-hidden d-flex flex-column" style="border-radius: 20px;">
                <div class="card-body p-4 d-flex flex-column gap-3 flex-grow-1">
                    
                    {{-- Provider Info --}}
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <img src="{{ tap($service->provider->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($service->provider->name), function() {}) }}" alt="{{ $service->provider->name }}" class="rounded-circle shadow-sm" style="width: 48px; height: 48px; object-fit: cover;">
                        <div>
                            <h6 class="mb-0 fw-bold text-slate-800">{{ $service->provider->name }}</h6>
                            <span class="text-slate-500 small"><i class="ph ph-star-fill text-warning"></i> 4.9 (124 reviews)</span> <!-- Placeholder for actual rating if added later -->
                        </div>
                    </div>

                    {{-- Service Title & Description --}}
                    <div>
                        <h4 class="fw-extrabold text-slate-900 mb-2" style="font-size: 1.25rem;">{{ $service->name }}</h4>
                        <p class="text-slate-500 small mb-0 line-clamp-3" style="min-height: 4.5em;">{{ $service->description }}</p>
                    </div>

                    {{-- Meta Info: Duration & Price --}}
                    <div class="d-flex justify-content-between align-items-center bg-slate-50 p-3 mt-auto" style="border-radius: 12px; border: 1px solid #e2e8f0;">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-white rounded p-2 shadow-sm d-flex align-items-center justify-content-center">
                                <i class="ph ph-clock-countdown text-primary"></i>
                            </div>
                            <div>
                                <span class="d-block text-slate-400 small fw-bold" style="font-size: 0.65rem; text-transform: uppercase;">Duration</span>
                                <span class="fw-bold text-slate-700">{{ $service->readable_duration }}</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="d-block text-slate-400 small fw-bold" style="font-size: 0.65rem; text-transform: uppercase;">Price</span>
                            <span class="fw-extrabold text-success font-mono" style="font-size: 1.15rem;">{{ $service->formatted_price }}</span>
                        </div>
                    </div>

                </div>
                
                {{-- Action Footer --}}
                <div class="card-footer bg-white border-top-0 p-4 pt-0 text-center">
                    {{-- In a real scenario, this would link to a specific booking route: route('bookings.create', [$service->provider_id, $service->id]) --}}
                    {{-- Using Javascript or a generic link for now since the flow wasn't explicitly defined in the previous requests to modify the booking flow --}}
                    <a href="{{ route('bookings.new') }}" class="btn btn-modern-primary w-100 rounded-pill fw-bold" style="padding: 12px 24px;">
                        Book Appointment
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 py-5 text-center">
            <div class="mb-4 mx-auto d-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm" style="width: 100px; height: 100px;">
                <i class="ph ph-magnifying-glass-minus text-slate-300" style="font-size: 3rem;"></i>
            </div>
            <h4 class="text-slate-800 fw-bold">No services available right now.</h4>
            <p class="text-slate-500 max-w-2xl mx-auto">It looks like there are no active services listed by providers matching your criteria at this moment. Please check back later.</p>
        </div>
    @endforelse

</div>

{{-- Pagination --}}
@if($services->hasPages())
<div class="d-flex justify-content-center mt-5">
    {{ $services->links() }}
</div>
@endif

<style>
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;  
        overflow: hidden;
    }
    .hover-scale {
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
    }
    .hover-scale:hover {
        transform: scale(1.02);
        z-index: 10;
        box-shadow: 0 20px 40px rgba(0,0,0,0.08) !important;
    }
    .btn-modern-primary {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: none;
        transition: all 0.3s ease;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    .btn-modern-primary:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        color: white;
    }
</style>
@endsection
