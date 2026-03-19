@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 px-lg-5">
    
    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="font-weight-bold mb-1 text-dark" style="font-size: 1.75rem; letter-spacing: -0.02em;">Find Professionals</h2>
            <p class="text-muted small mb-0">Discover top-rated experts available for booking now.</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex align-items-center gap-3">
            <div class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill font-weight-bold">
                <i class="ph ph-seal-check mr-1"></i> VERIFIED PROVIDERS ONLY
            </div>
        </div>
    </div>

    {{-- Search Bar --}}
    <form action="{{ route('providers') }}" method="GET" class="mb-5">
        <div class="d-flex align-items-center bg-white" 
             style="max-width: 540px; height: 52px; border-radius: 50px; border: 1.5px solid #e2e8f0; padding: 0 6px 0 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); transition: all 0.2s ease;"
             onmouseover="this.style.borderColor='#cbd5e1'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.04)';"
             onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.02)';"
             onclick="this.querySelector('input').focus();">
             
            <i class="ph ph-magnifying-glass" style="font-size: 1.2rem; color: #64748b; margin-right: 14px;"></i>
            
            <input type="text" name="search" class="border-0 shadow-none bg-transparent p-0 w-100" 
                   style="color: #334155; font-size: 0.95rem; font-weight: 500; outline: none;" 
                   placeholder="Search by name, specialty, city or service..." 
                   value="{{ $search ?? '' }}"
                   onfocus="this.parentElement.style.borderColor='#1F7A63'; this.parentElement.style.boxShadow='0 0 0 3px rgba(31,122,99,0.1)';"
                   onblur="this.parentElement.style.borderColor='#e2e8f0'; this.parentElement.style.boxShadow='0 2px 10px rgba(0,0,0,0.02)';">
                   
            <button type="submit" class="btn btn-primary rounded-pill font-weight-bold flex-shrink-0"
                    style="height: 40px; padding: 0 24px; font-size: 0.85rem; background: linear-gradient(135deg, #1F7A63, #2DA884); border: none; box-shadow: 0 3px 10px rgba(31,122,99,0.2);">
                Search
            </button>
        </div>
        @if($search)
            <div class="mt-3 d-flex align-items-center gap-2">
                <span class="text-muted small">Showing results for <strong class="text-dark">"{{ $search }}"</strong> — {{ $providers->total() }} found</span>
                <a href="{{ route('providers') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="font-size: 0.78rem;">
                    <i class="ph ph-x mr-1"></i> Clear
                </a>
            </div>
        @else
            <p class="text-muted small mt-2 mb-0">{{ $providers->total() }} professional{{ $providers->total() !== 1 ? 's' : '' }} available</p>
        @endif
    </form>

    {{-- Providers List --}}
    @if($providers->count() > 0)
        <div class="row g-3">
            @foreach($providers as $provider)
                <div class="col-12 col-xl-6 animate-fade-in" style="animation-delay: {{ $loop->index * 0.04 }}s">
                    <div class="provider-row-card">

                        {{-- Left accent bar --}}
                        <div class="prc-accent"></div>

                        {{-- Avatar col (fixed width) --}}
                        <div class="prc-avatar-col">
                            <div class="prc-avatar-wrap">
                                <img src="{{ $provider->avatar_url }}" alt="{{ $provider->name }}" class="prc-avatar rounded-circle object-fit-cover">
                                @if($provider->average_rating >= 4.5)
                                    <div class="prc-verified"><i class="ph-fill ph-seal-check"></i></div>
                                @endif
                            </div>
                        </div>

                        {{-- Info col (flexible, clips overflow) --}}
                        <div class="prc-info-col">
                            <div class="prc-category">{{ Str::limit($provider->business_category ?? 'Professional', 20) }}</div>
                            <div class="prc-name text-truncate">{{ $provider->business_name ?? $provider->name . "'s Business" }}</div>
                            <div class="prc-meta">
                                <i class="ph ph-map-pin"></i> {{ $provider->city ?? 'Remote' }}
                                <span class="prc-dot">•</span>
                                <i class="ph-fill ph-star prc-star"></i>
                                {{ $provider->average_rating > 0 ? number_format($provider->average_rating, 1) : 'New' }}
                                <span class="prc-muted">({{ $provider->reviews_count }})</span>
                                <span class="prc-dot">•</span>
                                <i class="ph ph-briefcase"></i> {{ $provider->services->count() }} svc
                            </div>
                        </div>

                        {{-- CTA col (fixed width) --}}
                        <div class="prc-cta-col">
                            <a href="{{ route('providers.show', $provider->id) }}" class="prc-btn">
                                View <i class="ph ph-arrow-right"></i>
                            </a>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="row min-vh-50 pt-5 mt-2 d-flex justify-content-center animate-fade-in">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="text-center py-5 px-4 bg-white shadow-premium border-0 w-100 position-relative" style="border-radius: 32px;">
                    <i class="ph {{ $search ? 'ph-magnifying-glass' : 'ph-users-three' }} d-block mb-3" style="font-size: 3.5rem; color: #CBD5E1;"></i>
                    <h3 class="font-weight-bold text-dark mb-2">
                        {{ $search ? 'No Providers Found' : 'No Providers Available' }}
                    </h3>
                    <p class="text-muted mb-4 mx-auto" style="max-width: 360px; line-height: 1.6;">
                        {{ $search
                            ? 'No professionals matched "' . $search . '". Try a different keyword.'
                            : 'No verified professionals are available right now. Please check back later.' }}
                    </p>
                    @if($search)
                        <a href="{{ route('providers') }}" class="btn btn-primary rounded-pill px-5 py-2 font-weight-bold shadow-sm">
                            <i class="ph ph-arrow-counter-clockwise mr-1"></i> Show All Providers
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn btn-primary rounded-pill px-5 py-3 font-weight-bold shadow-sm">
                            <i class="ph ph-house mr-1"></i> Return to Dashboard
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Pagination --}}
    @if($providers->hasPages())
    <div class="d-flex justify-content-center mt-5">
        {{ $providers->appends(request()->query())->links() }}
    </div>
    @endif
