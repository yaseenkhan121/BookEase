@extends('layouts.app')

@section('content')
<div class="container-fluid p-0 bg-slate-50" style="min-height: calc(100vh - 70px); margin-top: -24px;">
    <div class="p-4 p-md-5">
        {{-- Header --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
            <div>
                <h2 class="display-6 fw-extrabold text-slate-900 mb-1">Search Results</h2>
                <p class="text-slate-500 font-medium">Found results for <span class="text-primary font-weight-bold">"{{ $query }}"</span></p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary shadow-sm bg-white px-4 rounded-pill">
                <i class="ph ph-arrow-left mr-2"></i> Back to Dashboard
            </a>
        </div>

        <div class="row g-4">
            {{-- Appointments Results --}}
            <div class="{{ $providers->count() > 0 ? 'col-lg-8' : 'col-lg-12' }}">
                <div class="card card-premium overflow-hidden mb-4">
                    <div class="card-header bg-white border-bottom border-slate-100 py-3 px-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary-light rounded-lg p-2 mr-3 text-primary">
                                <i class="ph ph-calendar-check" style="font-size: 1.25rem;"></i>
                            </div>
                            <h5 class="mb-0 fw-bold text-slate-800">Matching Appointments ({{ count($appointments) }})</h5>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="bg-slate-50 border-bottom border-slate-100">
                                    <th class="px-4 py-3 text-uppercase small font-weight-bold text-slate-500">Service & Party</th>
                                    <th class="px-4 py-3 text-uppercase small font-weight-bold text-slate-500 text-center">Schedule</th>
                                    <th class="px-4 py-3 text-uppercase small font-weight-bold text-slate-500 text-center">Status</th>
                                    <th class="px-4 py-3 text-uppercase small font-weight-bold text-slate-500 text-right">Details</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                @forelse ($appointments as $app)
                                <tr class="border-bottom border-slate-100 transition-all hover-row">
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-emerald-light rounded p-2 mr-3 text-emerald">
                                                <i class="ph ph-briefcase" style="font-size: 1.1rem;"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-slate-900">{{ $app->service->name ?? 'Deleted Service' }}</div>
                                                <div class="small text-slate-400">
                                                    @php 
                                                        $targetUser = auth()->user()->isProvider() ? $app->customer : $app->provider; 
                                                        $label = auth()->user()->isProvider() ? 'Customer: ' : 'Professional: ';
                                                    @endphp
                                                    {{ $label }} {{ $targetUser->name ?? ($targetUser->owner_name ?? 'N/A') }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 text-center">
                                        <div class="fw-bold text-slate-800">{{ \Carbon\Carbon::parse($app->start_time)->format('M d, Y') }}</div>
                                        <div class="small text-slate-400">{{ \Carbon\Carbon::parse($app->start_time)->format('g:i A') }}</div>
                                    </td>
                                    <td class="px-4 text-center">
                                        @php
                                            $statusStyles = [
                                                'pending'   => ['bg' => 'var(--primary-light)', 'text' => 'var(--primary-dark)', 'dot' => 'var(--primary)'],
                                                'approved'  => ['bg' => '#ECFDF5', 'text' => '#10B981', 'dot' => '#10B981'],
                                                'confirmed' => ['bg' => '#ECFDF5', 'text' => '#10B981', 'dot' => '#10B981'],
                                                'completed' => ['bg' => '#EFF6FF', 'text' => '#3B82F6', 'dot' => '#3B82F6'],
                                                'rejected'  => ['bg' => '#FEF2F2', 'text' => '#EF4444', 'dot' => '#EF4444'],
                                                'cancelled' => ['bg' => 'var(--slate-100)', 'text' => 'var(--slate-500)', 'dot' => 'var(--slate-400)'],
                                            ];
                                            $style = $statusStyles[$app->status] ?? ['bg' => 'var(--slate-50)', 'text' => 'var(--slate-600)', 'dot' => 'var(--slate-400)'];
                                        @endphp
                                        <span class="badge px-3 py-2 d-inline-flex align-items-center rounded-pill" 
                                              style="background-color: {{ $style['bg'] }}; color: {{ $style['text'] }}; font-weight: 700; font-size: 0.7rem;">
                                            <span class="mr-2" style="height: 6px; width: 6px; background: {{ $style['dot'] }}; border-radius: 50%;"></span>
                                            {{ strtoupper($app->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 text-right">
                                        <a href="{{ auth()->user()->role === 'admin' ? route('admin.bookings.index') : (auth()->user()->role === 'provider' ? route('provider.bookings.index') : route('bookings.index')) }}" class="btn btn-sm btn-light rounded-lg px-3 fw-bold text-slate-600">
                                            View
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="py-5 text-center bg-white border-0">
                                        <div class="py-5 text-slate-300">
                                            <i class="ph ph-magnifying-glass mb-3 d-block" style="font-size: 3.5rem; opacity: 0.3;"></i>
                                            <h5 class="text-slate-800 fw-bold mb-1">No matching appointments</h5>
                                            <p class="small">Try searching by status or service name.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Providers Results --}}
                @if($providers->count() > 0)
                <div class="card card-premium overflow-hidden border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom border-slate-100 py-3 px-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary-light rounded-lg p-2 mr-3 text-primary">
                                <i class="ph ph-users-three" style="font-size: 1.25rem;"></i>
                            </div>
                            <h5 class="mb-0 fw-bold text-slate-800">Matching Professionals ({{ $providers->count() }})</h5>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($providers as $provider)
                            <a href="{{ route('providers.show', $provider->id) }}" class="list-group-item list-group-item-action border-0 px-4 py-3 d-flex align-items-center gap-3 saas-search-item">
                                <img src="{{ $provider->avatar_url }}" class="rounded-circle" style="width: 48px; height: 48px; object-fit: cover; border: 2px solid var(--primary-light);">
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-slate-900 mb-0">{{ $provider->owner_name }}</div>
                                    <div class="small text-slate-500">{{ $provider->business_name ?? $provider->specialization }}</div>
                                </div>
                                <div class="text-right d-none d-md-block">
                                    <div class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fw-bold" style="font-size: 0.7rem;">
                                        {{ strtoupper($provider->business_category ?? 'Professional') }}
                                    </div>
                                </div>
                                <i class="ph ph-caret-right text-slate-300 ml-3"></i>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Services Results --}}
            <div class="{{ $providers->count() > 0 ? 'col-lg-4' : 'col-lg-4' }}">
                <div class="card card-premium overflow-hidden sticky-top" style="top: 100px;">
                    <div class="card-header bg-white border-bottom border-slate-100 py-3 px-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-indigo-light rounded-lg p-2 mr-3 text-indigo">
                                <i class="ph ph-sparkle" style="font-size: 1.25rem;"></i>
                            </div>
                            <h5 class="mb-0 fw-bold text-slate-800">Matching Services</h5>
                        </div>
                    </div>
                    <div class="card-body p-4 bg-white">
                        @forelse ($services as $service)
                            <div class="p-3 mb-3 rounded-xl border border-slate-100 hover-shadow transition-all bg-slate-50 cursor-pointer position-relative">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-slate-900">{{ $service->name }}</span>
                                    <span class="fw-bold text-emerald small font-weight-bold">PKR {{ number_format($service->price, 2) }}</span>
                                </div>
                                <p class="small text-slate-500 mb-0 line-clamp-2">{{ $service->description }}</p>
                                <div class="mt-2 text-right">
                                    <a href="{{ route('bookings.create', [$service->provider_id, $service->id]) }}" class="btn btn-xs btn-primary py-1 px-3 rounded-pill fw-bold" style="font-size: 0.75rem;">Book Now</a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-slate-300">
                                <i class="ph ph-selection-slash mb-2 d-block" style="font-size: 2.5rem; opacity: 0.3;"></i>
                                <p class="small text-slate-500">No services found.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-indigo-light { background: #EEF2FF; }
    .text-indigo { color: #4F46E5; }
    .bg-primary-light { background: var(--primary-light); }
    .bg-emerald-light { background: #ECFDF5; }
    .text-emerald { color: #10B981; }
    .rounded-xl { border-radius: 12px; }
    .hover-row:hover { background-color: #F8FAFB !important; transform: scale(1.002); }
    .hover-shadow:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.05); background: white !important; }
    .cursor-pointer { cursor: pointer; }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>
@endsection