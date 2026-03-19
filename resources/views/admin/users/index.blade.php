@extends('layouts.app')

@section('content')
<div class="container-fluid p-0" style="min-height: calc(100vh - 70px); margin-top: -24px; background-color: var(--bg-body);">
    <div class="p-4 p-md-5">

        {{-- Header --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
            <div>
                <h2 class="display-6 fw-extrabold mb-1">Users Management</h2>
                <p class="text-muted font-medium">View, manage and control all system users</p>
            </div>
        </div>

        {{-- Stats Row --}}
        <div class="row mb-4 g-3">
            @php
                $statCards = [
                    ['label' => 'Total Users', 'value' => $stats['total_users'], 'icon' => 'ph-users', 'color' => '#10B981'],
                    ['label' => 'Customers', 'value' => $stats['total_customers'], 'icon' => 'ph-user', 'color' => '#3B82F6'],
                    ['label' => 'Providers', 'value' => $stats['total_providers'], 'icon' => 'ph-briefcase', 'color' => '#8B5CF6'],
                    ['label' => 'New This Month', 'value' => $stats['new_this_month'], 'icon' => 'ph-user-plus', 'color' => '#F59E0B'],
                ];
            @endphp
            @foreach($statCards as $card)
            <div class="col-6 col-lg-3">
                <div class="card card-premium p-3 h-100 border shadow-sm" style="border-radius: 20px; background: var(--bg-card); border: 1px solid var(--border-color) !important; box-shadow: var(--shadow-premium) !important; position: relative; overflow: hidden;">
                    {{-- Decorative Glow for Dark Mode --}}
                    <div class="d-dark-only" style="position: absolute; top: -15px; right: -15px; width: 80px; height: 80px; background: {{ $card['color'] }}08; filter: blur(30px); border-radius: 50%;"></div>
                    
                    <div class="d-flex align-items-center" style="position: relative; z-index: 1;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mr-3 flex-shrink-0" 
                             style="width: 44px; height: 44px; background: {{ $card['color'] }}20; color: {{ $card['color'] }}; box-shadow: 0 0 12px {{ $card['color'] }}08; border: 1px solid {{ $card['color'] }}15;">
                            <i class="ph {{ $card['icon'] }} weight-bold" style="font-size: 1.3rem;"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted small font-weight-bold text-uppercase" style="letter-spacing: 0.06em; line-height: 1.2;">{{ $card['label'] }}</p>
                            <h4 class="mb-0 fw-bold" style="font-size: 1.4rem; letter-spacing: -0.02em; color: var(--text-dark);">{{ $card['value'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Success/Error Alerts --}}
        @if(session('success'))
            <div class="alert alert-modern-success border-0 mb-4 shadow-sm" style="border-left: 4px solid var(--primary) !important; border-radius: 12px; background: var(--bg-card);">
                <div class="d-flex align-items-center">
                    <i class="ph ph-check-circle mr-2 text-success" style="font-size: 1.25rem;"></i>
                    <span class="fw-bold">{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="alert border-0 mb-4 shadow-sm" style="background: rgba(239, 68, 68, 0.1); border-left: 4px solid #EF4444 !important; border-radius: 12px; color: #ef4444;">
                <div class="d-flex align-items-center">
                    <i class="ph ph-warning-circle mr-2" style="font-size: 1.25rem;"></i>
                    <span class="fw-bold">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Search & Filter Bar --}}
        <div class="card card-premium p-3 mb-4" style="border-radius: 16px; box-shadow: var(--shadow-premium) !important; border: 1px solid var(--border-color) !important;">
            <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex flex-column flex-md-row gap-3 align-items-md-center">
                <div class="flex-grow-1 position-relative">
                    <i class="ph ph-magnifying-glass position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.1rem;"></i>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search by name or email..." 
                           value="{{ request('search') }}"
                           style="background: var(--bg-body); border-radius: 12px; padding: 10px 10px 10px 48px !important; font-size: 0.95rem;">
                </div>
                <select name="role" class="form-control border-0" style="background: var(--bg-body); border-radius: 12px; max-width: 180px; padding: 10px 14px; font-size: 0.95rem;">
                    <option value="">All Roles</option>
                    <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Customers</option>
                    <option value="provider" {{ request('role') == 'provider' ? 'selected' : '' }}>Providers</option>
                </select>
                <button type="submit" class="btn text-white font-weight-bold" style="background: var(--primary); border-radius: 12px; padding: 10px 24px;">
                    <i class="ph ph-funnel mr-1"></i> Filter
                </button>
                @if(request('search') || request('role'))
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary" style="border-radius: 12px; padding: 10px 18px;">
                    <i class="ph ph-x mr-1"></i> Clear
                </a>
                @endif
            </form>
        </div>


        {{-- Users Table --}}
        <div class="card card-premium overflow-hidden" style="border-radius: 20px; box-shadow: var(--shadow-premium) !important; border: 1px solid var(--border-color) !important;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="border-bottom border-slate-100">

                            <th class="px-4 py-3 text-uppercase small font-weight-bold text-muted">User</th>
                            <th class="px-4 py-3 text-uppercase small font-weight-bold text-muted text-center">Role</th>
                            <th class="px-4 py-3 text-uppercase small font-weight-bold text-muted text-center">Email Verified</th>
                            <th class="px-4 py-3 text-uppercase small font-weight-bold text-muted text-center">Joined</th>
                            <th class="px-4 py-3 text-uppercase small font-weight-bold text-muted text-center">Status</th>
                            <th class="px-4 py-3 text-uppercase small font-weight-bold text-muted text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                        <tr class="border-bottom" style="transition: all 0.2s ease;" 
                            onmouseover="this.style.background='rgba(31, 122, 99, 0.02)';" 
                            onmouseout="this.style.background='transparent';">

                            {{-- User Info --}}
                             <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar-wrapper shadow-none mr-3 flex-shrink-0" style="width: 42px; height: 42px; border: 2px solid var(--border-color);">
                                        <img src="{{ $user->avatar_url }}" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="fw-bold text-truncate" style="font-size: 0.95rem; max-width: 250px;">{{ $user->name }}</div>
                                        <div class="text-muted small text-truncate" style="max-width: 250px;">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Role Badge --}}
                            <td class="px-4 text-center">
                                @if($user->role === 'provider')
                                    <span class="status-pill" style="background: rgba(124, 58, 237, 0.1); color: #7C3AED;">Provider</span>
                                @else
                                    <span class="status-pill" style="background: rgba(37, 99, 235, 0.1); color: #2563EB;">Customer</span>
                                @endif
                            </td>

                            {{-- Email Verified --}}
                            <td class="px-4 text-center">
                                @if($user->email_verified_at)
                                    <span class="status-pill" style="background: rgba(16, 185, 129, 0.1); color: #10B981;">
                                        <i class="ph ph-check-circle mr-1"></i> Verified
                                    </span>
                                @else
                                    <span class="status-pill" style="background: rgba(239, 68, 68, 0.1); color: #EF4444;">
                                        <i class="ph ph-x-circle mr-1"></i> Unverified
                                    </span>
                                @endif
                            </td>

                            {{-- Joined Date --}}
                            <td class="px-4 text-center text-muted small">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>

                            {{-- Provider Status --}}
                            <td class="px-4 text-center">
                                @if($user->isProvider() && $user->providerProfile)
                                    @if($user->providerProfile->status === 'approved')
                                        <span class="status-pill" style="background: rgba(16, 185, 129, 0.1); color: #10B981;">Active</span>
                                    @else
                                        <span class="status-pill" style="background: rgba(245, 158, 11, 0.1); color: #F59E0B;">Inactive</span>
                                    @endif
                                @else
                                    <span class="text-muted opacity-50">—</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 text-right">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-action btn-outline-primary" title="View Details">
                                        <i class="ph ph-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-action btn-outline-secondary" title="Edit User">
                                        <i class="ph ph-pencil-simple"></i>
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('⚠️ Permanently delete {{ $user->name }}? This will cancel their bookings and remove all data.');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-action btn-outline-danger" title="Delete User">
                                            <i class="ph ph-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-5 text-center border-0" style="background: var(--bg-card);">
                                <div class="py-5">
                                    <i class="ph ph-user-list text-muted opacity-25 mb-3" style="font-size: 3.5rem;"></i>
                                    <h5 class="fw-bold">No users found</h5>
                                    <p class="text-muted small">Try adjusting your search or filter criteria.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-4 d-flex justify-content-center">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
