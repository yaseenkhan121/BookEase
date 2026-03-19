<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'BookEase') }} - Dashboard</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Custom Scrollbar - Premium SaaS Style */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: var(--bg-body);
        }
        ::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary);
        }

        body.dark-mode ::-webkit-scrollbar-track {
            background: #000000;
        }
        body.dark-mode ::-webkit-scrollbar-thumb {
            background: #2a2a2a;
        }
        body.dark-mode ::-webkit-scrollbar-thumb:hover {
            background: var(--primary);
        }

        :root {
            /* Primary Green Palette */
            --primary: #1E7C62;
            --primary-dark: #16614D;
            --primary-light: #2DAA84;
            --primary-gradient: linear-gradient(135deg, #1E7C62, #2DAA84);
            --primary-glow: 0 4px 15px rgba(30, 124, 98, 0.3);

            /* Neutral Palette */
            --bg-body: #F5F7F6;
            --bg-card: #FFFFFF;
            --border-color: #EAEAEA;
            --text-dark: #1F1F1F;
            --text-muted: #7A7A7A;
            --text-label: #5A5A5A;

            /* Slate Scale (for backwards compat with existing views) */
            --slate-50: #F5F7F6;
            --slate-100: #EEFAF3;
            --slate-200: #EAEAEA;
            --slate-300: #D1D5DB;
            --slate-400: #9CA3AF;
            --slate-500: #7A7A7A;
            --slate-600: #5A5A5A;
            --slate-700: #3A3A3A;
            --slate-800: #1F1F1F;
            --slate-900: #111111;

            /* Shadows — soft, diffused SaaS style */
            --shadow-sm: 0 1px 4px 0 rgba(0, 0, 0, 0.04);
            --shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 20px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 8px 30px rgba(0, 0, 0, 0.1);
            --shadow-card: 0 2px 12px rgba(0, 0, 0, 0.05);

            /* Radius */
            --radius-lg: 14px;
            --radius-xl: 18px;
            --radius-2xl: 20px;
        }

        /* ========== DARK MODE OVERRIDES (Green Edition) ========== */
        body.dark-mode {
            --bg-body: #000000;  /* Pure Black */
            --bg-card: #0a0a0a;  /* Deep Matte Black */
            --bg-secondary: #141414;  /* Slightly lighter black */
            --bg-glass: rgba(10, 10, 10, 0.7);
            --danger-color: #ff5c5c;
            --border-color: #1f1f1f;
            --border-light: rgba(255, 255, 255, 0.05);
            --text-dark: #f8fafc;
            --text-muted: #94a3b8;
            --text-label: #cbd5e1;
            --slate-50: #080808;
            --slate-100: #000000;
            --slate-200: #111111;
            --slate-300: #1a1a1a;
            --slate-400: #2a2a2a;
            --slate-500: #6b8c83;
            --slate-600: #8da6a0;
            --slate-700: #a8bcba;
            --slate-800: #ecfdf5;
            --slate-900: #f0fff4;
            --shadow-premium: 0 10px 40px rgba(0, 0, 0, 0.8), 0 0 0 1px #1f1f1f;
            --inner-glow: inset 0 1px 1px rgba(255, 255, 255, 0.03);
        }

        body.dark-mode .sidebar { background: var(--bg-card); border-right-color: var(--border-color); box-shadow: 10px 0 30px rgba(0,0,0,0.5); }
        body.dark-mode header { 
            background-color: var(--bg-glass) !important; 
            backdrop-filter: blur(12px) !important;
            -webkit-backdrop-filter: blur(12px) !important;
            border-bottom-color: var(--border-color) !important; 
        }
        body.dark-mode .card, body.dark-mode .card-premium { 
            background: var(--bg-card); 
            border-color: var(--border-color); 
            box-shadow: var(--shadow-premium);
            background-image: linear-gradient(180deg, rgba(255,255,255,0.02) 0%, rgba(255,255,255,0) 100%);
        }
        body.dark-mode .card-header { background-color: var(--bg-card) !important; border-bottom-color: var(--border-color) !important; }
        body.dark-mode .nav-link { color: var(--text-muted); }
        body.dark-mode .nav-link:hover { background: rgba(30, 124, 98, 0.1); color: var(--primary-light); }
        body.dark-mode .text-dark, body.dark-mode .font-weight-bold, body.dark-mode .fw-bold, body.dark-mode .fw-semibold, body.dark-mode .fw-extrabold, body.dark-mode h1, body.dark-mode h2, body.dark-mode h3, body.dark-mode h4, body.dark-mode h5, body.dark-mode h6 { color: #ffffff !important; }
        body.dark-mode .text-muted { color: #94a3b8 !important; }
        body.dark-mode .bg-white { background-color: var(--bg-card) !important; }
        body.dark-mode .bg-light { background-color: var(--bg-card) !important; }
        body.dark-mode .table thead th { background-color: transparent !important; color: var(--text-muted) !important; border-bottom-color: var(--border-color) !important; }
        body.dark-mode .table tbody tr:hover { background: rgba(30, 124, 98, 0.05); }
        body.dark-mode .form-control { background-color: var(--bg-body) !important; color: var(--text-dark) !important; border-color: var(--border-color) !important; }
        body.dark-mode .search-container { background: var(--bg-body); border-color: var(--border-color); }
        body.dark-mode .search-input { color: #f1f5f9; }
        body.dark-mode .alert-success { background-color: #052e16; color: #dcfce7; border-color: #0d4a2a; }
        body.dark-mode .modal-content { background-color: var(--bg-card); color: var(--text-dark); border-color: var(--border-color); }
        body.dark-mode .pagination .page-link { background-color: var(--bg-card); border-color: var(--border-color); color: var(--text-muted); }
        body.dark-mode .pagination .page-item.active .page-link { background-color: var(--primary); border-color: var(--primary); color: #fff; }
        body.dark-mode .badge.bg-light { background-color: #0c1a14 !important; color: var(--text-dark) !important; border-color: #1a2f2a !important; }
        body.dark-mode .status-pill { border-color: rgba(30, 124, 98, 0.2); }
        body.dark-mode .border, body.dark-mode .border-bottom, body.dark-mode .border-top { border-color: var(--border-color) !important; }
        body.dark-mode .text-slate-600, body.dark-mode .text-slate-700 { color: var(--text-label) !important; }
        body.dark-mode .text-slate-500, body.dark-mode .text-slate-400 { color: var(--text-muted) !important; }
        body.dark-mode .status-pill.status-active { color: #34D399 !important; background: rgba(16, 185, 129, 0.15); border-color: rgba(16, 185, 129, 0.3); }
        body.dark-mode .status-pill.status-inactive { color: #FBBF24 !important; background: rgba(245, 158, 11, 0.15); border-color: rgba(245, 158, 11, 0.3); }
        body.dark-mode .bg-slate-50, body.dark-mode .bg-slate-100 { background-color: var(--bg-body) !important; }
        body.dark-mode .btn-light { background-color: var(--bg-card) !important; border-color: var(--border-color) !important; color: var(--text-dark) !important; }
        body.dark-mode .btn-outline-secondary { border-color: var(--border-color) !important; color: var(--text-muted) !important; }
        body.dark-mode .btn-outline-secondary:hover { background-color: rgba(30, 124, 98, 0.1) !important; color: var(--primary-light) !important; border-color: var(--primary) !important; }
        body.dark-mode .table td { border-color: var(--border-color) !important; color: var(--text-dark); }
        
        /* SweetAlert2 Dark Mode Overrides */
        body.dark-mode .swal2-popup {
            background-color: #0d0d0d !important;
            color: #ffffff !important;
            border: 1px solid var(--border-color) !important;
            box-shadow: 0 10px 40px rgba(0,0,0,0.8) !important;
        }
        body.dark-mode .swal2-title, body.dark-mode .swal2-html-container { color: #ffffff !important; }
        body.dark-mode .swal2-toast { background-color: #121212 !important; border: 1px solid var(--border-color) !important; }
        body.dark-mode .swal2-timer-progress-bar { background: var(--primary) !important; }
        body.dark-mode .swal2-success-circular-line-left,
        body.dark-mode .swal2-success-circular-line-right,
        body.dark-mode .swal2-success-fix { background-color: transparent !important; }

        /* Force Icons to be "Light Mode" style even in Dark Mode */
        body.dark-mode [class^="ph-"], 
        body.dark-mode [class*=" ph-"] { 
            /* If the icon is not inside a colored button/badge, make it brighter */
            filter: brightness(1.7) contrast(1.2);
            opacity: 0.95 !important;
        }
        
        /* Sidebar Icons specifically */
        body.dark-mode .nav-link i { 
            color: var(--primary-light) !important; 
            filter: brightness(1.5);
        }
        body.dark-mode .nav-link.active i { 
            color: #ffffff !important; 
            filter: none;
        }
        body.dark-mode .bg-light-soft { background-color: rgba(255, 255, 255, 0.03) !important; }
        body.dark-mode .bg-slate-50 { background-color: #080808 !important; }

        /* Stat Card Icons */
        body.dark-mode .card-premium .ph {
            filter: brightness(1.2) contrast(1.1);
        }

        /* Decoration Utilities */
        .d-dark-only { display: none; }
        body.dark-mode .d-dark-only { display: block; }

        .glass-panel {
            background: var(--bg-glass);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
        }

        body.dark-mode .premium-shadow {
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4), 0 0 0 1px var(--border-color) !important;
        }

        body.dark-mode .inner-glow {
            box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.05) !important;
        }

        * { box-sizing: border-box; }

        body { 
            font-family: 'Inter', sans-serif; 
            background-color: var(--bg-body); 
            color: var(--text-dark); 
            line-height: 1.6;
            letter-spacing: -0.012em;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        h1, h2, h3, h4, h5, h6 {
            letter-spacing: -0.025em;
            font-weight: 700;
            line-height: 1.25;
        }
        
        /* ========== SIDEBAR ========== */
        .sidebar { 
            width: 270px; 
            background: var(--bg-card);
            border-right: 1px solid var(--border-color); 
            display: flex; 
            flex-direction: column; 
            position: fixed; 
            height: 100vh;
            z-index: 1000; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .main-content { 
            margin-left: 270px; 
            min-height: 100vh; 
            width: calc(100% - 270px); 
            transition: 0.3s;
        }
        
        /* ========== NAV LINKS ========== */
        .nav-link { 
            color: var(--text-muted); 
            font-weight: 500; 
            padding: 0.75rem 1.15rem; 
            border-radius: 12px; 
            margin: 3px 0.85rem; 
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            text-decoration: none !important;
            border: none;
            background: transparent;
            font-size: 0.92rem;
        }
        
        .nav-link:hover { 
            background: rgba(30, 124, 98, 0.06);
            color: var(--primary);
            transform: translateX(3px);
        }
        
        .nav-link.active { 
            background: var(--primary-gradient) !important; 
            color: white !important; 
            font-weight: 600;
            box-shadow: var(--primary-glow);
        }
        
        .nav-link i { font-size: 1.3rem; margin-right: 11px; }
        
        /* ========== PREMIUM CARDS ========== */
        .card-premium {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-card);
            transition: all 0.22s ease;
        }
        .card-premium:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-3px);
        }

        /* ========== SEARCH BAR ========== */
        .search-container {
            display: flex;
            align-items: center;
            background: var(--bg-body);
            border-radius: var(--radius-lg);
            padding: 0 16px;
            width: 480px;
            height: 44px;
            border: 1px solid var(--border-color);
            transition: all 0.25s ease;
        }
        .search-container:focus-within {
            background: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30, 124, 98, 0.1);
        }
        .search-icon {
            font-size: 1.15rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
        }
        .search-input {
            border: none;
            outline: none;
            background: transparent;
            width: 100%;
            font-size: 0.9rem;
            color: var(--text-dark);
            padding-left: 10px;
        }
        .search-input::placeholder { color: var(--text-muted); }

        /* ========== AVATAR ========== */
        .user-avatar-wrapper {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            background: var(--bg-body);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s ease;
        }
        .user-avatar-wrapper:hover { transform: scale(1.08); box-shadow: var(--shadow); }
        .user-avatar-wrapper img { width: 100%; height: 100%; object-fit: cover; }

        /* ========== LOGO ========== */
        .logo-box {
            background: var(--primary-gradient); 
            width: 36px; height: 36px; 
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(30, 124, 98, 0.25);
        }

        /* ========== STATUS PILLS (Saas Style) ========== */
        .status-pill {
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid transparent;
        }
        .status-pill::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }
        .status-pill.status-active { 
            background: rgba(16, 185, 129, 0.1); 
            color: #065F46; 
            border-color: rgba(16, 185, 129, 0.2);
        }
        .status-pill.status-active::before { background: #10B981; }
        
        .status-pill.status-inactive { 
            background: rgba(245, 158, 11, 0.1); 
            color: #92400E; 
            border-color: rgba(245, 158, 11, 0.2);
        }
        .status-pill.status-inactive::before { background: #F59E0B; }

        /* ========== GLOBAL BUTTONS ========== */
        .btn-primary, .btn-modern-primary {
            background: var(--primary-gradient) !important;
            border: none !important;
            color: white !important;
            font-weight: 600;
            border-radius: 50px;
            padding: 10px 24px;
            transition: all 0.2s ease;
            box-shadow: var(--primary-glow);
            text-decoration: none !important;
        }
        .btn-primary:hover, .btn-modern-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(30, 124, 98, 0.35);
            filter: brightness(1.08);
        }

        .btn-modern-light {
            background: var(--bg-body);
            color: var(--text-dark);
            border: 1px solid var(--border-color) !important;
            border-radius: 50px;
            padding: 10px 24px;
            transition: all 0.2s ease;
        }
        .btn-modern-light:hover {
            background: var(--bg-card);
            border-color: var(--primary) !important;
            color: var(--primary);
            transform: translateY(-2px);
        }

        .btn-action {
            padding: 6px 14px !important;
            font-size: 0.82rem !important;
            font-weight: 600 !important;
            border-radius: 12px !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.2s ease;
        }
        .btn-action i { font-size: 1rem; }
        .btn-action:hover { transform: translateY(-1px); }

        /* ========== TABLES ========== */
        .table thead th {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-color);
        }
        .table tbody tr { transition: background 0.15s ease; }
        .table tbody tr:hover { background: rgba(30, 124, 98, 0.02); }

        /* ========== FORMS & INPUTS ========== */
        .form-control, .custom-input, select, textarea {
            background-color: var(--bg-card) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: var(--radius-lg) !important;
            padding: 12px 16px !important;
            height: auto !important; /* CRITICAL: Prevent clipping */
            min-height: 46px;
            color: var(--text-dark) !important;
            font-size: 0.92rem !important;
            transition: all 0.2s ease !important;
            box-shadow: none !important;
        }

        .form-control:focus, .custom-input:focus, select:focus, textarea:focus {
            background-color: var(--bg-card) !important;
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px rgba(30, 124, 98, 0.1) !important;
            outline: 0;
        }

        .form-group label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 8px;
            display: block;
        }

        .tiny-font {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 992px) {
            .sidebar { margin-left: -270px; }
            .main-content { margin-left: 0; width: 100%; }
            .sidebar.show { margin-left: 0; box-shadow: var(--shadow-lg); }
            .search-container { width: 100%; max-width: 300px; }
        }

        /* ========== COLLAPSING HEADER ========== */
        .collapsing-header {
            position: sticky;
            top: 70px; /* Aligns right below the top navbar */
            z-index: 1020;
            background-color: var(--bg-card); /* Matches page background */
            transition: all 0.35s cubic-bezier(0.25, 1, 0.5, 1);
            margin-left: -24px;
            margin-right: -24px;
            padding-left: 24px;
            padding-right: 24px;
            padding-top: 16px;
            padding-bottom: 16px;
            margin-top: -24px; /* Pulls it up into the container padding */
            border-bottom: 1px solid transparent;
        }

        .collapsing-header.is-collapsed {
            box-shadow: var(--shadow-sm);
            border-bottom: 1px solid var(--border-color);
            padding-top: 10px;
            padding-bottom: 10px;
            background-color: var(--bg-card);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }



        .hide-on-scroll {
            transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
            max-height: 500px;
            opacity: 1;
            overflow: hidden;
            transform-origin: top;
            transform: scaleY(1);
        }

        .is-collapsed .hide-on-scroll {
            max-height: 0;
            opacity: 0;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            border: none !important;
            transform: scaleY(0);
        }

        .show-on-scroll {
            transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            display: none !important;
        }

        .is-collapsed .show-on-scroll {
            display: block !important;
        }

        /* A trick to animate display changes: wait for next frame to change opacity/height */
        .is-collapsed.header-animating .show-on-scroll {
            max-height: 100px;
            opacity: 1;
        }

        .is-collapsed .show-on-scroll-flex {
            display: flex !important;
        }
        .is-collapsed.header-animating .show-on-scroll-flex {
            max-height: 100px;
            opacity: 1;
        }

        .shrink-on-scroll {
            transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
            transform-origin: left center;
        }

        .is-collapsed .shrink-on-scroll {
            transform: scale(0.8);
            margin-bottom: 0 !important;
        }
    </style>
    @stack('styles')
</head>
<body class="{{ auth()->check() ? auth()->user()->theme_preference . '-mode' : 'light-mode' }}">
    <div class="dashboard-layout">
        <aside class="sidebar py-4">
            <div class="logo mb-5 px-4 d-flex align-items-center gap-2">
                <div class="logo-box d-flex align-items-center justify-content-center mr-2">
                    <i class="ph ph-calendar-check text-white"></i>
                </div>
                <span style="font-weight: 800; color: var(--text-dark); font-size: 1.4rem;">BookEase</span>
            </div>

            <nav class="flex-grow-1 overflow-auto">
                @if(auth()->user()->isAdmin())
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="ph ph-presentation-chart"></i> Admin Dashboard
                </a>
                <a href="{{ route('analytics') }}" class="nav-link {{ request()->routeIs('analytics') ? 'active' : '' }}">
                    <i class="ph ph-chart-line-up"></i> Analytics
                </a>
                <a href="{{ route('admin.providers.index') }}" class="nav-link {{ request()->routeIs('admin.providers.*') ? 'active' : '' }}">
                    <i class="ph ph-shield-check"></i> 
                    Provider Approval
                    @if(isset($pendingProvidersCount) && $pendingProvidersCount > 0)
                        <span class="badge bg-danger text-white ml-auto rounded-pill px-2" style="font-size: 0.7rem;">{{ $pendingProvidersCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.customers.index') }}" class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                    <i class="ph ph-users-three"></i> Customers
                </a>
                <a href="{{ route('admin.bookings.index') }}" class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                    <i class="ph ph-calendar-blank"></i> All Bookings
                </a>


                @elseif(auth()->user()->isProvider())
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="ph ph-house-line"></i> Dashboard
                </a>
                <a href="{{ route('analytics') }}" class="nav-link {{ request()->routeIs('analytics') ? 'active' : '' }}">
                    <i class="ph ph-chart-line-up"></i> Analytics
                </a>
                <a href="{{ route('provider.bookings.index') }}" class="nav-link {{ request()->routeIs('provider.bookings.*') ? 'active' : '' }}">
                    <i class="ph ph-calendar-blank"></i> My Bookings
                </a>
                <a href="{{ route('calendar') }}" class="nav-link {{ request()->routeIs('calendar') ? 'active' : '' }}">
                    <i class="ph ph-calendar-check"></i> Schedule View
                </a>
                <a href="{{ route('provider.services.index') }}" class="nav-link {{ request()->routeIs('provider.services.*') ? 'active' : '' }}">
                    <i class="ph ph-shopping-bag"></i> My Services
                </a>
                <a href="{{ route('provider.availability.index') }}" class="nav-link {{ request()->routeIs('provider.availability.*') ? 'active' : '' }}">
                    <i class="ph ph-clock"></i> Availability
                </a>

                @else
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="ph ph-house"></i> Dashboard
                </a>
                <a href="{{ route('analytics') }}" class="nav-link {{ request()->routeIs('analytics') ? 'active' : '' }}">
                    <i class="ph ph-chart-line-up"></i> Analytics
                </a>
                <a href="{{ route('providers') }}" class="nav-link {{ request()->routeIs('providers*') ? 'active' : '' }}">
                    <i class="ph ph-user-circle"></i> Find Providers
                </a>
                <a href="{{ route('bookings.index') }}" class="nav-link {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                    <i class="ph ph-calendar-blank"></i> My Bookings
                </a>
                
                <a href="{{ route('calendar') }}" class="nav-link {{ request()->routeIs('calendar') ? 'active' : '' }}">
                    <i class="ph ph-calendar"></i> Calendar
                </a>
                @endif

                <hr class="mx-4 my-3" style="border-top: 1px solid var(--border-color);">

                {{-- Unified Routes --}}
                <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                    <i class="ph ph-bell"></i> 
                    Notifications
                    @auth
                        @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                        @if($unreadCount > 0)
                            <span class="badge badge-danger badge-notification ml-auto">{{ $unreadCount }}</span>
                        @endif
                    @endauth
                </a>

                <a href="{{ route('settings') }}" class="nav-link {{ request()->routeIs('settings*') ? 'active' : '' }}">
                    <i class="ph ph-gear"></i> Settings
                </a>
            </nav>
            
            <div class="px-3 mt-auto">
                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                    @csrf
                    <button type="button" onclick="confirmLogout()" class="btn nav-link w-100 text-left border-0 bg-transparent mb-4">
                        <i class="ph ph-sign-out mr-2 text-danger"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <main class="main-content">
            <header class="border-bottom py-2 px-4 d-flex justify-content-between align-items-center shadow-sm sticky-top" style="height: 70px; background-color: var(--bg-card);">
                {{-- Mobile Toggle --}}
                <button class="btn d-lg-none p-0 border-0" onclick="document.querySelector('.sidebar').classList.toggle('show')">
                    <i class="ph ph-list text-dark" style="font-size: 1.5rem;"></i>
                </button>

                {{-- Search Bar Removed --}}
                <div class="header-search flex-grow-1 mx-4 d-none d-md-block">
                    <!-- Global search removed to keep only one clear search bar per page -->
                </div>

                <div class="d-flex align-items-center ml-auto">
                    @auth
                    <div class="text-right mr-3 d-none d-sm-block">
                        <p class="mb-0 font-weight-bold small text-dark" style="line-height: 1.2;">{{ auth()->user()->name }}</p>
                        <p class="mb-0 text-muted" style="font-size: 0.75rem;">{{ ucfirst(auth()->user()->role) }} Account</p>
                    </div>
                    
                    <a href="{{ route('settings.profile') }}" class="user-avatar-wrapper border text-decoration-none">
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" id="navbar-avatar">
                    </a>
                    @endauth
                </div>
            </header>

            <div class="p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-left: 4px solid var(--primary) !important;">
                        <i class="ph ph-check-circle mr-2"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-left: 4px solid #B91C1C !important;">
                        <i class="ph ph-warning-circle mr-2"></i> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function confirmLogout() {
            Swal.fire({
                title: 'Sign Out?',
                text: "Are you sure you want to log out of BookEase?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Logout',
                customClass: { popup: 'rounded-4' }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            })
        }

        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const button = document.getElementById(inputId + '-toggle');
            
            // Fallback for elements without the -toggle suffix in ID
            const targetButton = button || document.querySelector(`button[onclick*="'${inputId}'"]`);
            if (!targetButton) return;

            const icon = targetButton.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('ph-eye');
                icon.classList.add('ph-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('ph-eye-slash');
                icon.classList.add('ph-eye');
            }
        }

        // Real-time Initialization
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof window.Echo !== 'undefined') {
                @auth
                    const userId = {{ auth()->id() }};
                    const userRole = "{{ auth()->user()->role }}";

                            // 1. Listen for Private User Notifications
                            window.Echo.private(`user.${userId}`)
                                .listen('ProfileUpdated', (e) => {
                                    console.log('Real-time: Profile Updated', e);
                                    // Update navbar name 
                                    const nameElements = document.querySelectorAll('.text-dark.font-weight-bold.small');
                                    nameElements.forEach(el => {
                                        if (el.innerText.trim() !== 'Administrator') {
                                            el.innerText = e.user.name;
                                        }
                                    });
                                    Swal.fire({
                                        title: 'Profile Updated',
                                        text: 'Your profile changes have been synced across todos.',
                                        icon: 'success',
                                        toast: true,
                                        position: 'top-end',
                                        timer: 3000,
                                        showConfirmButton: false
                                    });
                                })
                                .listen('ProfileImageUpdated', (e) => {
                                    console.log('Real-time: Profile Image Updated', e);
                                    // Update all avatar images on page
                                    const avatars = document.querySelectorAll('img[src*="profile_images"], img[id*="avatar"]');
                                    avatars.forEach(img => {
                                        img.src = e.user.avatar_url + '?v=' + Date.now();
                                    });
                                    
                                    // Specifically target navbar avatar
                                    const navbarAvatar = document.getElementById('navbar-avatar');
                                    if (navbarAvatar) navbarAvatar.src = e.user.avatar_url + '?v=' + Date.now();
                                })
                                .notification((notification) => {
                            console.log('Real-time Notification Received:', notification);
                            
                            // Update Badge
                            const badge = document.querySelector('.badge-notification');
                            if (badge) {
                                let count = parseInt(badge.innerText) || 0;
                                badge.innerText = count + 1;
                                badge.classList.remove('d-none');
                            } else {
                                // Create badge if missing
                                const bellIcon = document.querySelector('.ph-bell');
                                if (bellIcon) {
                                    const newBadge = document.createElement('span');
                                    newBadge.className = 'badge badge-danger badge-notification ml-auto';
                                    newBadge.innerText = '1';
                                    bellIcon.parentElement.appendChild(newBadge);
                                }
                            }

                            // Show Toast
                            Swal.fire({
                                title: notification.title || 'New Notification',
                                text: notification.message || 'You have new updates.',
                                icon: 'info',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 5000,
                                timerProgressBar: true
                            });

                            // Instant Table Refresh (if on relevant page)
                            if (window.location.pathname.includes('bookings')) {
                                // Simple reload or AJAX refresh could go here
                                // For now, we'll suggest a refresh if it's a critical update
                            }
                        });

                    // 2. Admin Dashboard Live Updates
                    if (userRole === 'admin') {
                        window.Echo.private('admin.dashboard')
                            .listen('BookingCreated', (e) => {
                                console.log('Admin: New Booking Created Live', e);
                                Swal.fire({
                                    title: 'Live: New Booking',
                                    text: `A new booking was just created for ${e.appointment.customer_name}`,
                                    icon: 'success',
                                    toast: true,
                                    position: 'top-end',
                                    timer: 4000
                                });
                            })
                            .listen('ProviderRegistered', (e) => {
                                console.log('Admin: New Provider Registered Live', e);
                                Swal.fire({
                                    title: 'Live: New Provider',
                                    text: `${e.user.name} just registered as a service provider!`,
                                    icon: 'info',
                                    toast: true,
                                    position: 'top-end',
                                    timer: 4000
                                });
                            });
                    }

                    // 3. Public: New Provider Live Update
                    window.Echo.channel('providers')
                        .listen('ProviderRegistered', (e) => {
                            console.log('Public: New Provider Live!', e);
                            Swal.fire({
                                title: 'New Professional!',
                                text: `${e.user.name} just joined the platform and is available for booking!`,
                                icon: 'success',
                                toast: true,
                                position: 'top-end',
                                timer: 6000
                            });

                            // If on the providers listing page, we could dynamically append or show a "New results available" banner
                            if (window.location.pathname.includes('/providers')) {
                                const container = document.querySelector('.row.g-4');
                                if (container) {
                                    // Optionally: container.insertAdjacentHTML('afterbegin', ...);
                                    // For simplicity and to ensure correct logic/styling, we suggest a reload or show a refresh button
                                    const refreshBtn = document.createElement('div');
                                    refreshBtn.className = 'col-12 text-center mb-4';
                                    refreshBtn.innerHTML = `<button onclick="window.location.reload()" class="btn btn-primary rounded-pill px-4">New Providers Available - Refresh to View</button>`;
                                    container.prepend(refreshBtn);
                                }
                            }
                        });
                @endauth
            }
        });
    </script>

    {{-- Global AJAX Form Handler & Loading Indicators --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        // Auto-intercept forms with data-ajax="true"
        document.querySelectorAll('form[data-ajax="true"]').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = form.querySelector('[type="submit"]');
                const originalHTML = btn ? btn.innerHTML : '';
                const loadingText = btn?.dataset.loadingText || 'Processing...';

                // Show loading state
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-2" role="status"></span>' + loadingText;
                }

                const formData = new FormData(form);

                fetch(form.action, {
                    method: form.method || 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(async response => {
                    const data = await response.json().catch(() => null);
                    if (response.ok && data?.success) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message || 'Saved!', showConfirmButton: false, timer: 2500, timerProgressBar: true });
                        }
                        if (data.redirect) {
                            setTimeout(() => window.location.href = data.redirect, 800);
                        }
                    } else if (data?.errors) {
                        const msgs = Object.values(data.errors).flat().join('<br>');
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Validation Error', html: msgs, showConfirmButton: false, timer: 4000 });
                        }
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: data?.message || 'Something went wrong.', showConfirmButton: false, timer: 3000 });
                        }
                    }
                })
                .catch(() => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Network error. Please try again.', showConfirmButton: false, timer: 3000 });
                    }
                })
                .finally(() => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = originalHTML;
                    }
                });
            });
        });

        // Global: Add loading state to ALL submit buttons (even non-AJAX forms)
        document.querySelectorAll('form:not([data-ajax="true"])').forEach(function(form) {
            form.addEventListener('submit', function() {
                const btn = form.querySelector('[type="submit"]');
                if (btn && !btn.disabled) {
                    btn.disabled = true;
                    const loadingText = btn.dataset.loadingText || 'Processing...';
                    btn.dataset.originalHtml = btn.innerHTML;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-2" role="status"></span>' + loadingText;
                    // Re-enable after 8 seconds as safety net
                    setTimeout(() => {
                        if (btn.disabled) {
                            btn.disabled = false;
                            btn.innerHTML = btn.dataset.originalHtml || 'Submit';
                        }
                    }, 8000);
                }
            });
        });
    });
    </script>

    @stack('scripts')
    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const button = document.querySelector(`#${inputId}-toggle`);
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('ph-eye');
                icon.classList.add('ph-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('ph-eye-slash');
                icon.classList.add('ph-eye');
            }
        }
    </script>
</body>
</html>