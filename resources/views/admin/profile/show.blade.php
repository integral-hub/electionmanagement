@extends('layouts.admin')
@section('page-title','My Profile')
@section('topbar-actions')
<a href="{{ route('admin.profile.edit') }}" class="btn btn-s btn-sm">Edit Profile</a>
<a href="{{ route('admin.profile.password.edit') }}" class="btn btn-p btn-sm">Change Password</a>
@endsection
@section('content')
<div style="max-width:600px">
<div class="card" style="margin-bottom:16px"><div style="display:flex;gap:18px;align-items:flex-start">
<div style="width:64px;height:64px;border-radius:50%;overflow:hidden;flex-shrink:0;background:var(--acl);display:grid;place-items:center;border:2px solid var(--bdr)">
@if(isset($user) && data_get($user, 'profile_photo_path.url'))
    <img src="{{ data_get($user, 'profile_photo_path.url') }}"
            alt="{{ $user->name }}">
@else<span style="font-size:24px;font-weight:700;color:var(--ac)">{{ strtoupper(substr($user->name,0,1)) }}</span>@endif</div>
<div style="flex:1"><h2 style="font-size:18px;font-weight:700;margin-bottom:3px">{{ $user->name }}</h2><p style="font-size:13px;color:var(--ink3);margin-bottom:9px">{{ $user->email }}</p>
<div style="display:flex;gap:5px;flex-wrap:wrap">@foreach($user->roles as $role)<span class="badge b-scheduled">{{ $role->name }}</span>@endforeach</div></div></div></div>
<div class="card"><div class="ct" style="margin-bottom:14px">Organisation</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
<div style="padding:11px;background:var(--sur);border-radius:7px"><div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--ink3);margin-bottom:2px">Organisation</div><div style="font-size:13px;font-weight:500">{{ $user->organization?->name??'—' }}</div></div>
<div style="padding:11px;background:var(--sur);border-radius:7px"><div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--ink3);margin-bottom:2px">Member Since</div><div style="font-size:13px;font-weight:500">{{ $user->created_at->format('M j, Y') }}</div></div>
</div></div></div>
@endsection
