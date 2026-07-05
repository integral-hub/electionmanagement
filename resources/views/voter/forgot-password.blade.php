@extends('layouts.voter')
@section('title', 'Forgot Password')

@section('content')
<div class="auth-icon-wrap">
    <div class="auth-icon">
        <svg fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24" width="28" height="28">
            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>
    <h1 class="auth-title">Forgot Password</h1>
    <p class="auth-subtitle">Enter your email to receive a reset link</p>
</div>

<div class="v-card">

    @if(session('status'))
        <div class="v-alert v-alert-success">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="v-alert v-alert-error">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('voter.password.email', $election) }}" method="POST">
        @csrf

        <div class="v-form-group">
            <label class="v-label" for="email">Email Address</label>

            <input type="email"
                   id="email"
                   name="email"
                   class="v-input"
                   value="{{ old('email') }}"
                   placeholder="voter@example.com"
                   required autofocus>

            @error('email')
                <span class="v-error">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="v-btn v-btn-primary">
            Send Reset Link
            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" width="15" height="15">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </button>

    </form>
</div>

<div class="auth-footer">
    <a href="{{ route('voter.login', $election) }}" class="auth-back-link">← Back to login</a>
</div>
@endsection
