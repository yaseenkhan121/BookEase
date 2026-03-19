@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color: var(--text-primary, #1e293b);">Notifications</h2>
            <p class="small mb-0" style="color: var(--text-secondary, #64748b);">Stay updated with your latest booking activity</p>
        </div>
        @if($unreadCount > 0)
            <form action="{{ route('notifications.markAllRead') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm fw-bold px-3 rounded-pill"
                    style="background: var(--primary); color: #fff; border: none;">
                    <i class="ph ph-checks me-1"></i> Mark all as read
                </button>
            </form>
        @endif
    </div>

    <div class="card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden;">
        <div class="list-group list-group-flush">
            @forelse ($notifications as $notification)
                <div class="list-group-item p-4 border-bottom notification-item {{ $notification->unread() ? 'is-unread' : '' }}"
                     style="background: var(--bg-card); border-color: var(--border-color) !important;">
                    <div class="d-flex align-items-start">
                        {{-- Icon --}}
                        <div class="notification-icon-wrapper me-3 mt-1">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 48px; height: 48px; 
                                        background: {{ $notification->unread() ? 'rgba(31, 124, 98, 0.15)' : 'var(--bg-secondary, #141414)' }};
                                        color: {{ $notification->unread() ? 'var(--primary)' : 'var(--text-muted)' }};">
                                <i class="ph ph-bell{{ $notification->unread() ? '-ringing' : '' }}" style="font-size: 1.4rem;"></i>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h5 class="mb-0 fw-bold" style="color: var(--text-dark); font-size: 1.1rem; letter-spacing: -0.01em;">
                                    {{ $notification->data['title'] ?? 'Notification' }}
                                </h5>
                            </div>
                            
                            <div class="text-muted small mb-2" style="font-weight: 500; opacity: 0.7;">
                                {{ $notification->created_at->diffForHumans() }}
                            </div>

                            <p class="mb-3" style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; max-width: 90%;">
                                {{ $notification->data['message'] ?? '' }}
                            </p>
                            
                            <div class="d-flex gap-3 align-items-center mt-3">
                                @if(isset($notification->data['action_url']))
                                    <a href="{{ route('notifications.read', $notification->id) }}" class="btn btn-sm btn-primary py-2 px-4 rounded-pill">
                                        View Action
                                    </a>
                                @endif
                                
                                @if($notification->unread())
                                    <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-dark py-2 px-4 rounded-pill fw-bold" style="background: #000; border: none;">
                                            Mark read
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-inline ms-2">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0 text-decoration-none opacity-50 hover-opacity-100" title="Delete">
                                        <i class="ph ph-trash" style="font-size: 1.15rem;"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5" style="background: var(--bg-card);">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width: 80px; height: 80px; background: var(--bg-body); border: 2px solid var(--border-color);">
                        <i class="ph ph-bell-slash" style="font-size: 2.5rem; color: var(--text-muted);"></i>
                    </div>
                    <h5 class="fw-bold" style="color: var(--text-dark);">All caught up!</h5>
                    <p class="small" style="color: var(--text-muted);">You don't have any new notifications at the moment.</p>
                </div>
            @endforelse
        </div>
        
        @if($notifications->hasPages())
            <div class="py-3 px-4" style="background: var(--card-bg, #fff); border-top: 1px solid var(--border-color, #e2e8f0);">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@push('styles')
<style>
    .notification-item {
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .notification-item:hover {
        background-color: var(--bg-secondary) !important;
        transform: translateX(4px);
    }
    .notification-item.is-unread {
        background: rgba(31, 124, 98, 0.03) !important;
        border-left: 4px solid var(--primary) !important;
    }
    .hover-opacity-100:hover {
        opacity: 1 !important;
    }
    .btn-dark:hover {
        filter: brightness(1.2);
    }
</style>
@endpush
@endsection