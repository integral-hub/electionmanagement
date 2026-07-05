@extends('layouts.admin')
@section('page-title', isset($voter)?'Edit Voter':'Assign Voter')
@section('topbar-actions')
<a href="{{ route('admin.elections.voters.index',$election) }}" class="btn btn-s btn-sm">← Voters</a>
@endsection

@section('content')
<div style="max-width:540px">
<div class="card">

<div style="margin-bottom:20px">
    <h2 style="font-size:16px;font-weight:700">
        {{ isset($voter)?'Edit Voter':'Assign Voter' }}
    </h2>
    <p style="font-size:12.5px;color:var(--ink3)">
        Election: <strong>{{ $election->name }}</strong>
    </p>
</div>

<form action="{{ isset($voter)
    ? route('admin.elections.voters.update',[$election,$voter])
    : route('admin.elections.voters.store',$election) }}"
method="POST" class="fs">

@csrf
@if(isset($voter))
    @method('PUT')
@endif

{{-- EMAIL + PHONE --}}
<div class="fr">
    <div class="fg">
        <label class="fl">Email</label>
        <input type="email" name="email"
            class="fi {{ $errors->has('email')?'err':'' }}"
            value="{{ old('email',$voter->email??'') }}"
            placeholder="voter@example.com">
        @error('email')<span class="fe">{{ $message }}</span>@enderror
    </div>

    <div class="fg">
        <label class="fl">Phone</label>
        <input type="tel" name="phone"
            class="fi"
            value="{{ old('phone',$voter->phone??'') }}"
            placeholder="+234 xxx xxxxxxx">
    </div>
</div>

{{-- PASSWORD (create only) --}}
@if(!isset($voter))
<div class="fr">
    <div class="fg">
        <label class="fl">Password <span class="fh">(blank = auto-generate)</span></label>
        <input type="password" name="password"
            class="fi {{ $errors->has('password')?'err':'' }}"
            placeholder="Min. 8 chars"
            autocomplete="new-password">
        @error('password')<span class="fe">{{ $message }}</span>@enderror
    </div>

    <div class="fg">
        <label class="fl">Confirm Password</label>
        <input type="password" name="password_confirmation"
            class="fi"
            placeholder="Repeat"
            autocomplete="new-password">
    </div>
</div>
@endif

{{-- BIO DATA --}}
@if(!empty($fields))
<div style="border-top:1px solid var(--bdr);padding-top:14px">

<p style="font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--ink3);margin-bottom:11px">
    Bio Data
</p>

@foreach($fields as $field)

@php
    $key = $field['field_name'];

    $value = old(
        $key,
        $voter?->voter_data[$key]
        ??  $voter?->getUniqueValue($key)
        ?? ''
    );
@endphp

<div class="fg" style="margin-bottom:12px">

    <label class="fl">
        {{ $field['label'] }}
        @if($field['required']??false)
            <span style="color:var(--err)"> *</span>
        @endif
    </label>

    @if($field['description']??null)
        <span class="fh">{{ $field['description'] }}</span>
    @endif

    @switch($field['field_type'])

        @case('textarea')
            <textarea name="{{ $key }}" class="fta"
                {{ ($field['required']??false)?'required':'' }}>
                {{ $value }}
            </textarea>
        @break

        @case('select')
            <select name="{{ $key }}" class="fsel"
                {{ ($field['required']??false)?'required':'' }}>
                <option value="">Select…</option>

                @foreach($field['options']??[] as $opt)
                    <option value="{{ $opt }}"
                        {{ $value === $opt ? 'selected' : '' }}>
                        {{ $opt }}
                    </option>
                @endforeach
            </select>
        @break

        @case('date')
            <input type="date" name="{{ $key }}"
                class="fi"
                value="{{ $value }}"
                {{ ($field['required']??false)?'required':'' }}>
        @break

        @default
            <input type="text" name="{{ $key }}"
                class="fi"
                value="{{ $value }}"
                placeholder="{{ $field['label'] }}"
                {{ ($field['required']??false)?'required':'' }}>
    @endswitch

    @error($key)
        <span class="fe">{{ $message }}</span>
    @enderror

</div>

@endforeach
</div>
@endif

{{-- BUTTONS --}}
<div style="display:flex;gap:9px;padding-top:4px">
    <button type="submit" class="btn btn-p">
        {{ isset($voter)?'Update Voter':'Assign Voter' }}
    </button>

    <a href="{{ route('admin.elections.voters.index',$election) }}" class="btn btn-s">
        Cancel
    </a>
</div>

</form>
</div>
</div>
@endsection