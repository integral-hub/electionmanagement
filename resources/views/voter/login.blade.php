@extends('layouts.voter')
@section('title', 'Voter Login')

@push('styles')
    @vite('resources/css/voter-login.css')
@endpush

@push('scripts')
    @vite('resources/js/voter-login.js')
@endpush

@section('content')
<div class="v-card">
    <div class="auth-icon-wrap">
        <div class="auth-icon">
            <svg fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24" width="28" height="28">
                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
        </div>

        <h1 class="auth-title">Voter Login</h1>
        <p class="auth-subtitle">
            Enter your credentials to access the ballot.
        </p>
    </div>

    @if($errors->any())
        <div class="v-alert v-alert-error">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('voter.login.submit', $election) }}" method="POST">
        @csrf

        @foreach($loginFields as $field)

            @switch($field['key'])

                @case('email')
                    <div class="v-form-group">
                        <label class="v-label" for="email">
                            {{ $field['label'] }}
                            <span class="required-mark">*</span>
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="v-input"
                            placeholder="you@example.com"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            required>
                    </div>
                    @break

                @case('phone')
                    <div class="v-form-group">
                        <label class="v-label" for="phone">
                            {{ $field['label'] }}
                            <span class="required-mark">*</span>
                        </label>

                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            class="v-input"
                            placeholder="+234 7xxxxxxxxx"
                            value="{{ old('phone') }}"
                            required>
                    </div>
                    @break

                @case('password')
                    <div class="v-form-group">
                        <label class="v-label" for="password">
                            {{ $field['label'] }}
                            <span class="required-mark">*</span>
                        </label>

                        <div class="login-password-wrap">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="v-input"
                                placeholder="Password"
                                autocomplete="current-password"
                                required>

                            <button
                                type="button"
                                class="login-password-toggle"
                                data-toggle-password
                                aria-label="Show password">
                                👁
                            </button>
                        </div>
                        <!-- FORGOT PASSWORD -->
                        <div class="login-forgot-wrap">
                            <a href="{{ route('voter.password.request', $election) }}" class="auth-back-link">
                                Forgot password?
                            </a>
                        </div>
                    </div>
                    @break

                @default
                    <div class="v-form-group">
                        <label class="v-label" for="{{ $field['key'] }}">
                            {{ $field['label'] }}
                            <span class="required-mark">*</span>
                        </label>

                        <input
                            type="text"
                            id="{{ $field['key'] }}"
                            name="{{ $field['key'] }}"
                            class="v-input"
                            value="{{ old($field['key']) }}"
                            placeholder="Enter your {{ strtolower($field['label']) }}"
                            required>
                    </div>

            @endswitch

        @endforeach

        <button type="submit" class="v-btn v-btn-primary">
            Access Ballot
            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" width="16" height="16">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </button>
    </form>

    @if($election->is_registration_open)
        <div class="v-divider">or</div>

        <a href="{{ route('voter.register', $election) }}" class="v-btn v-btn-secondary">
            Register as a Voter
        </a>
    @endif
</div>

<div class="auth-footer">
    Your vote is private and secure. &nbsp;·&nbsp; Powered by {{ config('app.name') }}
</div>
@endsection
