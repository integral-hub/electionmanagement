@extends('layouts.admin')
@section('page-title', 'Election Settings')

@section('topbar-actions')
    <a href="{{ route('admin.elections.show', $election) }}" class="btn btn-secondary btn-sm">← Back to Election</a>
@endsection

@section('content')
<div style="max-width:680px;">
    <div class="card">
        <div style="margin-bottom:24px;">
            <h2 style="font-size:18px;font-weight:700;">Settings — {{ $election->name }}</h2>
            <p style="font-size:14px;color:var(--ink-3);margin-top:4px;">Configure registration, voting, and security options.</p>
        </div>

        <form action="{{ route('admin.elections.settings.update', $election) }}" method="POST" class="form-section">
            @csrf @method('PUT')

            {{-- Registration Mode --}}
            <div>
                <div style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--ink-3);margin-bottom:12px;">Registration</div>
                <div class="form-group">
                    <label class="form-label">Registration Mode</label>
                    <div style="display:flex;gap:10px;flex-wrap:wrap;">
                        @foreach(['open'=>['Open – anyone can register','#059669'],'closed'=>['Closed – no new registrations','#dc2626']] as $mode => [$desc,$color])
                        <label style="flex:1;min-width:160px;border:2px solid {{ old('registration_mode',$election->setting?->registration_mode) === $mode ? $color : 'var(--border)' }};border-radius:10px;padding:14px;cursor:pointer;transition:border .15s;" class="radio-card">
                            <input type="radio" name="registration_mode" value="{{ $mode }}" {{ old('registration_mode',$election->setting?->registration_mode) === $mode ? 'checked' : '' }} style="display:none;" onchange="updateRadioCards(this)">
                            <div style="font-weight:600;font-size:14px;margin-bottom:2px;">{{ ucfirst($mode) }}</div>
                            <div style="font-size:12px;color:var(--ink-3);">{{ $desc }}</div>
                        </label>
                        @endforeach
                    </div>
                    @error('registration_mode') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>
            {{-- Vote before validation --}}
    <div style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--ink-3);margin-bottom:12px;">
        Voters Validation Type
    </div>
            <div>
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:14px;">
                    <input type="checkbox" name="vote_before_validation" value="1" {{ $election->setting?->vote_before_validation ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--accent);">
                    Allow voters to cast votes before verification is complete
                </label>
                <p style="font-size:12px;color:var(--ink-3);margin-top:4px;margin-left:26px;">Voting will be held pending verification approval.</p>
            </div>

{{-- Voter Login Field --}}
<div>
    <div style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--ink-3);margin-bottom:12px;">
        Voter Login Field
    </div>
        <div style="margin-top:12px;padding:12px 14px;border:1px solid var(--border);border-radius:8px;background:var(--surface);font-size:13px;color:var(--ink-3);line-height:1.4;">
            <strong style="color:var(--danger);">IMPORTANT:</strong>
            You can customize voters login or use default <strong>Email & Password</strong>
        </div><br>

    <p style="font-size:12px;color:var(--ink-3);margin-bottom:10px;">
        You can select up to 3 fields voters can use to log in.
    </p>

    @php
        $loginOptions = [
            'email' => 'email address',
            'phone' => 'phone number',
            'password' => 'password'
        ];

        $uniqueFields = collect($election->registrationField?->fields ?? [])
            ->filter(fn ($f) => $f['unique_field'] ?? false)
            ->pluck('label', 'field_name');

       $currentLoginFields = old('login_fields');

        $currentLoginFields = old('login_fields', $election->setting?->login_fields ?? []);
        $currentLoginFields = is_array($currentLoginFields) ? array_keys($currentLoginFields) : [];
    @endphp

    {{-- GRID --}}
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;">

        {{-- Default fields --}}
        @foreach($loginOptions as $value => $label)
        <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;font-size:14px;padding:10px;border:1px solid var(--border);border-radius:8px;">
            <input type="checkbox"
                   name="login_fields[]"
                   value="{{ $value . ',' . $label }}"
                   {{ in_array($value, $currentLoginFields) ? 'checked' : '' }}
                   class="login-checkbox"
                   style="width:15px;height:15px;accent-color:var(--accent);margin-top:2px;">
            <div>
                <div style="font-weight:600;">{{ ucfirst($value) }}</div>
                <div style="font-size:12px;color:var(--ink-3);">{{ $label }}</div>
            </div>
        </label>
        @endforeach

        {{-- Unique fields --}}
        @foreach($uniqueFields as $fieldName => $fieldLabel)
        <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;font-size:14px;padding:10px;border:1px solid var(--border);border-radius:8px;">
            <input type="checkbox"
                   name="login_fields[]"
                   value="{{ $fieldName . ',' . $fieldLabel }}"
                   {{ in_array($fieldName, $currentLoginFields) ? 'checked' : '' }}
                   class="login-checkbox"
                   style="width:15px;height:15px;accent-color:var(--accent);margin-top:2px;">
            <div>
                <div style="font-weight:600;">{{ $fieldLabel }}</div>
                <div style="font-size:12px;color:var(--ink-3);">
                    Custom unique field
                </div>
            </div>
        </label>
        @endforeach
    </div>

    {{-- WARNING MESSAGE --}}
    <p id="loginLimitMsg"
       style="font-size:12px;color:green;margin-top:8px;display:none;">
        <strong>You have reach maximum (3) login fields selection.</strong>
    </p>
    @error('login_fields')
        <span class="form-error">{{ $message }}</span>
    @enderror
