@extends('layouts.app')

@section('content')
<div class="container-fluid position-relative overflow-hidden py-1">
    
    {{-- Decorative Background Elements --}}
    <div class="decorative-blob blob-1"></div>
    <div class="decorative-blob blob-2"></div>
    <div class="decorative-blob blob-3"></div>

    {{-- Header with Search Bar --}}
    <div class="mb-4 position-relative z-index-1 animate-fade-in">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div>
                <div class="d-flex align-items-center mb-2">
                    <div class="badge status-pill status-active mr-3">
                        <span class="pulse-dot"></span> Live Analytics
                    </div>
                    <span class="text-muted small font-weight-bold uppercase-letter-spacing">Real-Time Data</span>
                </div>
                <h1 class="display-5 font-weight-extrabold text-dark tracking-tight mb-1">Platform <span class="text-gradient">Insights</span></h1>
                <p class="text-muted mb-0">Advanced visualization of your {{ $role }} performance metrics.</p>
            </div>
        </div>

        {{-- Search Bar --}}
        <div class="search-bar-analytics mt-3">
            <div class="search-container d-flex align-items-center px-3" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 14px; height: 48px;">
                <i class="ph ph-magnifying-glass text-muted mr-2" style="font-size: 1.2rem;"></i>
                <input type="text" id="analyticsSearch" class="search-input border-0 bg-transparent flex-grow-1" placeholder="Search charts, metrics..." style="outline: none; font-size: 0.95rem; color: var(--text-dark);">
            </div>
        </div>
    </div>

    @if(isset($no_provider_profile))
        <div class="glass-card premium-shadow p-5 text-center animate-slide-up">
            <div class="icon-circle-lg bg-warning-soft mb-4">
                <i class="ph ph-warning-circle text-warning"></i>
            </div>
            <h3 class="font-weight-bold">Profile Setup Required</h3>
            <p class="text-muted mb-4">Complete your provider profile to unlock role-based analytics and growth tracking.</p>
            <a href="{{ route('provider.profile.edit') }}" class="btn btn-primary" style="border-radius: 14px; padding: 12px 28px; background: var(--primary); border: none;">
                Finalize Profile <i class="ph ph-arrow-right ml-2"></i>
            </a>
        </div>
    @else
        <div class="row position-relative z-index-1">
            
            {{-- Main Booking Trend Chart --}}
            <div class="{{ $role == 'customer' ? 'col-12' : 'col-lg-8' }} mb-4">
                <div class="glass-card h-100 animate-slide-up">
                    <div class="card-header-v2">
                        <div>
                            <h5 class="font-weight-bold mb-1">
                                {{ $role == 'admin' ? 'Global Booking Activity' : ($role == 'provider' ? 'My Booking Trends' : 'My Booking Activity') }}
                            </h5>
                            <p class="text-muted small mb-0">Daily booking volume — last 7 days</p>
                        </div>
                        <div class="chart-legend-v2">
                            <span class="legend-item"><span class="dot primary"></span> Bookings</span>
                        </div>
                    </div>
                    <div class="chart-container-v2">
                        <canvas id="bookingsChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Secondary Chart (Admin: Provider Growth / Provider: Service Popularity) --}}
            @if($role != 'customer')
            <div class="col-lg-4 mb-4">
                <div class="glass-card h-100 animate-slide-up" style="animation-delay: 0.1s;">
                    <div class="card-header-v2">
                        <div>
                            <h5 class="font-weight-bold mb-1">
                                {{ $role == 'admin' ? 'Provider Growth' : 'Service Popularity' }}
                            </h5>
                            <p class="text-muted small mb-0">Distribution of volume</p>
                        </div>
                    </div>
                    <div class="chart-container-v2 px-2">
                        <canvas id="secondaryChart"></canvas>
                    </div>
                </div>
            </div>
            @endif

            {{-- Revenue / Earnings Chart --}}
            @if($role == 'admin' || $role == 'provider')
            <div class="col-12 mb-4">
                <div class="glass-card animate-slide-up" style="animation-delay: 0.2s;">
                    <div class="card-header-v2">
                        <div>
                            <h5 class="font-weight-bold mb-1">
                                {{ $role == 'admin' ? 'Revenue Trend' : 'Earnings Trend' }}
                            </h5>
                            <p class="text-muted small mb-0">Completed bookings revenue — last 7 days</p>
                        </div>
                        <div class="chart-legend-v2">
                            <span class="legend-item"><span class="dot revenue"></span> Revenue (PKR)</span>
                        </div>
                    </div>
                    <div class="chart-container-v2" style="min-height: 280px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
            @endif

            {{-- Customer Info Cards --}}
            @if($role == 'customer')
            <div class="col-12 mb-4">
                <div class="row">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="glass-card p-4 h-100 animate-slide-up" style="animation-delay: 0.2s;">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-circle bg-primary-soft mr-3">
                                    <i class="ph ph-shooting-star text-primary"></i>
                                </div>
                                <h5 class="font-weight-bold mb-0">Smart Insights</h5>
                            </div>
                            <p class="text-muted small mb-0">Our system analyzes your booking frequency to suggest optimal times for your future appointments.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="glass-card p-4 h-100 animate-slide-up" style="animation-delay: 0.3s;">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-circle bg-success-soft mr-3">
                                    <i class="ph ph-shield-check text-success"></i>
                                </div>
                                <h5 class="font-weight-bold mb-0">Data Integrity</h5>
                            </div>
                            <p class="text-muted small mb-0">Your analytics are derived directly from secured transaction logs, ensuring 100% accuracy.</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    @endif
