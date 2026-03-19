@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 font-weight-bold text-dark mb-1">Booking Calendar</h2>
            <p class="text-muted small mb-0">View and manage your schedule across the month</p>
        </div>
        <div class="d-flex align-items-center">
            <a href="{{ auth()->user()->role === 'admin' ? route('admin.bookings.index') : (auth()->user()->role === 'provider' ? route('provider.bookings.index') : route('bookings.index')) }}" class="btn btn-outline-primary shadow-sm font-weight-bold" style="border-radius: 8px;">
                <i class="ph ph-list-bullets mr-1"></i> View List Mode
            </a>
        </div>
    </div>

    {{-- Legend / Status Indicators --}}
    <div class="d-flex flex-wrap gap-2 mb-4 p-2 rounded-pill shadow-sm border" style="background: var(--bg-card); width: fit-content; border-color: var(--border-color) !important;">
        <div class="d-inline-flex align-items-center bg-light px-3 py-1 rounded-pill mr-2" style="background: rgba(16, 185, 129, 0.1) !important;">
            <span class="mr-2" style="height: 8px; width: 8px; background: #10B981; border-radius: 50%; display: inline-block;"></span>
            <span class="tiny-font font-bold text-success">Approved</span>
        </div>
        <div class="d-inline-flex align-items-center bg-light px-3 py-1 rounded-pill mr-2" style="background: rgba(245, 158, 11, 0.1) !important;">
            <span class="mr-2" style="height: 8px; width: 8px; background: #F59E0B; border-radius: 50%; display: inline-block;"></span>
            <span class="tiny-font font-bold" style="color: #F59E0B;">Pending</span>
        </div>
        <div class="d-inline-flex align-items-center bg-light px-3 py-1 rounded-pill mr-2" style="background: rgba(59, 130, 246, 0.1) !important;">
            <span class="mr-2" style="height: 8px; width: 8px; background: #3B82F6; border-radius: 50%; display: inline-block;"></span>
            <span class="tiny-font font-bold text-primary">Completed</span>
        </div>
        <div class="d-inline-flex align-items-center bg-light px-3 py-1 rounded-pill" style="background: rgba(239, 68, 68, 0.1) !important;">
            <span class="mr-2" style="height: 8px; width: 8px; background: #EF4444; border-radius: 50%; display: inline-block;"></span>
            <span class="tiny-font font-bold text-danger">Rejected</span>
        </div>
    </div>

    {{-- Calendar Card --}}
    <div class="card card-premium border-0 shadow-sm" style="border-radius: 24px;">
        <div class="card-body p-4">
            <div id="calendar" style="min-height: 700px;"></div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* FullCalendar Premium Customization */
    :root {
        --fc-border-color: var(--border-color);
        --fc-daygrid-event-dot-width: 8px;
        --fc-button-bg-color: var(--primary);
        --fc-button-border-color: var(--primary);
        --fc-button-hover-bg-color: #0d9668;
        --fc-button-hover-border-color: #0d9668;
        --fc-button-active-bg-color: #0d9668;
        --fc-button-active-border-color: #0d9668;
        --fc-page-bg-color: var(--bg-card);
        --fc-neutral-bg-color: var(--bg-body);
        --fc-list-event-hover-bg-color: var(--bg-body);
        --fc-today-bg-color: rgba(16, 185, 129, 0.05);
    }
    
    .fc .fc-toolbar-title { 
        font-size: 1.5rem; 
        font-weight: 800; 
        color: var(--text-dark); 
        letter-spacing: -0.02em;
    }

    .fc .fc-button { 
        font-weight: 700; 
        border-radius: 50px !important; 
        padding: 10px 22px !important;
        text-transform: capitalize;
        transition: all 0.2s ease;
        font-size: 0.85rem;
        border: none !important;
        box-shadow: var(--shadow-sm);
    }

    .fc .fc-button:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }
    
    .fc .fc-button-primary:not(:disabled).fc-button-active, 
    .fc .fc-button-primary:not(:disabled):active {
        background-color: var(--primary) !important;
        filter: brightness(0.9);
    }

    .fc-theme-standard td, .fc-theme-standard th {
        border-color: var(--border-color) !important;
    }

    .fc .fc-col-header-cell-cushion {
        padding: 15px 0 !important;
        color: var(--text-muted) !important;
        font-weight: 700 !important;
        font-size: 0.75rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
    }

    .fc .fc-daygrid-day-number {
        padding: 12px !important;
        font-weight: 600 !important;
        color: var(--text-dark) !important;
        font-size: 0.9rem;
    }

    .fc-event { 
        cursor: pointer; 
        border: none !important; 
        padding: 5px 10px !important; 
        border-radius: 10px !important; 
        transition: 0.2s; 
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        font-weight: 600 !important;
        font-size: 0.78rem !important;
    }

    .fc-event:hover { 
        filter: brightness(1.1); 
        transform: scale(1.02); 
    }

    .fc-day-today { 
        background: var(--fc-today-bg-color) !important; 
    }

    .fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
        color: var(--primary) !important;
        background: var(--bg-body);
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 8px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        if (!calendarEl) return;

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: 'auto',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: @json($events),
            eventTimeFormat: { 
                hour: 'numeric',
                minute: '2-digit',
                meridiem: 'short'
            },
            eventClick: function(info) {
                Swal.fire({
                    title: info.event.title,
                    html: `
                        <div class="text-left mt-3" style="font-size: 0.95rem;">
                            <p class="mb-2"><strong><i class="ph ph-clock mr-2"></i>Time:</strong> ${info.event.start.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</p>
                            <p class="mb-2"><strong><i class="ph ph-user mr-2"></i>Customer:</strong> ${info.event.extendedProps.customer}</p>
                            <p class="mb-2"><strong><i class="ph ph-tag mr-2"></i>Service:</strong> ${info.event.extendedProps.service}</p>
                            <p class="mb-0"><strong><i class="ph ph-info mr-2"></i>Status:</strong> <span class="badge badge-pill badge-primary" style="background-color: ${info.event.backgroundColor}">${info.event.extendedProps.status}</span></p>
                        </div>
                    `,
                    icon: 'info',
                    confirmButtonColor: '#10B981',
                    confirmButtonText: 'Close'
                });
            }
        });

        calendar.render();
    });
</script>
@endpush