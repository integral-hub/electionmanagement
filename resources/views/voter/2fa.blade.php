@extends('layouts.voter')
@section('title','Two-Factor Authentication')

@push('styles')
    @vite('resources/css/voter-otp.css')
@endpush

@push('scripts')
    @vite('resources/js/voter-otp.js')
@endpush

@section('content')
<div class="v-card">
    <div class="auth-icon-wrap">
        <div class="auth-icon">
            <svg fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24" width="26" height="26">
                <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <h1 class="auth-title">Two-Factor Authentication</h1>
        <p class="auth-subtitle">
            A 6-digit code has been sent to your email.<br>
            Enter it below to complete your login.
        </p>
    </div>

    @if(session('success'))
    <div class="v-alert v-alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->has('otp'))
    <div class="v-alert v-alert-error">{{ $errors->first('otp') }}</div>
    @endif

    <form action="{{ route('voter.2fa.submit', $election) }}" method="POST">
        @csrf
        <input type="hidden" name="otp" id="otpHidden">

        <div class="otp-wrap" aria-label="Enter 6-digit code">
            @for($i = 0; $i < 6; $i++)
            <input type="tel" inputmode="numeric" maxlength="1"
                   class="otp-box" autocomplete="one-time-code"
                   {{ $i === 0 ? 'autofocus' : '' }}>
            @endfor
        </div>

        <button type="submit" class="v-btn v-btn-primary">
            Confirm &amp; Sign In
            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" width="15" height="15">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </button>
    </form>

    <div class="otp-note">
        <div class="otp-note-inner">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="15" height="15">
                <circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/>
            </svg>
            <span>The code expires in  <strong>{{ config('otp.expiry') }} minutes</strong> and can only be used once.</span>
        </div>
    </div>

    <div class="otp-resend-wrap">
        <form action="{{ route('voter.resend-otp', $election) }}?type=2fa" method="POST" style="display:inline;">
            @csrf
            <button type="submit" id="resendBtn" disabled class="otp-resend-btn">
                Resend code
            </button>
        </form>
        <span id="cdText" class="otp-resend-countdown">Resend in 60s</span>
    </div>

    <div class="otp-back-wrap">
        <a href="{{ route('voter.login', $election) }}" class="auth-back-link">← Start over</a>
    </div>
</div>
@endsection
