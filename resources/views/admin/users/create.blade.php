@extends('layouts.admin')
@php $isSelf = $isSelf ?? false; @endphp
@section('page-title', $isSelf ? 'Update Profile' : (isset($user) ? 'Edit Staff Member' : 'Invite Staff'))

@section('topbar-actions')
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">← Back</a>
@endsection

@section('content')
<div style="max-width:520px;">
    <div class="card">
        <div style="margin-bottom:24px;">
            <h2 style="font-size:18px;font-weight:700;">{{ $isSelf ? 'Update Profile' : (isset($user) ? 'Edit Staff Member' : 'Invite Staff Member') }}</h2>
            <p style="font-size:14px;color:var(--ink-3);margin-top:4px;">
                {{ $isSelf ? 'Update your profile' : (isset($user) ? 'Update staff details and role.' : 'They\'ll receive an email with login credentials.') }}
            </p>
        </div>

        <form action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST" class="form-section">
            @csrf
            @if(isset($user)) @method('PUT') @endif

            <div class="form-group">
                <label class="form-label" for="name">Full Name <span style="color:var(--danger)">*</span></label>
                <input type="text" id="name" name="name" class="form-input {{ $errors->has('name') ? 'error' : '' }}" value="{{ old('name', $user?->name ?? '') }}" placeholder="Jane Doe" required>
                @error('name') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email Address <span style="color:var(--danger)">*</span></label>
                <input type="email" id="email" name="email" class="form-input {{ $errors->has('email') ? 'error' : '' }}" value="{{ old('email', $user?->email ?? '') }}" placeholder="jane@example.com"
                   {{ $isSelf ? 'readonly style=background:#f1f5f9;cursor:not-allowed;' : '' }} required>
                @error('email') <span class="form-error">{{ $message }}</span> @enderror
            </div>

           @if(!$isSelf) 
            <div class="form-group">
                <label class="form-label" for="role">Role <span style="color:var(--danger)">*</span></label>
                <select id="role" name="role" class="form-select {{ $errors->has('role') ? 'error' : '' }}" required>
                    <option value="">Select a role…</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ old('role', $user?->roles?->first()?->name ?? '') === $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role') <span class="form-error">{{ $message }}</span> @enderror
            </div>
            @endif

            @if(!isset($user))
            <div class="alert alert-info" style="margin:0;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
                A secure temporary password will be generated and emailed to the new user.
            </div>
            @endif

            <div style="display:flex;gap:10px;padding-top:8px;">
                <button type="submit" class="btn btn-primary">
                    {{ $isSelf ? 'Update Profile' : (isset($user) ? 'Update Member' : 'Send Invitation') }}
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
