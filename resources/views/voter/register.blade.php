@extends('layouts.voter')
@section('title', 'Register to Vote')

@push('styles')
    @vite('resources/css/voter-register.css')
@endpush

@section('content')
<div class="v-card">
    <div class="register-icon-wrap">
        <div class="register-icon">
            <svg fill="none" stroke="#059669" stroke-width="2" viewBox="0 0 24 24" width="28" height="28">
                <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/>
            </svg>
        </div>
        <h1 class="register-title">Register to Vote</h1>
        <p class="register-subtitle">Fill in your details to register for <strong>{{ $election->name }}</strong>.</p>
    </div>

    @if($errors->any())
        <div class="v-alert v-alert-error">
            <ul class="register-error-list">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('voter.register.submit', $election) }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Dynamic registration fields --}}
        @if(!empty($fields))
        <div class="register-fields-section">
            <p class="register-fields-heading">Registration Information</p>

            @foreach($fields as $field)
            <div class="v-form-group">
                <label class="v-label" for="rf_{{ $field['field_name'] }}">
                    {{ $field['label'] }}
                    @if($field['required'] ?? false) <span class="required-mark">*</span> @endif
                </label>
                @if($field['description'] ?? null)
                    <span class="v-hint">{{ $field['description'] }}</span>
                @endif

                @switch($field['field_type'])
                    @case('textarea')
                        <textarea id="rf_{{ $field['field_name'] }}" name="{{ $field['field_name'] }}" class="v-textarea" {{ ($field['required']??false) ? 'required' : '' }}
                        maxlength="{{ $field['max_input'] ?? '' }}">{{ old($field['field_name']) }}</textarea>
                        @break
                    @case('select')
                        <select id="rf_{{ $field['field_name'] }}" name="{{ $field['field_name'] }}" class="v-select" {{ ($field['required']??false) ? 'required' : '' }}>
                            <option value="">Select…</option>
                            @foreach($field['options'] ?? [] as $opt)
                            <option value="{{ $opt }}" {{ old($field['field_name']) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                        @break
                    @case('radio')
                        <div class="register-radio-group">
                            @foreach($field['options'] ?? [] as $opt)
                            <label class="register-radio-label">
                                <input type="radio" name="{{ $field['field_name'] }}" value="{{ $opt }}" {{ old($field['field_name']) === $opt ? 'checked' : '' }} {{ ($field['required']??false) ? 'required' : '' }}>
                                {{ $opt }}
                            </label>
                            @endforeach
                        </div>
                        @break
                    @case('checkbox')
                        <label class="register-checkbox-label">
                            <input type="checkbox" name="{{ $field['field_name'] }}" value="1" {{ old($field['field_name']) ? 'checked' : '' }}>
                            {{ $field['label'] }}
                        </label>
                        @break
                    @case('date')
                        <input type="date" id="rf_{{ $field['field_name'] }}" name="{{ $field['field_name'] }}" class="v-input" value="{{ old($field['field_name']) }}" {{ ($field['required']??false) ? 'required' : '' }}>
                        @break
                    @case('file')
                        <input type="file" id="rf_{{ $field['field_name'] }}" name="{{ $field['field_name'] }}" class="v-input register-file-input" accept=".pdf,.doc,.docx,image/*" {{ ($field['required']??false) ? 'required' : '' }}>
                        @break
                    @default
                        <input type="text" id="rf_{{ $field['field_name'] }}" name="{{ $field['field_name'] }}" class="v-input" value="{{ old($field['field_name']) }}" placeholder="{{ $field['label'] }}" {{ ($field['required']??false) ? 'required' : '' }}
                        maxlength="{{ $field['max_input'] ?? '' }}">
                @endswitch

                @error($field['field_name']) <span class="v-error">{{ $message }}</span> @enderror
            </div>
            @endforeach
        </div>
        @endif

        {{-- Base fields --}}
        <div class="v-form-group">
            <label class="v-label" for="email">Email Address <span class="required-mark">*</span></label>
            <input type="email" id="email" name="email" class="v-input" value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" required>
            @error('email') <span class="v-error">{{ $message }}</span> @enderror
        </div>

        <div class="v-form-group">
            <label class="v-label" for="phone">Phone Number <span class="v-hint">(optional)</span></label>
            <input type="tel" id="phone" name="phone" class="v-input" value="{{ old('phone') }}" placeholder="+234 7xxx xxxxxx">
            @error('phone') <span class="v-error">{{ $message }}</span> @enderror
        </div>

        <div class="v-form-group">
            <label class="v-label" for="password">Password <span class="required-mark">*</span></label>
            <input type="password" id="password" name="password" class="v-input" placeholder="Min. 8 characters" autocomplete="new-password" required>
            @error('password') <span class="v-error">{{ $message }}</span> @enderror
        </div>

        <div class="v-form-group">
            <label class="v-label" for="password_confirmation">Confirm Password <span class="required-mark">*</span></label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="v-input" placeholder="Repeat password" required>
        </div>

        <button type="submit" class="v-btn v-btn-primary">
            Register &amp; Continue
            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" width="16" height="16"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </button>
    </form>

    <div class="v-divider">already registered?</div>
    <a href="{{ route('voter.login', $election) }}" class="v-btn v-btn-secondary">Sign In Instead</a>
</div>
@endsection
