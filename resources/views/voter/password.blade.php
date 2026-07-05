@extends('layouts.voter')

@section('page-title','Change Password')

@push('styles')
    @vite('resources/css/voter-password.css')
@endpush

@push('scripts')
    @vite('resources/js/voter-password.js')
@endpush

@section('topbar-actions')
<a href="{{ route('voter.ballot', $election) }}" class="v-btn-secondary v-btn" style="width:auto;">← Back</a>
@endsection

@section('content')
<div class="password-page">
    <div class="v-card">

        <div class="v-form-group" style="margin-bottom:8px;">
            <h2 style="font-size:16px;font-weight:700;">Change Password</h2>
            <p class="v-hint">Confirm your current password, then set a new one.</p>
        </div>

        <form action="{{ route('voter.password.change', $election) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Current Password --}}
            <div class="v-form-group">
                <label class="v-label" for="current_password">Current Password *</label>

                <div class="password-field-wrap">
                    <input type="password"
                           id="current_password"
                           name="current_password"
                           class="v-input"
                           placeholder="Your current password"
                           required
                           autocomplete="current-password">

                    <button type="button" class="password-toggle" data-toggle-password="current_password" aria-label="Show password">
                        <svg class="icon-show" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="icon-hide" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.94 17.94A10.94 10.94 0 0112 20c-7 0-11-8-11-8a20.3 20.3 0 015.06-5.94M9.9 4.24A10.94 10.94 0 0112 4c7 0 11 8 11 8a20.3 20.3 0 01-3.22 4.44M14.12 14.12a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
                @error('current_password')
                    <span class="v-error">{{ $message }}</span>
                @enderror
            </div>

            {{-- New Password --}}
            <div class="v-form-group">
                <label class="v-label" for="new_password">New Password *</label>

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

            {{-- Confirm Password --}}
            <div class="v-form-group">
                <label class="v-label" for="password_confirmation">Confirm New Password *</label>

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

            {{-- Info --}}
            <div class="v-alert v-alert-info">
                You will remain logged in after changing your password.
            </div>

            {{-- Actions --}}
            <div class="password-actions">
                <button type="submit" class="v-btn v-btn-primary">Update Password</button>
                <a href="{{ route('voter.ballot', $election) }}" class="v-btn v-btn-secondary">Cancel</a>
            </div>

        </form>
    </div>
</div>
@endsection
