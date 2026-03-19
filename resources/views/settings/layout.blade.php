@extends('layouts.app')

@section('content')
<style>
    /* Premium SaaS Design Tokens */
    :root {
        --saas-primary: #1F7A63;
        --saas-primary-soft: rgba(31, 122, 99, 0.08);
        --saas-primary-gradient: linear-gradient(135deg, #1F7A63, #2DAA84);
        --saas-text-main: #111827;
        --saas-text-muted: #6B7280;
        --saas-border: #E5E7EB;
        --saas-radius-lg: 20px;
        --saas-radius-md: 12px;
        --saas-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
    }

    /* Premium Settings Layout */
    .settings-container {
        max-width: 1050px;
        margin: 0 auto;
        padding: 2.5rem 2rem;
    }

    .settings-title {
        font-size: 2.2rem !important;
        font-weight: 800 !important;
        color: var(--text-dark);
        margin-bottom: 0.25rem !important;
        letter-spacing: -0.02em !important;
    }

    .settings-subtitle {
        color: var(--text-muted);
        font-size: 1rem;
        margin-bottom: 2.5rem;
    }

    .settings-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }

    /* Tabs Navigation from Screenshot */
    .settings-nav {
        display: flex;
        background: #F8F9FA;
        border-bottom: 1px solid var(--border-color);
        padding: 10px;
        gap: 8px;
    }

    .settings-nav-link {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 14px 20px;
        border-radius: 8px;
        color: #4B5563;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none !important;
        transition: all 0.2s ease;
        background: transparent;
    }

    .settings-nav-link i {
        font-size: 1.1rem;
    }

    .settings-nav-link:hover {
        background: rgba(31, 122, 99, 0.05);
        color: #1F7A63;
    }

    .settings-nav-link.active {
        background: #1F7A63 !important;
        color: white !important;
        box-shadow: 0 4px 12px rgba(31, 122, 99, 0.2);
    }

    .settings-nav-link.active i {
        color: white !important;
    }

    /* Form Elements Standardization */
    .form-label {
        font-size: 0.82rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--text-muted);
        margin-bottom: 12px;
        display: block;
    }

    .form-control {
        background-color: #F8FAF9 !important;
        border: 1px solid #E2E8E5 !important;
        border-radius: 14px !important;
        padding: 0 24px !important;
        padding-right: 50px !important; /* Spacing for eye icons */
        font-size: 0.95rem !important;
        color: #111827 !important;
        transition: all 0.25s ease !important;
        height: 56px !important;
        line-height: 56px !important;
    }

    .form-control:focus {
        background-color: #ffffff !important;
        border-color: #1F7A63 !important;
        box-shadow: 0 0 0 4px rgba(31, 122, 99, 0.1) !important;
    }

    .btn-secondary-modern {
        background: #F3F4F6;
        color: #4B5563;
        border: none;
        border-radius: 10px;
        font-weight: 700;
        padding: 12px 28px;
        transition: all 0.2s;
    }

    .btn-save-modern {
        background: #1F7A63;
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 700;
        padding: 12px 32px;
        box-shadow: 0 4px 10px rgba(31, 122, 99, 0.2);
        transition: all 0.2s;
    }

    .btn-save-modern:hover {
        background: #166551;
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(31, 122, 99, 0.3);
        color: white;
    }

    /* Dark Mode Overrides (Green Edition Integration) */
    body.dark-mode .settings-card { background: var(--bg-card); border-color: var(--border-color); }
    body.dark-mode .settings-nav { background: var(--bg-card); border-color: var(--border-color); }
    body.dark-mode .settings-nav-link.active { border-bottom-color: var(--primary); color: var(--primary); }
    body.dark-mode .section-title, body.dark-mode .setting-title { color: var(--text-dark); }
    body.dark-mode .setting-row { border-color: var(--border-color); }
    body.dark-mode .settings-nav-link { color: var(--text-muted); }
    body.dark-mode .settings-nav-link:hover { color: var(--primary); }
    
    body.dark-mode .form-label { color: var(--text-label); }
    body.dark-mode .form-control { background-color: var(--bg-body) !important; border-color: var(--border-color) !important; color: var(--text-dark) !important; }
    body.dark-mode .btn-secondary-modern { background: #080808; color: #8da6a0; }

    /* New Sidebar Dark Mode Fixes */
    body.dark-mode .list-group { background-color: transparent !important; }
    body.dark-mode .list-group-item {
        background-color: var(--bg-card) !important;
        border-color: var(--border-color) !important;
        color: var(--text-dark) !important;
    }
    body.dark-mode .list-group-item:not(.active):hover {
        background-color: rgba(30, 124, 98, 0.05) !important;
    }
    body.dark-mode .list-group-item p.text-muted {
        color: var(--text-muted) !important;
    }
    body.dark-mode .bg-light { 
        background-color: #030303 !important; 
    }
    body.dark-mode .bg-white {
        background-color: var(--bg-card) !important;
    }
    body.dark-mode .border-white {
        border-color: var(--border-color) !important;
    }
    body.dark-mode .settings-container h6 {
        color: var(--text-dark) !important;
    }
    body.dark-mode .list-group-item.active h6,
    body.dark-mode .list-group-item.active p {
        color: #ffffff !important;
    }

    /* Setting Rows & Switches */
    .setting-list {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .setting-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid var(--border-color);
    }

    .setting-row:last-child {
        border-bottom: none;
    }

    .setting-title {
        display: block;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 4px;
        font-size: 1.05rem;
    }

    .setting-desc {
        display: block;
        color: var(--text-muted);
        font-size: 0.9rem;
        line-height: 1.5;
        max-width: 85%;
    }

    /* Premium Toggle Switch */
    .premium-switch {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 24px;
        margin-bottom: 0;
    }

    .premium-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .premium-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: var(--border-color);
        transition: .3s;
        border-radius: 34px;
    }

    .premium-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    input:checked + .premium-slider {
        background-color: #1F7A63;
        box-shadow: 0 0 12px rgba(31, 122, 99, 0.35);
    }

    input:checked + .premium-slider:before {
        transform: translateX(24px);
    }

    /* Removed redundant dark mode override to allow checked state to show green */
    body.dark-mode .premium-slider {
        background-color: #1a1a1a;
    }
    
    body.dark-mode input:checked + .premium-slider {
        background-color: #1F7A63 !important;
    }
    
    .section-title {
        font-weight: 800;
        color: var(--text-dark);
        font-size: 1.25rem;
    }
    
    .section-subtitle {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-bottom: 2rem;
    }