</div>

@push('styles')
<style>
    /* ===== Provider Row Card (Fixed Grid Layout) ===== */
    .provider-row-card {
        display: grid;
        grid-template-columns: 4px 72px 1fr 120px;
        align-items: center;
        background: var(--bg-card, #fff);
        border-radius: 16px;
        border: 1.5px solid #F1F5F9;
        overflow: hidden;
        height: 80px;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    }
    .provider-row-card:hover {
        border-color: #1F7A63;
        box-shadow: 0 6px 24px rgba(31,122,99,0.12);
        transform: translateY(-2px);
    }

    /* Accent bar */
    .prc-accent {
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, #1F7A63, #2DA884);
    }

    /* Avatar column */
    .prc-avatar-col {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
    }
    .prc-avatar-wrap { position: relative; }
    .prc-avatar {
        width: 44px;
        height: 44px;
        border: 2px solid #E8F5F0;
        box-shadow: 0 2px 8px rgba(31,122,99,0.15);
    }
    .prc-verified {
        position: absolute;
        bottom: -2px; right: -2px;
        width: 16px; height: 16px;
        background: #fff;
        border-radius: 50%;
        border: 1.5px solid #1F7A63;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.6rem; color: #1F7A63;
    }

    /* Info column */
    .prc-info-col {
        padding: 0 14px;
        min-width: 0;
    }
    .prc-category {
        font-size: 0.6rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #1F7A63;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 2px;
    }
    .prc-name {
        font-size: 0.92rem;
        font-weight: 700;
        color: var(--text-dark, #1e293b);
        letter-spacing: -0.01em;
        margin-bottom: 3px;
    }
    .prc-meta {
        font-size: 0.7rem;
        color: #64748B;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .prc-meta i { font-size: 0.68rem; color: #94A3B8; }
    .prc-star { color: #F59E0B !important; }
    .prc-dot { margin: 0 4px; color: #CBD5E1; }
    .prc-muted { color: #94A3B8; font-weight: 500; }

    /* CTA column */
    .prc-cta-col {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        padding: 0 12px;
    }
    .prc-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        height: 36px;
        padding: 0 16px;
        font-size: 0.8rem;
        font-weight: 700;
        color: #fff;
        background: linear-gradient(135deg, #1F7A63, #2DA884);
        border-radius: 50px;
        border: none;
        white-space: nowrap;
        text-decoration: none;
        box-shadow: 0 3px 10px rgba(31,122,99,0.25);
        transition: all 0.2s ease;
    }
    .prc-btn:hover {
        color: #fff;
        background: linear-gradient(135deg, #175d4b, #1F7A63);
        box-shadow: 0 5px 16px rgba(31,122,99,0.35);
        transform: translateY(-1px);
        text-decoration: none;
    }
    .prc-btn i { font-size: 0.8rem; }

    /* Dark mode */
    body.dark-mode .provider-row-card { background: var(--bg-card); border-color: rgba(255,255,255,0.08); box-shadow: 0 4px 15px rgba(0,0,0,0.3); }
    body.dark-mode .provider-row-card:hover { border-color: rgba(45,168,132,0.5); box-shadow: 0 6px 20px rgba(45,168,132,0.15); }
    body.dark-mode .prc-avatar-col, body.dark-mode .prc-cta-col { border-color: transparent; }
    body.dark-mode .prc-avatar { border-color: rgba(255,255,255,0.1); }
    body.dark-mode .prc-name { color: #f8fafc; }
    body.dark-mode .prc-category { color: #34d399; }
    body.dark-mode .prc-meta { color: #94a3b8; }
    body.dark-mode .prc-meta i { color: #64748b; }
    body.dark-mode .prc-verified { background: var(--bg-card); border-color: #34d399; color: #34d399; }

    /* Misc */
    .shadow-premium { box-shadow: 0 15px 35px rgba(0,0,0,0.03), 0 1px 3px rgba(0,0,0,0.02) !important; }
    .animate-fade-in { animation: fadeIn 0.5s ease forwards; opacity: 0; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: none; } }
    .form-control:focus { background-color: var(--bg-card) !important; box-shadow: 0 0 0 3px rgba(31,122,99,0.15) !important; border-color: #1F7A63 !important; }

</style>
@endpush
@endsection