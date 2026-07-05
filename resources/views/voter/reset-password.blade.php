@extends('layouts.voter')
@section('title', 'Reset Password')

@push('styles')
    @vite('resources/css/voter-password.css')
@endpush

@push('scripts')
    @vite('resources/js/voter-password.js')
@endpush

@section('content')
<div class="password-page">
    <div class="auth-icon-wrap">
        <div class="auth-icon">
            <svg fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24" width="28" height="28">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h1 class="auth-title">Set New Password</h1>
        <p class="auth-subtitle">Choose a strong password for your account</p>
    </div>

    <div class="v-card">

        @if($errors->any())
            <div class="v-alert v-alert-error">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('voter.password.update', $election) }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="v-form-group">
                <label class="v-label" for="email">Email Address</label>
                <input type="email"
                       id="email"
                       name="email"
                       class="v-input"
                       value="{{ old('email', $email) }}"
                       required
                       autocomplete="email"
                       readonly>
                @error('email')
                    <span class="v-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="v-form-group">
                <label class="v-label" for="new_password">New Password</label>

                <div class="password-field-wrap">
                    <input type="password"
                           id="new_password"
                           name="password"
                           class="v-input"
                           placeholder="Min. 8 chars, upper, lower & number"
                           required
                           autocomplete="new-password">

                    <button type="button" class="password-toggle" data-toggle-password="new_password" aria-label="Show password">
                        <svg class="icon-show" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="icon-hide" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.94 17.94A10.94 10.94 0 0112 20c-7 0-11-8-11-8a20.3 20.3 0 015.06-5.94M9.9 4.24A10.94 10.94 0 0112 4c7 0 11 8 11 8a20.3 20.3 0 01-3.22 4.44M14.12 14.12a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>

                <div class="password-strength-track">
                    <div id="passwordStrengthFill" class="password-strength-fill" data-level="0"></div>
                </div>
                <span id="passwordStrengthLabel" class="password-strength-label"></span>

                <ul class="password-requirements">
                    <li data-requirement="length">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
                        At least 8 characters
                    </li>
                    <li data-requirement="upper">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
                        One uppercase letter
                    </li>
                    <li data-requirement="number">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
                        One number
                    </li>
                    <li data-requirement="symbol">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
                        One symbol
                    </li>
                </ul>

                @error('password')
                    <span class="v-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="v-form-group">
                <label class="v-label" for="password_confirmation">Confirm Password</label>

                <div class="password-field-wrap">
                    <input type="password"
                           id="password_confirmation"
                           name="password_confirmation"
                           class="v-input"
                           placeholder="Repeat new password"
                           required
                           autocomplete="new-password">

                    <button type="button" class="password-toggle" data-toggle-password="password_confirmation" aria-label="Show password">
                        <svg class="icon-show" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="icon-hide" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.94 17.94A10.94 10.94 0 0112 20c-7 0-11-8-11-8a20.3 20.3 0 015.06-5.94M9.9 4.24A10.94 10.94 0 0112 4c7 0 11 8 11 8a20.3 20.3 0 01-3.22 4.44M14.12 14.12a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="v-btn v-btn-primary">
                Reset Password
            </button>

        </form>
    </div>

    <div class="auth-footer">
        <a href="{{ route('voter.login', $election) }}" class="auth-back-link">← Back to login</a>
    </div>

</div>
@endsection
