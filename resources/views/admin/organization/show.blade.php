@extends('layouts.admin')
@section('page-title', 'Organisation')
@section('topbar-actions')
 @can('update', $org) <a href="{{ route('admin.organization.edit') }}" class="btn btn-primary btn-sm">Edit Organisation</a> @endcan
@endsection
@section('content')
<div style="max-width:640px;">
    <div class="card">
        <div style="display:flex;gap:20px;align-items:flex-start;margin-bottom:24px;">
            <div style="width:72px;height:72px;border-radius:16px;overflow:hidden;background:var(--surface);border:1px solid var(--border);flex-shrink:0;display:grid;place-items:center;">
                @if($org->logo)
                    <img src="{{ $org->logo['url'] }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <span style="font-size:28px;font-weight:700;color:var(--accent);">{{ strtoupper(substr($org->name,0,1)) }}</span>
                @endif
            </div>
            <div>
                <h2 style="font-size:20px;font-weight:700;">{{ $org->name }}</h2>
                <p style="font-size:14px;color:var(--ink-3);margin-top:2px;">Slug: <code style="font-family:monospace;background:var(--surface);padding:2px 6px;border-radius:4px;font-size:13px;">{{ $org->slug }}</code></p>
                <span class="badge {{ $org->active ? 'badge-running' : 'badge-cancelled' }}" style="margin-top:8px;">
                    <span class="badge-dot"></span>{{ $org->active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            @foreach(['Email' => $org->email, 'Phone' => $org->phone, 'Website' => $org->website] as $label => $value)
            <div style="padding:14px;background:var(--surface);border-radius:10px;">
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--ink-3);margin-bottom:4px;">{{ $label }}</div>
                <div style="font-size:14px;font-weight:500;">{{ $value ?: '—' }}</div>
            </div>
            @endforeach
            <div style="padding:14px;background:var(--surface);border-radius:10px;">
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--ink-3);margin-bottom:4px;">Created</div>
                <div style="font-size:14px;font-weight:500;">{{ $org->created_at->format('M j, Y') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
