@extends('layouts.admin')
@section('page-title', 'Edit Election')

@section('topbar-actions')
    <a href="{{ route('admin.elections.show', $election) }}" class="btn btn-secondary btn-sm">← Election</a>
@endsection

@section('content')
<div style="max-width:600px;">
    <div class="card">
        <div style="margin-bottom:24px;">
            <h2 style="font-size:18px;font-weight:700;">Edit Election</h2>
            <p style="font-size:14px;color:var(--ink-3);margin-top:4px;">Update the details for this election.</p>
        </div>

        <form action="{{ route('admin.elections.update', $election) }}" method="POST" class="form-section">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label" for="name">Election Name <span style="color:var(--danger)">*</span></label>
                <input type="text" id="name" name="name" class="form-input {{ $errors->has('name') ? 'error' : '' }}" value="{{ old('name', $election->name) }}" required>
                @error('name') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea id="description" name="description" class="form-textarea">{{ old('description', $election->description) }}</textarea>
            </div>
            @if(in_array($election->status, $statuses))
            <div class="form-group">
                <label class="form-label" for="status">Status</label>
                <select id="status" name="status" class="form-select">
                    @foreach($statuses as $s)
                    <option value="{{ $s }}" {{ old('status', $election->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div style="display:flex;gap:10px;padding-top:8px;">
                <button type="submit" class="btn btn-primary">Update Election</button>
                <a href="{{ route('admin.elections.show', $election) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
