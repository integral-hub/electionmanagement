<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Vote') — {{ $election?->name ?? config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/voter-layout.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>

<header class="voter-header">
    <div class="voter-brand">
        @if($election->organization->logo)
            <img src="{{ $election->organization->logo['url'] }}" class="voter-brand-mark">
        @else
        <div class="voter-brand-mark">
            <svg viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        @endif
        <div>
            <div class="election-name">
                @yield('page-title', $election?->name)
            </div>

            <div class="election-status">
                @if(isset($election->setting->voting_end))
                    Closes {{ $election->setting->voting_end->format('M j, Y \a\t H:i') }}
                @else
                    Election Portal
                @endif
            </div>
        </div>
    </div>
    @auth('voter')
    <div class="voter-actions">
        @yield('topbar-actions')

        <a href="{{ route('voter.password.form', $election) }}" class="voter-change-password">
            Change Password
        </a>

        <form action="{{ route('voter.logout', $election) }}" method="POST">
            @csrf
            <button type="submit" class="voter-signout">
                Sign out
            </button>
        </form>
    </div>
    @endauth
</header>

<div class="voter-container">
    @if(session('success'))
        <div class="v-alert v-alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="v-alert v-alert-error">{{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="v-alert v-alert-info">{{ session('info') }}</div>
    @endif

    @yield('content')
</div>

@stack('scripts')
</body>
</html>
