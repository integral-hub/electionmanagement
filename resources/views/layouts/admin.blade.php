<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/admin-layout.css', 'resources/js/app.js', 'resources/js/admin-layout.js'])
    @stack('styles')
</head>
<body>
<div class="sidebar-overlay" id="overlay"></div>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="sidebar-logo-mark">
            <svg viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="sidebar-logo-text">
            <div class="sidebar-org">{{ auth()->user()?->organization?->name ?? config('app.name') }}</div>
            <div class="sidebar-role">Election Platform</div>
        </div>
        <button id="collapseBtn" class="collapse-btn" aria-label="Collapse sidebar" title="Collapse sidebar">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16"><path d="M15 19l-7-7 7-7"/></svg>
        </button>
    </div>

    <div class="nav-list">
        <div class="nav-section">Main</div>

        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            <span class="nav-label">Dashboard</span>
        </a>
        @canany(['view.elections', 'view.users', 'view.roles'])
        <div class="nav-section nav-label">Manage</div>
        @can('view.elections')
        <a href="{{ route('admin.elections.index') }}" class="nav-item {{ request()->routeIs('admin.elections.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span class="nav-label">Elections</span>
        </a>
        @endcan
        @can('view.users')
        <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
            <span class="nav-label">Staff / Users</span>
        </a>
        @endcan
        @can('view.roles')
        <a href="{{ route('admin.roles.index') }}" class="nav-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            <span class="nav-label">Roles & Permissions</span>
        </a>
        @endcan
        @endcanany
        @canany(['view.audit_logs', 'view.organization'])
        <div class="nav-section nav-label">System</div>
        @can('view.audit_logs')
        <a href="{{ route('admin.audit-logs') }}" class="nav-item {{ request()->routeIs('admin.audit-logs') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span class="nav-label">Audit Logs</span>
        </a>
        @endcan
        @can('view.organization')
        <a href="{{ route('admin.organization.show') }}" class="nav-item {{ request()->routeIs('admin.organization.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <span class="nav-label">Organisation Setting</span>
        </a>
        @endcan
        @endcanany
    </div>

    <div class="sidebar-footer">
        <a href="{{ route('admin.profile.show') }}" class="uc">
            <div class="user-chip">
                <div class="user-avatar">
                    @if($url = data_get(auth()->user(), 'profile_photo_path.url'))
                    <img src="{{ $url }}" alt="{{ auth()->user()?->name }}">
                    @endif
                </div>
                <div class="user-chip-info nav-label">
                    <div class="user-name">{{ auth()->user()?->name }}</div>
                    <div class="ur">{{ auth()->user()?->roles->first()?->name ?? 'Staff' }}</div>
                </div>
            </div>
        </a>
        <form action="{{ route('admin.logout') }}" method="POST" class="signout-form">
            @csrf
            <button class="btn btn-secondary btn-block" type="submit">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span class="nav-label">Sign out</span>
            </button>
        </form>
    </div>
</aside>

<!-- Main -->
<div class="main">
    <!-- Topbar -->
    <header class="topbar">
        <button id="menuBtn" aria-label="Toggle sidebar">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="22" height="22"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <div class="topbar-title-wrap">
            <div class="topbar-title">
                @yield('page-title', 'Dashboard')
            </div>

            @if(trim($__env->yieldContent('page-subtitle')))
                <div class="topbar-subtitle">
                    @yield('page-subtitle')
                </div>
            @endif
        </div>
        <div class="topbar-actions">@yield('topbar-actions')</div>
    </header>

    <!-- Page Content -->
    <main class="page-body">
        @if(session('success'))
            <div class="alert alert-success">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</div>
@stack('scripts')
</body>
</html>
