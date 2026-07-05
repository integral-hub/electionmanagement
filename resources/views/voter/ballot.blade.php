@extends('layouts.voter')
@section('title', 'Cast Your Vote')

@push('styles')
    @vite('resources/css/voter-ballot.css')
@endpush

@push('scripts')
    @vite('resources/js/voter-ballot.js')
@endpush

@section('content')

{{-- Progress bar --}}
<div class="ballot-progress">
    <div class="ballot-progress-top">
        <span class="ballot-progress-label">Your Ballot</span>
        <span class="ballot-progress-count" id="progressLabel">0 of {{ $election->positions->count() }} positions</span>
    </div>
    <div class="ballot-progress-track">
        <div id="progressBar" class="ballot-progress-fill"></div>
    </div>
</div>

<form action="{{ route('voter.cast', $election) }}" method="POST" id="ballotForm" data-total-positions="{{ $election->positions->count() }}">
    @csrf

    @if($errors->any())
        <div class="v-alert v-alert-error">{{ $errors->first() }}</div>
    @endif

    @forelse($election->positions as $posIndex => $position)
    <div class="v-card position-card" id="pos-{{ $position->id }}">
        {{-- Position header --}}
        <div class="position-header">
            <div>
                <div class="position-eyebrow">Position {{ $posIndex + 1 }}</div>
                <h2 class="position-title">{{ $position->title }}</h2>
                @if($position->description)
                    <p class="position-description">{{ $position->description }}</p>
                @endif
            </div>
            <div id="check-{{ $position->id }}" class="position-check">
                <svg fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24" width="16" height="16"><path d="M5 13l4 4L19 7"/></svg>
            </div>
        </div>

        {{-- Candidates --}}
        @if($position->candidates->isEmpty())
            <p class="position-empty">No candidates for this position.</p>
        @else
        <div class="candidate-choices">
            @foreach($position->candidates as $candidate)
            <label class="candidate-card" for="vote_{{ $position->id }}_{{ $candidate->id }}">
                <input
                    type="radio"
                    id="vote_{{ $position->id }}_{{ $candidate->id }}"
                    name="votes[{{ $posIndex }}][candidate_id]"
                    value="{{ $candidate->id }}"
                    data-position="{{ $position->id }}"
                    class="candidate-card-radio"
                >
                <input type="hidden" name="votes[{{ $posIndex }}][position_id]" value="{{ $position->id }}">

                {{-- Photo --}}
                <div class="candidate-photo-lg">
                @if(data_get($candidate, 'photo.url'))
                <img src="{{ data_get($candidate, 'photo.url') }}" alt="{{ $candidate->name }}">
                @else
                <div class="candidate-photo-placeholder">{{ strtoupper(substr($candidate->name, 0, 1)) }}</div>
                @endif
                </div>

                {{-- Info --}}
                <div class="candidate-info">
                    <div class="candidate-info-name">{{ $candidate->name }}</div>
                    @if($candidate->bio)
                        <div class="candidate-info-bio">{{ Str::limit($candidate->bio, 80) }}</div>
                    @endif
                    @if($candidate->manifesto)
                        <div class="candidate-info-manifesto">"{{ Str::limit($candidate->manifesto, 70) }}"</div>
                    @endif
                </div>

                {{-- Selected indicator --}}
                <div class="vote-indicator">
                    <div class="vote-indicator-dot"></div>
                </div>
            </label>
            @endforeach
        </div>
        @endif
    </div>
    @empty
        <div class="v-card ballot-empty-card">
            <p>No positions have been set up for this election yet.</p>
        </div>
    @endforelse

    @if($election->positions->isNotEmpty())
    <div class="ballot-submit-wrap">
        <button type="submit" class="v-btn v-btn-primary ballot-submit-btn" id="submitBtn">
            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" width="18" height="18"><polyline points="9,11 12,14 22,4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
            Submit My Votes
        </button>
    </div>
    @endif
</form>

<div class="ballot-footnote">
    <strong>Important:</strong> Once submitted, your votes cannot be changed.
</div>
@endsection
