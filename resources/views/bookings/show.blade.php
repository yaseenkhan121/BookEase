@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            {{-- Header with Status & Navigation --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-2" style="background: transparent; padding: 0;">
                            <li class="breadcrumb-item"><a href="{{ route('bookings.index') }}" class="text-decoration-none text-muted">My Bookings</a></li>
                            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Booking Details</li>
                        </ol>
                    </nav>
                    <h2 class="display-6 fw-extrabold text-dark mb-0">Record #{{ $appointment->id }}</h2>
                </div>
                <div class="d-flex align-items-center gap-3">
                    @php
                        $statusStyles = [
                            'pending'   => ['bg' => 'rgba(245, 158, 11, 0.1)', 'text' => '#F59E0B'],
                            'confirmed' => ['bg' => 'rgba(16, 185, 129, 0.1)', 'text' => '#10B981'],
                            'completed' => ['bg' => 'rgba(59, 130, 246, 0.1)', 'text' => '#3B82F6'],
                            'rejected'  => ['bg' => 'rgba(239, 68, 68, 0.1)', 'text' => '#EF4444'],
                            'cancelled' => ['bg' => 'rgba(148, 163, 184, 0.1)', 'text' => '#94A3B8'],
                        ];
                        $style = $statusStyles[$appointment->status] ?? ['bg' => 'rgba(148, 163, 184, 0.1)', 'text' => '#94A3B8'];
                    @endphp
                    <span class="status-pill px-4 py-2" style="background: {{ $style['bg'] }}; color: {{ $style['text'] }}; border: 1px solid transparent; border-radius: 50px; font-weight: 700; font-size: 0.9rem;">
                        {{ in_array($appointment->status, ['confirmed', 'approved']) ? 'Confirmed' : ucfirst($appointment->status) }}
                    </span>
                    @if($appointment->status === 'pending' && auth()->user()->isCustomer())
                        <form action="{{ route('bookings.destroy', $appointment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger px-4 rounded-pill fw-bold">Cancel Booking</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="row g-4">
                {{-- Left: Booking Summary --}}
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
                        <div class="card-body p-4 p-md-5">
                            <h5 class="fw-bold text-dark mb-4 d-flex align-items-center">
                                <i class="ph ph-calendar-blank me-2 text-primary"></i> Appointment Information
                            </h5>
                            
                            <div class="row g-4">
                                <div class="col-sm-6">
                                    <label class="text-muted small text-uppercase font-weight-bold tracking-widest d-block mb-1">Date</label>
                                    <p class="fw-bold text-dark fs-5 mb-0">{{ $appointment->start_time->format('M d, Y') }}</p>
                                    <p class="text-muted mb-0 small">{{ $appointment->start_time->format('l') }}</p>
                                </div>
                                <div class="col-sm-6">
                                    <label class="text-muted small text-uppercase font-weight-bold tracking-widest d-block mb-1">Time</label>
                                    <p class="fw-bold text-dark fs-5 mb-0">{{ $appointment->start_time->format('h:i A') }}</p>
                                    <p class="text-muted mb-0 small">{{ $appointment->end_time->format('h:i A') }} ({{ $appointment->service->duration_minutes ?? 0 }} mins)</p>
                                </div>
                                <div class="col-12 pr-md-5">
                                    <div class="p-3 rounded-3 bg-light border-0">
                                        <label class="text-muted small text-uppercase font-weight-bold tracking-widest d-block mb-1">Service Notes</label>
                                        <p class="text-dark mb-0 italic" style="font-size: 0.95rem;">
                                            {{ $appointment->notes ?: 'No additional notes provided for this appointment.' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Customer Info (Visible to Provider/Admin) --}}
                    @if(!auth()->user()->isCustomer())
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                        <div class="card-body p-4 p-md-5">
                            <h5 class="fw-bold text-dark mb-4 d-flex align-items-center">
                                <i class="ph ph-user me-2 text-primary"></i> Customer Details
                            </h5>
                            <div class="d-flex align-items-center">
                                <img src="{{ $appointment->customer->avatar_url }}" class="rounded-circle me-3 border" style="width: 54px; height: 54px; object-fit: cover;">
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">{{ $appointment->customer_name ?: $appointment->customer->name }}</h6>
                                    <p class="text-muted small mb-0"><i class="ph ph-phone me-1"></i> {{ $appointment->customer_phone ?: $appointment->customer->phone_number }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Right: Provider & Service Sidebar --}}
                <div class="col-lg-5">
                    {{-- Provider Card --}}
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 text-center p-4 bg-white">
                        <label class="text-muted small text-uppercase font-weight-bold tracking-widest d-block mb-3">Service Provider</label>
                        <div class="mx-auto rounded-circle overflow-hidden shadow-sm border border-4 border-white mb-3" style="width: 100px; height: 100px;">
                            <img src="{{ $appointment->provider->avatar_url }}" class="w-100 h-100 object-fit-cover">
                        </div>
                        <h5 class="fw-bold text-dark mb-1">{{ $appointment->provider->business_name ?: $appointment->provider->name }}</h5>
                        <p class="text-primary small fw-bold mb-3">{{ $appointment->provider->specialization ?: 'Professional Partner' }}</p>
                        <a href="{{ route('providers.show', $appointment->provider_id) }}" class="btn btn-light rounded-pill px-4 btn-sm fw-bold">View Profile</a>
                    </div>

                    {{-- Service Summary (Simplified) --}}
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-dark mb-3">Service Summary</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">{{ $appointment->service->name }}</span>
                                <span class="fw-bold text-dark">PKR {{ number_format($appointment->price, 0) }}</span>
                            </div>
                            <hr class="my-3 opacity-50">
                            <div class="alert alert-info border-0 shadow-none mb-0 p-3" style="background: rgba(59, 130, 246, 0.05); color: #1E40AF; border-radius: 12px;">
                                <div class="d-flex">
                                    <i class="ph ph-info-circle me-2 mt-1 fs-5"></i>
                                    <div>
                                        <p class="fw-bold mb-1" style="font-size: 0.85rem;">Physical Payment Only</p>
                                        <p class="mb-0 small opacity-75">Payment will be made directly to the provider at the time of service.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-emerald-light { background-color: rgba(16, 185, 129, 0.05); }
    .text-emerald { color: #10B981; }
    .btn-emerald { background: #10B981; color: white; border: none; }
    .btn-emerald:hover { background: #059669; color: white; }
    .tracking-widest { letter-spacing: 0.1em; }
    .italic { font-style: italic; }
</style>
@endsection
