@extends('layouts.voter')
@section('title','Verify Your Email')

@push('styles')
    @vite('resources/css/voter-otp.css')
@endpush

@push('scripts')
    @vite('resources/js/voter-otp.js')
@endpush

@section('content')
<div class="v-card">
    <div class="auth-icon-wrap">
        <div class="auth-icon is-success">
            <svg fill="none" stroke="#059669" stroke-width="2" viewBox="0 0 24 24" width="26" height="26">
                <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <h1 class="auth-title">Verify Your Email</h1>
        <p class="auth-subtitle">
            We sent a 6-digit code to your email address.<br>
            Enter it below to activate your account.
        </p>
    </div>

    @if(session('success'))
    <div class="v-alert v-alert-success">{{ session('success') }}</div>
    @endif
    @if(session('info'))
    <div class="v-alert v-alert-info">{{ session('info') }}</div>
    @endif
    @if($errors->has('otp'))
    <div class="v-alert v-alert-error">{{ $errors->first('otp') }}</div>
    @endif

    <form action="{{ route('voter.verify-email.submit', $election) }}" method="POST">
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
            Verify Email
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
        <form action="{{ route('voter.resend-otp', $election) }}?type=verification" method="POST" style="display:inline;">
            @csrf
            <button type="submit" id="resendBtn" disabled class="otp-resend-btn">
                Resend code
            </button>
        </form>
        <span id="cdText" class="otp-resend-countdown">Resend in 60s</span>
    </div>

    <div class="otp-back-wrap">
        <a href="{{ route('voter.login', $election) }}" class="auth-back-link">← Back to login</a>
    </div>
</div>
@endsection
