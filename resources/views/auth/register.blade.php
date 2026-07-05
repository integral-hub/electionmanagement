<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Organisation — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DM Sans', sans-serif; background: #f1f5f9; min-height: 100vh; display: flex; align-items: flex-start; justify-content: center; padding: 32px 16px; }
        .wrap { width: 100%; max-width: 500px; }
        .brand { text-align: center; margin-bottom: 28px; }
        .logo { width: 50px; height: 50px; background: #4f46e5; border-radius: 14px; display: grid; place-items: center; margin: 0 auto 12px; }
        .logo svg { width: 26px; height: 26px; fill: #fff; }
        h1 { font-size: 22px; font-weight: 700; color: #0f172a; }
        .sub { font-size: 14px; color: #94a3b8; margin-top: 4px; }

        .card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,.05); }
        .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #94a3b8; margin-bottom: 14px; padding-bottom: 8px; border-bottom: 1px solid #f1f5f9; }
        .form-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 16px; }
        .form-label { font-size: 13px; font-weight: 600; color: #0f172a; }
        .form-input { width: 100%; padding: 10px 13px; border: 1.5px solid #e2e8f0; border-radius: 9px; font-size: 14px; font-family: inherit; outline: none; transition: border .15s; }
        .form-input:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,.1); }
        .form-input.error { border-color: #dc2626; }
        .form-error { font-size: 12px; color: #dc2626; }

        .btn { width: 100%; padding: 12px; border: none; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer; font-family: inherit; transition: background .15s; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn-primary { background: #4f46e5; color: #fff; }
        .btn-primary:hover { background: #4338ca; }

        .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 16px; border-radius: 10px; font-size: 14px; margin-bottom: 20px; }
        .footer-note { text-align: center; margin-top: 20px; font-size: 13px; color: #94a3b8; }
        .footer-note a { color: #4f46e5; text-decoration: none; font-weight: 500; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="brand">
        <div class="logo">
            <svg viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h1>Register Your Organisation</h1>
        <div class="sub">Set up your election management account</div>
    </div>

    <div class="card">
        @if($errors->any())
            <div class="alert-error">
                <ul style="list-style:none;display:flex;flex-direction:column;gap:3px;">
                    @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.submit') }}" method="POST">
            @csrf

            {{-- Organisation --}}
            <div class="section-title">Organisation Details</div>

            <div class="form-group">
                <label class="form-label">Organisation Name *</label>
                <input type="text" name="org_name" class="form-input {{ $errors->has('org_name')?'error':'' }}" value="{{ old('org_name') }}" placeholder="e.g. Acme University" required>
                @error('org_name') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Access Token *</label>
                <input type="text" name="access_token" class="form-input {{ $errors->has('access_token')?'error':'' }}" value="{{ old('access_token') }}" placeholder="Your issued access token" required>
                @error('access_token') <span class="form-error">{{ $message }}</span> @enderror
                <span style="font-size:12px;color:#94a3b8;">Contact support to obtain an access token.</span>
            </div>

            {{-- Admin user --}}
            <div class="section-title" style="margin-top:20px;">Administrator Account</div>

            <div class="form-group">
                <label class="form-label">Full Name *</label>
                <input type="text" name="name" class="form-input {{ $errors->has('name')?'error':'' }}" value="{{ old('name') }}" placeholder="Jane Doe" required>
                @error('name') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Email Address *</label>
                <input type="email" name="email" class="form-input {{ $errors->has('email')?'error':'' }}" value="{{ old('email') }}" placeholder="admin@example.com" required>
                @error('email') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Password *</label>
                <input type="password" name="password" class="form-input {{ $errors->has('password')?'error':'' }}" placeholder="Min. 8 characters" required autocomplete="new-password">
                @error('password') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Confirm Password *</label>
                <input type="password" name="password_confirmation" class="form-input" placeholder="Repeat password" required>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top:8px;">
                Create Account
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" width="16" height="16"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </button>
        </form>
    </div>

    <div class="footer-note">Already have an account? <a href="{{ route('login') }}">Sign in</a></div>
</div>
</body>
</html>
