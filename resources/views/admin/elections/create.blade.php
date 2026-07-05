@extends('layouts.admin')
@section('page-title', isset($election) ? 'Edit Election' : 'Create Election')

@section('topbar-actions')
    <a href="{{ route('admin.elections.index') }}" class="btn btn-secondary btn-sm">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="15" height="15"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        Back
    </a>
@endsection

@section('content')
<div style="max-width:600px;">
    <div class="card">
        <div style="margin-bottom:24px;">
            <h2 style="font-size:18px;font-weight:700;">{{ isset($election) ? 'Edit Election' : 'New Election' }}</h2>
            <p style="font-size:14px;color:var(--ink-3);margin-top:4px;">{{ isset($election) ? 'Update election details.' : 'Fill in the details to create a new election.' }}</p>
        </div>
        @if ($errors->any())
        <div class="alert alert-error" style="margin-bottom:16px;">
            <ul style="margin:0;padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ isset($election) ? route('admin.elections.update', $election) : route('admin.elections.store') }}" method="POST" class="form-section">
            @csrf
            @if(isset($election)) @method('PUT') @endif

            <div class="form-group">
                <label class="form-label" for="name">Election Name <span style="color:var(--danger)">*</span></label>
                <input type="text" id="name" name="name" class="form-input {{ $errors->has('name') ? 'error' : '' }}" placeholder="e.g. Student Union Elections 2025" value="{{ old('name', $election->name ?? '') }}" required>
                @error('name') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea id="description" name="description" class="form-textarea {{ $errors->has('description') ? 'error' : '' }}" placeholder="Brief description of this election…">{{ old('description', $election->description ?? '') }}</textarea>
                @error('description') <span class="form-error">{{ $message }}</span> @enderror
            </div>
            
            <div style="display:flex;gap:10px;padding-top:8px;">
                <button type="submit" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16"><path d="M5 13l4 4L19 7"/></svg>
                    {{ isset($election) ? 'Update Election' : 'Create Election' }}
                </button>
                <a href="{{ route('admin.elections.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
