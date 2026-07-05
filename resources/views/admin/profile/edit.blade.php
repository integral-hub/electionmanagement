@extends('layouts.admin')
@section('page-title','Edit Profile')
@section('topbar-actions')<a href="{{ route('admin.profile.show') }}" class="btn btn-s btn-sm">← Profile</a>@endsection
@section('content')
<div style="max-width:500px"><div class="card">
<div style="margin-bottom:20px"><h2 style="font-size:16px;font-weight:700">Edit Profile</h2></div>
<form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="fs">
@csrf @method('PUT')
<div style="display:flex;align-items:center;gap:14px;margin-bottom:18px">
<div style="width:58px;height:58px;border-radius:50%;overflow:hidden;background:var(--acl);display:grid;place-items:center;flex-shrink:0;border:2px solid var(--bdr)" id="pp">
@if(isset($user) && data_get($user, 'profile_photo_path.url'))
    <img src="{{ data_get($user, 'profile_photo_path.url') }}"
            alt="{{ $user->name }}">
@else<span id="px" style="font-size:20px;font-weight:700;color:var(--ac)">{{ strtoupper(substr($user->name,0,1)) }}</span>@endif</div>
<div><label class="btn btn-s btn-sm" style="cursor:pointer">Change Photo<input type="file" name="profile_photo" accept="image/*" style="display:none" onchange="prev(this)"></label><p style="font-size:11px;color:var(--ink3);margin-top:3px">JPG, PNG up to 2MB</p></div></div>
<div class="fg"><label class="fl">Full Name *</label><input type="text" name="name" class="fi {{ $errors->has('name')?'err':'' }}" value="{{ old('name',$user->name) }}" required>@error('name')<span class="fe">{{ $message }}</span>@enderror</div>
<div class="fg"><label class="fl">Email Address</label><input type="email" class="fi" value="{{ $user->email }}" disabled style="opacity:.6;cursor:not-allowed"><span class="fh">Email cannot be changed here. Contact support.</span></div>
<div style="display:flex;gap:9px;padding-top:4px"><button type="submit" class="btn btn-p">Save Changes</button><a href="{{ route('admin.profile.show') }}" class="btn btn-s">Cancel</a></div>
</form></div></div>
<script>function prev(i){if(!i.files[0])return;const r=new FileReader();r.onload=e=>{const pp=document.getElementById('pp');pp.innerHTML=`<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover">`};r.readAsDataURL(i.files[0])}</script>
@endsection
