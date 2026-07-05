<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .login-wrap { width: 100%; max-width: 420px; }
        .login-brand { text-align: center; margin-bottom: 32px; }
        .login-logo { width: 52px; height: 52px; background: #4f46e5; border-radius: 14px; display: grid; place-items: center; margin: 0 auto 14px; }
        .login-logo svg { width: 28px; height: 28px; fill: #fff; }
        .login-title { font-size: 22px; font-weight: 700; color: #0f172a; }
        .login-sub { font-size: 14px; color: #94a3b8; margin-top: 4px; }

        .card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,.05); }

        .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 18px; }
        .form-label { font-size: 13px; font-weight: 600; color: #0f172a; }
        .form-input { width: 100%; padding: 11px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-size: 15px; font-family: inherit; outline: none; transition: border .15s, box-shadow .15s; }
        .form-input:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,.12); }
        .form-input.error { border-color: #dc2626; }
        .form-error { font-size: 12px; color: #dc2626; }

        .btn-primary { width: 100%; padding: 13px; background: #4f46e5; color: #fff; border: none; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer; font-family: inherit; transition: background .15s; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn-primary:hover { background: #4338ca; }

        .alert { padding: 12px 16px; border-radius: 10px; font-size: 14px; margin-bottom: 20px; }
        .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }

        .footer-note { text-align: center; margin-top: 24px; font-size: 13px; color: #94a3b8; }
        .footer-note a { color: #4f46e5; text-decoration: none; font-weight: 500; }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="login-brand">
        <div class="login-logo">
            <svg viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="login-title">{{ config('app.name') }}</div>
        <div class="login-sub">Sign in to the admin panel</div>
    </div>

    <div class="card">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any() || session('error'))
            <div class="alert alert-error">{{ $errors->first() ?: session('error') }}</div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-input {{ $errors->has('email') ? 'error' : '' }}" value="{{ old('email') }}" placeholder="admin@example.com" required autofocus autocomplete="email">
                @error('email') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div style="position:relative;">
                    <input type="password" id="password" name="password" class="form-input {{ $errors->has('password') ? 'error' : '' }}" placeholder="Your password" required autocomplete="current-password" style="padding-right:44px;">
                    <button type="button" onclick="this.previousElementSibling.type = this.previousElementSibling.type === 'password' ? 'text' : 'password'" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </div>
                @error('password') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;">
                <label style="display:flex;align-items:center;gap:7px;font-size:13px;cursor:pointer;">
                    <input type="checkbox" name="remember" style="accent-color:#4f46e5;width:15px;height:15px;">
                    Remember me
                </label>
                <a href="{{ route('password.request') }}" style="font-size:13px;color:#4f46e5;text-decoration:none">Forgot password?</a>
            </div>

            <button type="submit" class="btn-primary">
                Sign In
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" width="16" height="16"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </button>
        </form>
    </div>

    <div class="footer-note">
        New organisation? <a href="{{ route('register') }}">Register here</a>
    </div>
</div>
</body>
</html>
