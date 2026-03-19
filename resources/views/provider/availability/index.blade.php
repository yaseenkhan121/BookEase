@extends('layouts.app')

@section('content')
<div class="container-fluid p-0" style="min-height: calc(100vh - 70px); margin-top: -24px; background-color: var(--bg-body);">
    <div class="p-4 p-md-5">
        
        {{-- Header Section --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5">
            <div>
                <h2 class="fw-bold mb-1">Working Hours</h2>
                <p class="text-muted font-medium">Manage your availability for customer bookings</p>
            </div>
        </div>

        {{-- Professional Alert --}}
        @if(session('success'))
            <div class="alert alert-modern-success border-0 mb-4 animate-fade-in shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="ph ph-check-circle mr-2" style="font-size: 1.25rem;"></i> 
                    <span class="fw-bold">{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 mb-4 animate-fade-in shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="ph ph-warning-circle mr-2" style="font-size: 1.25rem;"></i> 
                    <span class="fw-bold">{{ session('error') }}</span>
                </div>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger border-0 mb-4 animate-fade-in shadow-sm">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <div class="row">
            {{-- Add Availability Form --}}
            <div class="col-lg-4 mb-4">
                <div class="card card-premium border-0 shadow-sm" style="border-radius: 20px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Add Working Hours</h5>
                        <form action="{{ route('provider.availability.store') }}" method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="form-label font-bold text-muted small text-uppercase">Day of Week</label>
                                <select name="day_of_week" class="form-select form-control-lg bg-light border-0" required>
                                    <option value="1">Monday</option>
                                    <option value="2">Tuesday</option>
                                    <option value="3">Wednesday</option>
                                    <option value="4">Thursday</option>
                                    <option value="5">Friday</option>
                                    <option value="6">Saturday</option>
                                    <option value="0">Sunday</option>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label font-bold text-muted small text-uppercase">Start Time</label>
                                    <input type="time" name="start_time" class="form-control form-control-lg bg-light border-0" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label font-bold text-muted small text-uppercase">End Time</label>
                                    <input type="time" name="end_time" class="form-control form-control-lg bg-light border-0" required>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold">
                                <i class="ph ph-plus-circle mr-1"></i> Add Schedule
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Availability List --}}
            <div class="col-lg-8">
                <div class="card card-premium border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 border-0">
                            <thead>
                                <tr class="border-bottom">
                                    <th class="px-4 py-3 text-uppercase tiny-font font-bold text-muted">Day</th>
                                    <th class="px-4 py-3 text-uppercase tiny-font font-bold text-muted text-center">Start Time</th>
                                    <th class="px-4 py-3 text-uppercase tiny-font font-bold text-muted text-center">End Time</th>
                                    <th class="px-4 py-3 text-uppercase tiny-font font-bold text-muted text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $days = [
                                        0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 
                                        3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'
                                    ];
                                @endphp
                                @forelse ($availabilities as $slot)
                                    <tr class="border-bottom border-light">
                                        <td class="px-4 py-4">
                                            <div class="d-inline-flex align-items-center px-3 py-1 rounded-pill border fw-bold" style="background: var(--bg-body);">
                                                <i class="ph ph-calendar-blank mr-2 text-primary"></i>
                                                {{ $days[$slot->day_of_week] }}
                                            </div>
                                        </td>
                                        <td class="px-4 text-center fw-medium font-mono text-slate-700">
                                            {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                                        </td>
                                        <td class="px-4 text-center fw-medium font-mono text-slate-700">
                                            {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                        </td>
                                        <td class="px-4 text-right">
                                            <form action="{{ route('provider.availability.destroy', $slot) }}" method="POST" onsubmit="return confirm('Remove this time slot?');" class="m-0 d-inline-block">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light border text-danger rounded-pill px-3 fw-bold">
                                                    <i class="ph ph-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-5 text-center border-0">
                                            <div class="py-5">
                                                <div class="mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background: var(--bg-body);">
                                                    <i class="ph ph-clock text-muted" style="font-size: 2.5rem;"></i>
                                                </div>
                                                <h5 class="fw-bold">No availability set</h5>
                                                <p class="text-muted small mb-0">Use the form to add your working hours.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
