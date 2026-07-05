@extends('layouts.admin')
@section('page-title', isset($position) ? 'Edit Position' : 'Add Position')
@section('topbar-actions')
    <a href="{{ route('admin.elections.show', $election) }}" class="btn btn-secondary btn-sm">← Election</a>
@endsection
@section('content')
<div style="max-width:520px;">
    <div class="card">
        <div style="margin-bottom:24px;">
            <h2 style="font-size:18px;font-weight:700;">{{ isset($position) ? 'Edit Position' : 'Add Position' }}</h2>
            <p style="font-size:14px;color:var(--ink-3);">Election: <strong>{{ $election->name }}</strong></p>
        </div>

        <form action="{{ isset($position) ? route('admin.elections.positions.update',[$election,$position]) : route('admin.elections.positions.store',$election) }}" method="POST" class="form-section">
            @csrf
            @if(isset($position)) @method('PUT') @endif

            <div class="form-group">
                <label class="form-label" for="title">Position Title *</label>
                <input type="text" id="title" name="title" class="form-input {{ $errors->has('title')?'error':'' }}" value="{{ old('title',$position->title??'') }}" placeholder="e.g. President, Secretary General" required>
                @error('title') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description <span class="form-hint">(optional)</span></label>
                <textarea id="description" name="description" class="form-textarea" placeholder="Brief description of this role…">{{ old('description',$position->description??'') }}</textarea>
            </div>

            <div style="display:flex;gap:10px;padding-top:8px;">
                <button type="submit" class="btn btn-primary">{{ isset($position) ? 'Update Position' : 'Add Position' }}</button>
                <a href="{{ route('admin.elections.show', $election) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
