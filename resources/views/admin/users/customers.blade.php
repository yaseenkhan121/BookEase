@extends('layouts.app')

@section('content')
<div class="container-fluid p-0" style="min-height: calc(100vh - 70px); margin-top: -24px; background-color: var(--bg-body);">
    <div class="p-4 p-md-5">

        {{-- Header --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
            <div>
                <h2 class="display-6 fw-extrabold mb-1">Customer Management</h2>
                <p class="text-muted font-medium">View and manage all registered customers</p>
            </div>
            <div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary" style="border-radius: 12px; padding: 10px 20px;">
                    <i class="ph ph-users mr-1"></i> All Users
                </a>
            </div>
        </div>

        {{-- Search Bar --}}
        <div class="card card-premium p-3 mb-4" style="border-radius: 16px; box-shadow: var(--shadow-premium) !important; border: 1px solid var(--border-color) !important;">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="d-flex gap-3 align-items-center">
                <div class="flex-grow-1 position-relative">
                    <i class="ph ph-magnifying-glass position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.1rem;"></i>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search customers by name or email..." 
                           value="{{ request('search') }}"
                           style="background: var(--bg-body); border-radius: 12px; padding: 10px 10px 10px 48px !important; font-size: 0.95rem;">
                </div>
                <button type="submit" class="btn text-white font-weight-bold" style="background: var(--primary); border-radius: 12px; padding: 10px 24px;">
                    Filter
                </button>
            </form>
        </div>

        {{-- Table --}}
        <div class="card card-premium overflow-hidden" style="border-radius: 20px; box-shadow: var(--shadow-premium) !important; border: 1px solid var(--border-color) !important;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="border-bottom border-slate-100">
                            <th class="px-4 py-3 text-uppercase small font-weight-bold text-muted">Customer</th>
                            <th class="px-4 py-3 text-uppercase small font-weight-bold text-muted text-center">Status</th>
                            <th class="px-4 py-3 text-uppercase small font-weight-bold text-muted text-center">Joined</th>
                            <th class="px-4 py-3 text-uppercase small font-weight-bold text-muted text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                        <tr class="border-bottom" style="transition: all 0.2s ease;" 
                            onmouseover="this.style.background='rgba(31, 122, 99, 0.02)';" 
                            onmouseout="this.style.background='transparent';">
                             <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar-wrapper shadow-none mr-3 flex-shrink-0" style="width: 42px; height: 42px; border: 2px solid var(--border-color);">
                                        <img src="{{ $user->avatar_url }}" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $user->name }}</div>
                                        <div class="text-muted small">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 text-center">
                                @if($user->email_verified_at)
                                    <span class="status-pill" style="background: rgba(16, 185, 129, 0.1); color: #10B981;">Verified</span>
                                @else
                                    <span class="status-pill" style="background: rgba(239, 68, 68, 0.1); color: #EF4444;">Unverified</span>
                                @endif
                            </td>
                            <td class="px-4 text-center text-muted small">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-4 text-right">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-action btn-outline-primary" title="View Details">
                                        <i class="ph ph-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-action btn-outline-secondary" title="Edit Customer">
                                        <i class="ph ph-pencil-simple"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-5 text-center">No customers found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
