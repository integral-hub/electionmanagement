@extends('layouts.voter')
@section('title', 'Not Available')

@section('content')
<div class="not-ready-wrapper">

    <div class="not-ready-icon">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
    </div>

    <h1 class="not-ready-title">Voter Portal Not Available</h1>

    <p class="not-ready-text">
        The voter portal for <strong>{{ $election->name }}</strong> is not available at the moment.
    </p>

    <p class="not-ready-subtext">
        Please check back later or contact the election administrator.
    </p>

    @if($can && !$can->allowed)
        <div class="not-ready-alert">
            {{ $can->reason }}
        </div>
    @endif

</div>
@endsection