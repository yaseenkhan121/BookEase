@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">

    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1">Dashboard Overview</h2>
                <p class="text-muted small mb-0">Welcome back, {{ auth()->user()->name }}</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if(auth()->user()->role === 'customer')
                    <a href="{{ route('providers') }}" class="btn btn-primary shadow-sm mr-2">
                        + New Appointment
                    </a>
                @endif
                <span class="badge badge-modern p-2 border shadow-sm" style="background: var(--bg-card); color: var(--text-dark); border-radius: 12px; border-color: var(--border-color) !important;">
                    <i class="ph ph-calendar-blank mr-1"></i> {{ now()->format('l, jS F Y') }}
                </span>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        @php
            $statCards = [
                ['label' => 'Total Appointments', 'value' => $stats['total_appointments'] ?? 0, 'icon' => 'ph-calendar-check', 'color' => 'var(--slate-800)'],
                ['label' => 'Pending Requests', 'value' => $stats['pending_requests'] ?? 0, 'icon' => 'ph-clock-clockwise', 'color' => '#F59E0B'],
                ['label' => 'Completed Projects', 'value' => $stats['completed_projects'] ?? 0, 'icon' => 'ph-check-circle', 'color' => '#10B981'],
                ['label' => 'Running Status', 'value' => $stats['running_appointments'] ?? 0, 'icon' => 'ph-activity', 'color' => '#3B82F6'],
            ];

            if (auth()->user()->isAdmin()) {
                $statCards[] = ['label' => 'Pending Approvals', 'value' => $stats['pending_providers'] ?? 0, 'icon' => 'ph-shield-check', 'color' => '#10B981'];
            }

            if (auth()->user()->isProvider() && !auth()->user()->isAdmin()) {
                $statCards[] = ['label' => 'Average Rating', 'value' => number_format($stats['average_rating'] ?? 0, 1) . ' ⭐', 'icon' => 'ph-star', 'color' => '#F59E0B'];
            }
        @endphp

        @foreach($statCards as $card)
        <div class="col-md-3 mb-4">
            <div class="card card-premium p-4 h-100 border shadow-sm" style="border-radius: 24px; background: var(--bg-card); border: 1px solid var(--border-color) !important; box-shadow: var(--shadow-premium) !important; position: relative; overflow: hidden;">
                {{-- Decorative Glow for Dark Mode --}}
                <div class="d-dark-only" style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: {{ $card['color'] }}08; filter: blur(40px); border-radius: 50%;"></div>
                
                <div class="d-flex align-items-center mb-3" style="position: relative; z-index: 1;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" 
                         style="width: 48px; height: 48px; background: {{ $card['color'] }}20; color: {{ $card['color'] }}; box-shadow: 0 0 15px {{ $card['color'] }}08; border: 1px solid {{ $card['color'] }}15;">
                        <i class="ph {{ $card['icon'] }} weight-bold" style="font-size: 1.5rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-uppercase font-weight-bold text-muted mb-0" style="font-size: 0.75rem; letter-spacing: 0.06em; line-height: 1.5;">
                            {{ $card['label'] }}
                        </p>
                    </div>
                </div>
                <h3 class="mb-0 font-weight-bold" style="font-size: 1.75rem; letter-spacing: -0.03em; color: var(--text-dark); position: relative; z-index: 1;">
                    {{ $card['value'] }}
                </h3>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card card-premium p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="font-weight-bold mb-0" style="color: var(--text-dark); font-size: 1.35rem; letter-spacing: -0.02em;">Booking Analytics</h4>
                    <select class="form-control form-control-sm w-auto px-4" style="border-radius: 12px; background: var(--bg-body); color: var(--text-dark); border: 1px solid var(--border-color); font-size: 0.95rem; height: 40px; cursor: pointer; box-shadow: none !important;">
                        <option>Last 7 Days</option>
                        <option>Last 30 Days</option>
                    </select>
                </div>
                <div style="height: 320px; width: 100%;">
                    <canvas id="bookingAnalytics"></canvas>
                </div>
            </div>

            @if(auth()->user()->isProvider() && count($recentReviews) > 0)
                <div class="card card-premium p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="font-weight-bold mb-0">Recent Feedback</h5>
                        <a href="{{ route('providers.show', auth()->user()->providerProfile->id) }}#reviews" class="text-primary small font-weight-bold">View Profile Reviews</a>
                    </div>
                    <div class="review-list">
                        @foreach($recentReviews as $review)
                            <div class="d-flex align-items-start mb-4 p-3 rounded-4 bg-light border-0" style="border-radius: 20px;">
                                <div class="avatar-sm rounded-circle mr-3 p-1 bg-white shadow-sm d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; min-width: 44px;">
                                    <span class="text-primary font-weight-bold small">{{ substr($review->customer->name, 0, 1) }}</span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0 font-weight-bold text-dark">{{ $review->customer->name }}</h6>
                                        <div class="text-warning small">
                                            @for($i=0; $i<5; $i++)
                                                <i class="ph{{ $i < $review->rating ? '-fill' : '' }} ph-star"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem; line-height: 1.5;">{{ $review->review_text }}</p>
                                    <div class="text-right mt-2">
                                        <small class="text-muted font-weight-medium" style="font-size: 0.7rem; opacity: 0.6;">{{ $review->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card card-premium p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="font-weight-bold mb-0" style="color: var(--text-dark); font-size: 1.35rem; letter-spacing: -0.02em; line-height: 1.2;">Upcoming<br>Schedule</h4>
                    <span class="badge px-3 py-1 font-weight-bold" style="border-radius: 8px; background: var(--bg-body); color: var(--text-label); font-size: 0.75rem;">{{ count($upcoming) }} Total</span>
                </div>
                <div class="booking-list mt-2">
                    @forelse ($upcoming as $booking)
                    <div class="d-flex align-items-center mb-4 p-2 rounded-3" style="transition: all 0.3s ease; border: 1px solid transparent;" 
                         onmouseover="this.style.background='var(--bg-body)'; this.style.borderColor='var(--border-color)';" 
                         onmouseout="this.style.background='transparent'; this.style.borderColor='transparent';">
                        <div class="user-avatar-wrapper shadow-none flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px; min-width: 50px; background: var(--bg-body); border: 1px solid var(--border-color) !important; aspect-ratio: 1/1; overflow: hidden; margin-right: 16px;">
                            @if($booking->customer && $booking->customer->profile_image)
                                <img src="{{ $booking->customer->avatar_url }}" alt="" class="rounded-circle object-fit-cover w-100 h-100" style="aspect-ratio: 1/1;">
                            @else
                                <span class="font-weight-bold" style="font-size: 1.1rem; color: var(--text-dark);">
                                    {{ substr($booking->customer->name ?? 'U', 0, 1) }}
                                </span>
                            @endif
                        </div>
 stone
                        <div class="flex-grow-1 overflow-hidden">
                            <h6 class="mb-0 font-weight-bold text-truncate" style="font-size: 1.05rem; color: var(--text-dark); letter-spacing: -0.01em;">
                                {{ Str::limit($booking->customer->name ?? ($booking->providerProfile->owner_name ?? 'Guest'), 8) }}
                            </h6>
                            <small class="text-truncate d-block mt-1" style="font-size: 0.85rem; color: #64748b;">{{ $booking->service->name ?? 'Service' }}</small>
                        </div>
                        <div class="text-right pl-2">
                            <p class="mb-0 font-weight-bold pb-1" style="font-size: 1.05rem; color: var(--text-dark); letter-spacing: -0.01em;">
                                {{ $booking->start_time ? \Carbon\Carbon::parse($booking->start_time)->format('H:i') : '--:--' }}
                            </p>
                            <small style="font-size: 0.8rem; color: #64748b;">
                                {{ $booking->start_time ? \Carbon\Carbon::parse($booking->start_time)->format('M d') : '' }}
                            </small>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px; background: var(--bg-body);">
                            <i class="ph ph-calendar-x text-muted" style="font-size: 1.5rem;"></i>
                        </div>
                        <p class="text-muted small">No upcoming appointments.</p>
                    </div>
                    @endforelse
                </div>
                <a href="{{ auth()->user()->role === 'admin' ? route('admin.bookings.index') : (auth()->user()->role === 'provider' ? route('provider.bookings.index') : route('bookings.index')) }}" 
                   class="btn btn-block font-weight-bold py-3 mt-2" 
                   style="border-radius: 12px; background: var(--bg-body); border: 1px solid var(--border-color); color: var(--text-dark); font-size: 0.95rem; transition: all 0.2s ease;"
                   onmouseover="this.style.borderColor='var(--text-muted)';"
                   onmouseout="this.style.borderColor='var(--border-color)';">
                    View All Schedule
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('bookingAnalytics');
        if (!ctx) return;

        // PREVENT PARSE ERROR: Extract data via clean PHP block
        @php
            $labels = $chartData['labels'] ?? ['Fri', 'Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu'];
            $values = $chartData['values'] ?? [0, 0, 0, 1, 0, 0, 1];
        @endphp

        const chartLabels = @json($labels);
        const chartValues = @json($values);

        const isDarkMode = document.body.classList.contains('dark-mode');
        // Very subtle dark grid
        const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.04)' : '#F1F5F9';
        // Very muted dark gray text for axes like the image
        const textColor = isDarkMode ? '#52525b' : '#94A3B8'; 
        const tooltipBg = isDarkMode ? '#0a0a0a' : '#1E293B';
        
        // Advanced decorative gradient
        const chartCtx = ctx.getContext('2d');
        const gradientFill = chartCtx.createLinearGradient(0, 0, 0, 320);
        if (isDarkMode) {
            gradientFill.addColorStop(0, 'rgba(16, 185, 129, 0.5)'); // Stronger green glow
            gradientFill.addColorStop(0.5, 'rgba(16, 185, 129, 0.15)');
            gradientFill.addColorStop(1, 'rgba(16, 185, 129, 0.0)'); // Transparent at bottom
        } else {
            gradientFill.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
            gradientFill.addColorStop(1, 'rgba(16, 185, 129, 0.0)');
        }

        new Chart(chartCtx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Bookings',
                    data: chartValues,
                    borderColor: '#10B981', // Emerald green
                    borderWidth: 4, // Thicker line
                    backgroundColor: gradientFill,
                    fill: true,
                    tension: 0.45, // Smoother bezier curve
                    pointRadius: 0, // No points visible normally (sleeker look)
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#10B981',
                    pointHoverBorderColor: isDarkMode ? '#0a0a0a' : '#fff',
                    pointHoverBorderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: { left: -10, bottom: -5 }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: tooltipBg,
                        titleFont: { family: 'Inter', size: 13, weight: 'bold' },
                        bodyFont: { family: 'Inter', size: 12 },
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: false,
                        titleColor: '#fff',
                        bodyColor: '#ecfdf5',
                        borderColor: isDarkMode ? 'rgba(255,255,255,0.1)' : 'transparent',
                        borderWidth: 1
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { 
                            color: gridColor, 
                            drawBorder: false,
                            lineWidth: 1
                        },
                        ticks: { 
                            font: { family: 'Inter', size: 11, weight: '500' }, 
                            color: textColor, 
                            stepSize: 1,
                            padding: 15
                        },
                        border: { display: false }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { 
                            font: { family: 'Inter', size: 11, weight: '500' }, 
                            color: textColor,
                            padding: 10
                        },
                        border: { display: false }
                    }
                }
            }
        });
    });
</script>
@endpush