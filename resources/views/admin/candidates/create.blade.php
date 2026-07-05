{{-- ============================================================
     admin/candidates/create.blade.php
     ============================================================ --}}
@extends('layouts.admin')
@section('page-title', isset($candidate) ? 'Edit Candidate' : 'Add Candidate')
@section('topbar-actions')
    <a href="{{ route('admin.elections.show', $election) }}" class="btn btn-secondary btn-sm">← Election</a>
@endsection
@section('content')
<div style="max-width:560px;">
    <div class="card">
        <div style="margin-bottom:24px;">
            <h2 style="font-size:18px;font-weight:700;">{{ isset($candidate) ? 'Edit Candidate' : 'Add Candidate' }}</h2>
            <p style="font-size:14px;color:var(--ink-3);">For election: <strong>{{ $election->name }}</strong></p>
        </div>
        <form action="{{ isset($candidate) ? route('admin.elections.candidates.update', [$election,$candidate]) : route('admin.elections.candidates.store', $election) }}" method="POST" enctype="multipart/form-data" class="form-section">
            @csrf
            @if(isset($candidate)) @method('PUT') @endif

            <div class="form-group">
                <label class="form-label">Position *</label>
                <select name="position_id" class="form-select {{ $errors->has('position_id')?'error':'' }}" required>
                    <option value="">Select position…</option>
                    @foreach($positions as $pos)
                    <option value="{{ $pos->id }}" {{ old('position_id', $candidate->position_id ?? '') == $pos->id ? 'selected' : '' }}>{{ $pos->title }}</option>
                    @endforeach
                </select>
                @error('position_id') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Full Name *</label>
                <input type="text" name="name" class="form-input {{ $errors->has('name')?'error':'' }}" value="{{ old('name',$candidate->name??'') }}" required>
                @error('name') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Photo</label>
        @if(isset($candidate) && data_get($candidate, 'photo.url'))
            <img src="{{ data_get($candidate, 'photo.url') }}"
                alt="{{ $candidate->name }}">
        @endif
                <input type="file" name="photo" class="form-input" accept="image/*" style="padding:8px;">
                @error('photo') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Bio</label>
                <textarea name="bio" class="form-textarea" placeholder="Short biography…">{{ old('bio',$candidate->bio??'') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Manifesto / Tagline</label>
                <input type="text" name="manifesto" class="form-input" value="{{ old('manifesto',$candidate->manifesto??'') }}" placeholder="Their key campaign message">
            </div>

            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    @foreach($statuses as $s)
                    <option value="{{ $s }}" {{ old('status',$candidate->status ?? $statuses[0]) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit" class="btn btn-primary">{{ isset($candidate) ? 'Update' : 'Add Candidate' }}</button>
                <a href="{{ route('admin.elections.show', $election) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
