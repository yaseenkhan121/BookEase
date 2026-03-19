@extends('layouts.app')

@section('content')
<div class="row mb-5 align-items-center">
    <div class="col-lg-8">
        <h2 class="display-6 fw-extrabold mb-1" style="color: var(--text-dark);">Provider Approvals</h2>
        <p class="text-muted font-medium mb-0">Review and manage professional service provider applications.</p>
    </div>
</div>

<div class="row g-4">
    {{-- Tabs for Filtering --}}
    <div class="col-12 mb-4">
        <div class="d-flex gap-2">
            <a href="{{ route('admin.providers.index', ['status' => 'pending']) }}" 
               class="btn {{ request('status', 'pending') === 'pending' ? 'btn-modern-primary' : '' }} px-3 py-2 rounded-xl small fw-bold"
               style="{{ request('status', 'pending') !== 'pending' ? 'background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-muted);' : '' }}">
                Pending Requests
            </a>
            <a href="{{ route('admin.providers.index', ['status' => 'approved']) }}" 
               class="btn {{ request('status') === 'approved' ? 'btn-modern-primary' : '' }} px-3 py-2 rounded-xl small fw-bold"
               style="{{ request('status') !== 'approved' ? 'background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-muted);' : '' }}">
                Approved
            </a>
            <a href="{{ route('admin.providers.index', ['status' => 'rejected']) }}" 
               class="btn {{ request('status') === 'rejected' ? 'btn-modern-primary' : '' }} px-3 py-2 rounded-xl small fw-bold"
               style="{{ request('status') !== 'rejected' ? 'background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-muted);' : '' }}">
                Rejected
            </a>
        </div>
    </div>


    <div class="col-12">
        <div class="card card-premium overflow-hidden shadow-sm" style="border-radius: 20px; box-shadow: var(--shadow-premium) !important; border: 1px solid var(--border-color) !important;">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead>
                        <tr class="border-bottom" style="background: transparent;">

                            <th class="px-4 py-3 text-uppercase small font-weight-bold text-muted border-0">Provider Details</th>
                            <th class="py-3 text-uppercase small font-weight-bold text-muted border-0">Business Info</th>
                            <th class="py-3 text-center text-uppercase small font-weight-bold text-muted border-0">Registration Date</th>
                            <th class="py-3 text-center text-uppercase small font-weight-bold text-muted border-0">Status</th>
                            <th class="px-4 py-3 text-right text-uppercase small font-weight-bold text-muted border-0">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($providers as $p)
                        <tr style="transition: all 0.2s ease;" 
                            onmouseover="this.style.background='rgba(31, 122, 99, 0.02)';" 
                            onmouseout="this.style.background='transparent';">

                            <td class="px-4 py-4">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar-wrapper mr-3" style="width: 48px; height: 48px;">
                                        <img src="{{ $p->user->avatar_url }}" alt="{{ $p->owner_name }}">
                                    </div>
                                    <div>
                                        <h6 class="mb-0 font-weight-bold text-dark">{{ $p->owner_name }}</h6>
                                        <p class="mb-0 text-muted small">{{ $p->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4">
                                <h6 class="mb-0 font-weight-bold text-dark" style="font-size: 0.9rem;">{{ $p->business_name }}</h6>
                                <span class="badge px-2 py-1 mt-1" style="background: rgba(30, 124, 98, 0.1); color: var(--primary-light); border: 1px solid rgba(30, 124, 98, 0.15); border-radius: 6px; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.04em;">{{ $p->business_category }}</span>
                            </td>
                            <td class="py-4 text-center">
                                <span class="text-muted small fw-medium">
                                    {{ $p->created_at->format('M d, Y') }}<br>
                                    <span class="tiny-font">{{ $p->created_at->diffForHumans() }}</span>
                                </span>
                            </td>
                            <td class="py-4 text-center">
                                @if($p->status === 'pending')
                                    <span class="status-pill status-inactive">Pending Review</span>
                                @elseif($p->status === 'approved')
                                    <span class="status-pill status-active">Approved</span>
                                @elseif($p->status === 'rejected')
                                    <span class="status-pill px-2 py-1 text-danger bg-danger-light" style="background: rgba(239, 68, 68, 0.1); color: #B91C1C; border-color: rgba(239, 68, 68, 0.2);">Rejected</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-right">
                                <div class="btn-group gap-2">
                                    @if($p->status === 'pending')
                                        <form action="{{ route('admin.providers.approve', $p->user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-action btn-success text-white">
                                                <i class="ph ph-check"></i> Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.providers.reject', $p->user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-action btn-outline-danger">
                                                <i class="ph ph-x"></i> Reject
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('admin.users.show', $p->user->id) }}" class="btn btn-action btn-outline-primary">
                                            <i class="ph ph-eye"></i> View Profile
                                        </a>
                                    @endif
                                    
                                    {{-- Global Delete Action --}}
                                    <form action="{{ route('admin.users.destroy', $p->user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to permanently delete this provider and all their data? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-action btn-outline-danger">
                                            <i class="ph ph-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-5 text-center">
                                <div class="py-5">
                                    <i class="ph ph-users-three text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="text-muted fw-medium">No provider requests found for the selected filter.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($providers->hasPages())
                <div class="px-4 py-3 border-top">
                    {{ $providers->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
