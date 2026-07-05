@extends('layouts.admin')
@section('page-title', 'Edit Organisation')
@section('topbar-actions')
    <a href="{{ route('admin.organization.show') }}" class="btn btn-secondary btn-sm">← Organisation</a>
@endsection
@section('content')
<div style="max-width:560px;">
    <div class="card">
        <div style="margin-bottom:24px;">
            <h2 style="font-size:18px;font-weight:700;">Edit Organisation</h2>
        </div>
        <form action="{{ route('admin.organization.update', $org) }}" method="POST" enctype="multipart/form-data" class="form-section">
            @csrf 
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Organisation Name *</label>
                <input type="text" name="name" class="form-input {{ $errors->has('name')?'error':'' }}" value="{{ old('name', $org->name) }}" required>
                @error('name') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email',$org->email) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="tel" name="phone" class="form-input" value="{{ old('phone',$org->phone) }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Website</label>
                <input type="url" name="website" class="form-input" value="{{ old('website',$org->website) }}" placeholder="https://example.com">
            </div>

            <div class="form-group">
                <label class="form-label">Logo</label>
                @if(!empty($org->logo))
                    <img src="{{ $org->logo['url'] }}" style="width:60px;height:60px;border-radius:10px;object-fit:cover;margin-bottom:8px;">
                @endif
                <input type="file" name="logo" class="form-input" accept="image/*" style="padding:8px;">
            </div>

            <div style="display:flex;gap:10px;padding-top:8px;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('admin.organization.show') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
