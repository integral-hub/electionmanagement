<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Forgot Password — {{ config('app.name') }}</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite(['resources/css/app.css','resources/js/app.js'])
<style>
*{box-sizing:border-box;margin:0;padding:0}body{font-family:'DM Sans',sans-serif;background:#f1f5f9;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:16px}.wrap{width:100%;max-width:400px}.brand{text-align:center;margin-bottom:26px}.logo{width:48px;height:48px;background:#4f46e5;border-radius:13px;display:grid;place-items:center;margin:0 auto 11px}.logo svg{width:26px;height:26px;fill:#fff}h1{font-size:21px;font-weight:700;color:#0f172a}.sub{font-size:13.5px;color:#94a3b8;margin-top:3px}.card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:28px;box-shadow:0 2px 8px rgba(0,0,0,.05)}.fg{display:flex;flex-direction:column;gap:5px;margin-bottom:16px}.fl{font-size:12.5px;font-weight:600;color:#0f172a}.fi{width:100%;padding:10px 13px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;transition:border .14s}.fi:focus{border-color:#4f46e5;box-shadow:0 0 0 3px rgba(79,70,229,.1)}.btn{width:100%;padding:12px;border:none;border-radius:8px;font-size:14.5px;font-weight:600;cursor:pointer;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:7px;background:#4f46e5;color:#fff;transition:background .14s}.btn:hover{background:#4338ca}.as{background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;padding:10px 13px;border-radius:8px;font-size:13px;margin-bottom:16px}.ae{background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:10px 13px;border-radius:8px;font-size:13px;margin-bottom:16px}.fe{font-size:11.5px;color:#dc2626}.fn{text-align:center;margin-top:18px;font-size:12.5px;color:#94a3b8}.fn a{color:#4f46e5;text-decoration:none;font-weight:500}
</style>
</head>
<body>
<div class="wrap">
    <div class="brand">
        <div class="logo"><svg viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        <h1>Forgot Password</h1>
        <div class="sub">Enter your email to receive a reset link</div>
    </div>
    <div class="card">
        @if(session('status'))<div class="as">{{ session('status') }}</div>@endif
        @if($errors->any())<div class="ae">{{ $errors->first() }}</div>@endif
        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="fg">
                <label class="fl">Email Address</label>
                <input type="email" name="email" class="fi {{ $errors->has('email')?'err':'' }}" value="{{ old('email') }}" placeholder="admin@example.com" required autofocus>
                @error('email')<span class="fe">{{ $message }}</span>@enderror
            </div>
            <button type="submit" class="btn">Send Reset Link <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" width="15" height="15"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button>
        </form>
    </div>
    <div class="fn"><a href="{{ route('login') }}">← Back to login</a></div>
</div>
</body>
</html>