</div>

@push('styles')
<style>
    /* ===== DECORATIVE BLOBS ===== */
    .decorative-blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        z-index: 0;
        opacity: 0.15;
        transition: all 1s ease;
    }
    body.dark-mode .decorative-blob { opacity: 0.08; }
    .blob-1 { width: 300px; height: 300px; background: var(--primary); top: -50px; right: -50px; }
    .blob-2 { width: 250px; height: 250px; background: #2DAA84; bottom: 100px; left: -50px; }
    .blob-3 { width: 200px; height: 200px; background: #1E7C62; top: 200px; left: 30%; }

    /* ===== GLASSMORPHISM CARDS ===== */
    .glass-card {
        background: rgba(255, 255, 255, 0.75);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.04);
        display: flex;
        flex-direction: column;
        transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
        overflow: hidden;
    }
    body.dark-mode .glass-card {
        background: rgba(15, 15, 15, 0.6);
        border-color: rgba(255, 255, 255, 0.05);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    .glass-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 48px rgba(0, 0, 0, 0.08);
    }

    /* ===== LAYOUT UTILS ===== */
    .z-index-1 { z-index: 1; }
    .display-5 { font-size: 2.2rem; font-weight: 800; }
    .text-gradient {
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .uppercase-letter-spacing { text-transform: uppercase; letter-spacing: 0.12em; font-size: 0.65rem; }

    .card-header-v2 { padding: 24px 24px 8px; display: flex; justify-content: space-between; align-items: flex-start; }
    .chart-container-v2 { padding: 0 16px 16px; flex-grow: 1; min-height: 300px; }

    .icon-circle { width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }
    .icon-circle-lg { width: 72px; height: 72px; display: flex; align-items: center; justify-content: center; border-radius: 18px; margin: 0 auto; font-size: 2rem; }
    .bg-primary-soft { background: rgba(30, 124, 98, 0.1); }
    .bg-success-soft { background: rgba(16, 185, 129, 0.1); }
    .bg-warning-soft { background: rgba(245, 158, 11, 0.1); }

    .legend-item { display: flex; align-items: center; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; }
    .dot { width: 8px; height: 8px; border-radius: 50%; margin-right: 6px; }
    .dot.primary { background: var(--primary); }
    .dot.revenue { background: #f59e0b; }

    /* ===== PULSE DOT ===== */
    .pulse-dot {
        width: 8px; height: 8px; background: #10B981; border-radius: 50%; display: inline-block; margin-right: 4px;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    /* ===== ANIMATIONS ===== */
    .animate-fade-in { animation: fadeIn 0.6s ease-out; }
    .animate-slide-up { animation: slideUp 0.6s cubic-bezier(0.23, 1, 0.32, 1) both; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    /* ===== SEARCH BAR ===== */
    .search-bar-analytics .search-input::placeholder { color: var(--text-muted); }
    body.dark-mode .search-bar-analytics .search-container { background: var(--bg-card) !important; border-color: var(--border-color) !important; }
    body.dark-mode .search-bar-analytics .search-input { color: #f1f5f9 !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDarkMode = document.body.classList.contains('dark-mode');
    const textColor = isDarkMode ? '#94a3b8' : '#64748b';
    const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';
    const primaryColor = '#1E7C62';
    const secondaryColor = '#2DAA84';

    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size = 12;

    const tooltipStyle = {
        backgroundColor: isDarkMode ? '#1a1a1a' : '#fff',
        titleColor: isDarkMode ? '#fff' : '#1a1a1a',
        bodyColor: isDarkMode ? '#fff' : '#1a1a1a',
        padding: 14,
        cornerRadius: 10,
        displayColors: false,
        borderWidth: 1,
        borderColor: gridColor,
    };

    // --- PRIMARY LINE CHART (Bookings) ---
    const bookingsCtx = document.getElementById('bookingsChart');
    if (bookingsCtx) {
        new Chart(bookingsCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartData['labels']) !!},
                datasets: [{
                    label: 'Bookings',
                    data: {!! json_encode($chartData['values']) !!},
                    borderColor: primaryColor,
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: primaryColor,
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: function(context) {
                        const chart = context.chart;
                        const {ctx, chartArea} = chart;
                        if (!chartArea) return null;
                        const grad = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                        grad.addColorStop(0, isDarkMode ? 'rgba(30, 124, 98, 0.25)' : 'rgba(30, 124, 98, 0.12)');
                        grad.addColorStop(1, 'rgba(30, 124, 98, 0)');
                        return grad;
                    }
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { ...tooltipStyle, callbacks: { label: (c) => ` ${c.parsed.y} Bookings` } } },
                scales: {
                    y: { grid: { color: gridColor, drawBorder: false }, ticks: { color: textColor, padding: 10, stepSize: 1 } },
                    x: { grid: { display: false }, ticks: { color: textColor, padding: 10 } }
                }
            }
        });
    }

    // --- SECONDARY BAR CHART (Provider Growth / Service Popularity) ---
    const secondaryCtx = document.getElementById('secondaryChart');
    if (secondaryCtx) {
        const role = '{{ $role }}';
        let labels, values;
        @if($role == 'admin')
            labels = {!! json_encode($providerGrowthChart['labels']) !!};
            values = {!! json_encode($providerGrowthChart['values']) !!};
        @elseif($role == 'provider')
            labels = {!! json_encode($serviceChart['labels'] ?? []) !!};
            values = {!! json_encode($serviceChart['values'] ?? []) !!};
        @else
            labels = [];
            values = [];
        @endif

        new Chart(secondaryCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: function(context) {
                        const chart = context.chart;
                        const {ctx, chartArea} = chart;
                        if (!chartArea) return primaryColor;
                        const grad = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                        grad.addColorStop(0, primaryColor);
                        grad.addColorStop(1, secondaryColor);
                        return grad;
                    },
                    borderRadius: 6,
                    barThickness: role === 'provider' ? 20 : 24,
                }]
            },
            options: {
                indexAxis: role === 'provider' ? 'y' : 'x',
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: tooltipStyle },
                scales: {
                    y: { grid: { color: gridColor, drawBorder: false }, ticks: { color: textColor, padding: 8 } },
                    x: { grid: { display: role === 'provider', color: gridColor }, ticks: { color: textColor, padding: 8, stepSize: 1 } }
                }
            }
        });
    }

    // --- REVENUE / EARNINGS LINE CHART ---
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        @if($role == 'admin')
            const revLabels = {!! json_encode($revenueChart['labels']) !!};
            const revValues = {!! json_encode($revenueChart['values']) !!};
        @elseif($role == 'provider')
            const revLabels = {!! json_encode($earningsChart['labels'] ?? []) !!};
            const revValues = {!! json_encode($earningsChart['values'] ?? []) !!};
        @else
            const revLabels = [];
            const revValues = [];
        @endif

        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: revLabels,
                datasets: [{
                    label: '{{ $role == "admin" ? "Revenue" : "Earnings" }}',
                    data: revValues,
                    borderColor: '#f59e0b',
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#f59e0b',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: function(context) {
                        const chart = context.chart;
                        const {ctx, chartArea} = chart;
                        if (!chartArea) return null;
                        const grad = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                        grad.addColorStop(0, isDarkMode ? 'rgba(245, 158, 11, 0.2)' : 'rgba(245, 158, 11, 0.1)');
                        grad.addColorStop(1, 'rgba(245, 158, 11, 0)');
                        return grad;
                    }
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { ...tooltipStyle, callbacks: { label: (c) => ` PKR ${c.parsed.y.toLocaleString()}` } } },
                scales: {
                    y: { grid: { color: gridColor, drawBorder: false }, ticks: { color: textColor, padding: 10, callback: (v) => 'PKR ' + v.toLocaleString() } },
                    x: { grid: { display: false }, ticks: { color: textColor, padding: 10 } }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
