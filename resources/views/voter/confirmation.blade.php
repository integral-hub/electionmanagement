@extends('layouts.voter')
@section('title', 'Vote Confirmed')

@push('styles')
    @vite('resources/css/voter-confirmation.css')
@endpush

@section('content')
<div class="v-card confirmation-card">
    {{-- checkmark --}}
    <div class="confirmation-check">
        <svg fill="none" stroke="#059669" stroke-width="2.5" viewBox="0 0 24 24" width="40" height="40">
            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>

    <h1 class="confirmation-title">Votes Submitted!</h1>
    <p class="confirmation-subtitle">Your votes for <strong>{{ $election->name }}</strong> have been recorded securely.</p>

    {{-- Vote summary --}}
    @if($castVotes->isNotEmpty())
    <div class="confirmation-choices">
        <div class="confirmation-choices-heading">Your Choices</div>
        @foreach($castVotes as $vote)
        <div class="confirmation-choice-row">
            <div class="confirmation-choice-photo">
                @if(data_get($vote->candidate, 'photo.url'))
                    <img src="{{ data_get($vote->candidate, 'photo.url') }}" alt="{{ $vote->candidate->name }}">
                @else
                    <div class="confirmation-choice-photo-placeholder">{{ strtoupper(substr($vote->candidate->name, 0, 1)) }}</div>
                @endif
            </div>
            <div class="confirmation-choice-info">
                <div class="confirmation-choice-position">{{ $vote->position->title }}</div>
                <div class="confirmation-choice-candidate">{{ $vote->candidate->name }}</div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <div class="confirmation-notice">
        <svg fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24" width="18" height="18">
            <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
        <span>Your vote is anonymous and securely stored. No one can see who you voted for.</span>
    </div>

    <form action="{{ route('voter.logout', $election) }}" method="POST">
        @csrf
        <button type="submit" class="v-btn v-btn-secondary confirmation-signout-btn">Sign Out</button>
    </form>
    <p class="confirmation-footnote">
        Results will be available once voting closes.
    </p>
</div>
@endsection
