<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Reset Password — {{ config('app.name') }}</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite(['resources/css/app.css','resources/js/app.js'])
<style>
*{box-sizing:border-box;margin:0;padding:0}body{font-family:'DM Sans',sans-serif;background:#f1f5f9;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:16px}.wrap{width:100%;max-width:400px}.brand{text-align:center;margin-bottom:26px}.logo{width:48px;height:48px;background:#4f46e5;border-radius:13px;display:grid;place-items:center;margin:0 auto 11px}.logo svg{width:26px;height:26px;fill:#fff}h1{font-size:21px;font-weight:700;color:#0f172a}.sub{font-size:13.5px;color:#94a3b8;margin-top:3px}.card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:28px;box-shadow:0 2px 8px rgba(0,0,0,.05)}.fg{display:flex;flex-direction:column;gap:5px;margin-bottom:16px}.fl{font-size:12.5px;font-weight:600;color:#0f172a}.fh{font-size:11px;color:#94a3b8}.pw{position:relative}.pw .tog{position:absolute;right:11px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8}.fi{width:100%;padding:10px 13px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;transition:border .14s}.fi:focus{border-color:#4f46e5;box-shadow:0 0 0 3px rgba(79,70,229,.1)}.fe{font-size:11.5px;color:#dc2626}.sb{height:4px;border-radius:4px;background:#e2e8f0;overflow:hidden;margin-top:5px}.sf{height:100%;border-radius:4px;transition:width .3s,background .3s;width:0}.btn{width:100%;padding:12px;border:none;border-radius:8px;font-size:14.5px;font-weight:600;cursor:pointer;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:7px;background:#4f46e5;color:#fff;transition:background .14s}.btn:hover{background:#4338ca}.ae{background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:10px 13px;border-radius:8px;font-size:13px;margin-bottom:16px}.fn{text-align:center;margin-top:18px;font-size:12.5px;color:#94a3b8}.fn a{color:#4f46e5;text-decoration:none;font-weight:500}
</style>
</head>
<body>
<div class="wrap">
    <div class="brand">
        <div class="logo"><svg viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        <h1>Set New Password</h1>
        <div class="sub">Choose a strong password for your account</div>
    </div>
    <div class="card">
        @if($errors->any())<div class="ae">{{ $errors->first() }}</div>@endif
        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="fg">
                <label class="fl">Email Address</label>
                <input type="email" name="email" class="fi {{ $errors->has('email')?'err':'' }}" value="{{ old('email',$email) }}" required autocomplete="email" readonly>
                @error('email')<span class="fe">{{ $message }}</span>@enderror
            </div>
            <div class="fg">
                <label class="fl">New Password</label>
                <div class="pw">
                    <input type="password" id="pw1" name="password" class="fi {{ $errors->has('password')?'err':'' }}" placeholder="Min. 8 chars, upper, lower &amp; number" required autocomplete="new-password" oninput="chk(this.value)" style="padding-right:42px">
                    <button type="button" class="tog" onclick="t('pw1')"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="17" height="17"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                </div>
                <div class="sb"><div class="sf" id="sf"></div></div>
                <span class="fh" id="sl"></span>
                @error('password')<span class="fe">{{ $message }}</span>@enderror
            </div>
            <div class="fg">
                <label class="fl">Confirm Password</label>
                <div class="pw">
                    <input type="password" id="pw2" name="password_confirmation" class="fi" placeholder="Repeat new password" required autocomplete="new-password" style="padding-right:42px">
                    <button type="button" class="tog" onclick="t('pw2')"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="17" height="17"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                </div>
            </div>
            <button type="submit" class="btn">Reset Password <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" width="15" height="15"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button>
        </form>
    </div>
    <div class="fn"><a href="{{ route('login') }}">← Back to login</a></div>
</div>
<script>
function t(id){const e=document.getElementById(id);e.type=e.type==='password'?'text':'password'}
function chk(v){const f=document.getElementById('sf'),l=document.getElementById('sl');let s=0;if(v.length>=8)s++;if(/[A-Z]/.test(v))s++;if(/[0-9]/.test(v))s++;if(/[^A-Za-z0-9]/.test(v))s++;const m={0:['0%','#e2e8f0',''],1:['25%','#dc2626','Weak'],2:['50%','#d97706','Fair'],3:['75%','#2563eb','Good'],4:['100%','#059669','Strong']};f.style.width=m[s][0];f.style.background=m[s][1];l.textContent=m[s][2]}
</script>
</body>
</html>