</div>

            {{-- Voting Window --}}
            @if(in_array($progress, [60, 80], true))
            <div>
                <div style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--ink-3);margin-bottom:12px;">Voting Window</div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Start Date & Time</label>
                        <input type="datetime-local" name="voting_start" class="form-input" value="{{ old('voting_start', $election->setting?->voting_start?->format('Y-m-d\TH:i')) }}">
                        @error('voting_start') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">End Date & Time</label>
                        <input type="datetime-local" name="voting_end" class="form-input" value="{{ old('voting_end', $election->setting?->voting_end?->format('Y-m-d\TH:i')) }}">
                        @error('voting_end') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            @endif

            {{-- Verification 
            <div>
                <div style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--ink-3);margin-bottom:12px;">Voter Verification</div>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    @foreach(['email'=>'Require email verification','phone'=>'Require phone verification','image_compare'=>'Enable face/image comparison'] as $key => $label)
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:14px;">
                        <input type="checkbox" name="voters_verification_requirement[{{ $key }}]" value="1" {{ ($election->setting?->voters_verification_requirement[$key] ?? false) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--accent);">
                        {{ $label }}
                    </label>
                    @endforeach
                </div>
            </div>
            --}}

            {{-- 2FA --}}
            <div>
                <div style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--ink-3);margin-bottom:12px;">Two-Factor Auth</div>
                <div style="display:flex;gap:16px;align-items:center;flex-wrap:wrap;">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;">
                        <input type="checkbox" name="voters_require_2fa" value="1" {{ $election->setting?->voters_require_2fa ? 'checked' : '' }} id="twoFaToggle" onchange="document.getElementById('twoFaType').style.display=this.checked?'block':'none'" style="width:16px;height:16px;accent-color:var(--accent);">
                        Require 2FA for voters
                    </label>
                    <div id="twoFaType" style="{{ $election->setting?->voters_require_2fa ? '' : 'display:none;' }}">
                        <select name="voters_2fa_type" class="form-select" style="width:auto;">
                            @foreach(['email'=>'Email'] as $val=>$lbl)
                            <option value="{{ $val }}" {{ $election->setting?->voters_2fa_type === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:10px;padding-top:8px;border-top:1px solid var(--border);">
               @can('update', $election->setting ?? $election)  
                <button type="submit" class="btn btn-primary">Save Settings</button>
                <a href="{{ route('admin.elections.show', $election) }}" class="btn btn-secondary">Cancel</a>
               @endcan
            </div>
        </form>
    </div>
</div>
<script>
function updateRadioCards(input) {
    document.querySelectorAll('.radio-card').forEach(card => {
        const radio = card.querySelector('input[type=radio]');
        const colors = { open:'#059669', closed:'#dc2626' };
        card.style.borderColor = radio.checked ? (colors[radio.value] || 'var(--accent)') : 'var(--border)';
    });
}
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('.login-checkbox');
    const max = 3;

    const msg = document.getElementById('loginLimitMsg');

    function updateUI() {
        const checked = document.querySelectorAll('.login-checkbox:checked');

        // show/hide warning
        msg.style.display = checked.length >= max ? 'block' : 'none';

        // disable unchecked when limit reached
        checkboxes.forEach(cb => {
            cb.disabled = (!cb.checked && checked.length >= max);
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateUI);
    });

    updateUI();
});
</script>
@endsection