</style>
<div class="settings-container">
    <div class="row gx-lg-5">
        {{-- Left Column: Settings Navigation List --}}
        <div class="col-lg-4 mb-4 mb-lg-0">
            <div class="mb-4">
                <h2 class="settings-title">Settings</h2>
                <p class="settings-subtitle">Manage your account and preferences</p>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="list-group list-group-flush">
                    <a href="{{ route('settings.profile') }}" class="list-group-item list-group-item-action border-0 px-4 py-3 d-flex align-items-center {{ request()->routeIs('settings.profile') ? 'active' : '' }}">
                        <div class="rounded-circle p-2 mr-3 avatar-icon-box {{ request()->routeIs('settings.profile') ? 'active' : '' }}">
                            <i class="ph ph-user" style="font-size: 1.2rem;"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 font-weight-bold">Profile Info</h6>
                            <p class="small mb-0 text-muted">Photo and personal details</p>
                        </div>
                    </a>

                    <a href="{{ route('settings.security') }}" class="list-group-item list-group-item-action border-0 px-4 py-3 d-flex align-items-center {{ request()->routeIs('settings.security') ? 'active' : '' }}">
                        <div class="rounded-circle p-2 mr-3 avatar-icon-box {{ request()->routeIs('settings.security') ? 'active' : '' }}">
                            <i class="ph ph-lock-key" style="font-size: 1.2rem;"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 font-weight-bold">Security</h6>
                            <p class="small mb-0 text-muted">Password and protection</p>
                        </div>
                    </a>

                    <a href="{{ route('settings.notifications') }}" class="list-group-item list-group-item-action border-0 px-4 py-3 d-flex align-items-center {{ request()->routeIs('settings.notifications') ? 'active' : '' }}">
                        <div class="rounded-circle p-2 mr-3 avatar-icon-box {{ request()->routeIs('settings.notifications') ? 'active' : '' }}">
                            <i class="ph ph-bell" style="font-size: 1.2rem;"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 font-weight-bold">Notifications</h6>
                            <p class="small mb-0 text-muted">Alert preferences</p>
                        </div>
                    </a>

                    <a href="{{ route('settings.appearance') }}" class="list-group-item list-group-item-action border-0 px-4 py-3 d-flex align-items-center {{ request()->routeIs('settings.appearance') ? 'active' : '' }}">
                        <div class="rounded-circle p-2 mr-3 avatar-icon-box {{ request()->routeIs('settings.appearance') ? 'active' : '' }}">
                            <i class="ph ph-palette" style="font-size: 1.2rem;"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 font-weight-bold">Appearance</h6>
                            <p class="small mb-0 text-muted">Theme and visuals</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="mt-4 p-3 rounded-4 bg-light border border-white">
                <h6 class="font-weight-bold small text-uppercase mb-2" style="letter-spacing: 0.1em; color: var(--text-muted);">Security Tip</h6>
                <p class="small text-muted mb-0">Never share your password or OTP with anyone. Our support team will never ask for it.</p>
            </div>
        </div>

        {{-- Right Column: Dynamic Content --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 p-md-5">
                    @yield('settings_content')
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-white-transparent { background: rgba(255,255,255,0.2) !important; }
    .list-group-item.active { background-color: rgba(30, 124, 98, 0.08) !important; border-left: 4px solid var(--primary) !important; color: var(--primary) !important; }
    .list-group-item.active h6 { color: var(--primary) !important; }
    .list-group-item.active p { color: var(--primary) !important; opacity: 0.7 !important; }
    
    .avatar-icon-box { background: var(--bg-body); color: var(--primary); transition: 0.2s; }
    .avatar-icon-box.active { background: var(--primary); color: white; }
    
    .settings-card { background: transparent !important; border: none !important; box-shadow: none !important; }
</style>
@endsection
