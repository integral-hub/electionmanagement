@extends('layouts.admin')
@section('page-title', 'Results — ' . $election->name)

@push('styles')
    @vite('resources/css/admin-results.css')
@endpush

@section('topbar-actions')
    <a href="{{ route('admin.elections.show', $election) }}" class="btn btn-secondary btn-sm">← Election</a>
@endsection

@section('content')

@php
    $electionStatus = $election->status;

    // Matches App\Enums\ElectionStatusEnum exactly (Draft/Running/Paused/Completed/Cancelled/Scheduled)
    $electionStatusColor = match($electionStatus) {
        \App\Enums\ElectionStatusEnum::Completed->value => '#16a34a',
        \App\Enums\ElectionStatusEnum::Running->value   => '#f59e0b',
        \App\Enums\ElectionStatusEnum::Scheduled->value => '#4f46e5',
        \App\Enums\ElectionStatusEnum::Paused->value    => '#9ca3af',
        \App\Enums\ElectionStatusEnum::Cancelled->value => '#dc2626',
        \App\Enums\ElectionStatusEnum::Draft->value     => '#6b7280',
        default                                          => '#4f46e5',
    };
@endphp

{{-- ELECTION STATUS HEADER --}}
<div class="card results-header">
    <div>
        <div class="card-title">{{ $election->name }}</div>
        <div class="results-header-sub">Election results overview</div>
    </div>

    <span class="status-pill" style="color: {{ $electionStatusColor }};">
        {{ $electionStatus }}
    </span>
</div>

{{-- TURNOUT SUMMARY --}}
<div class="card turnout-grid">
    <div class="turnout-donut-wrap">
        <div class="turnout-donut" style="--pct: {{ $turnoutPercent }}">
            <div class="turnout-donut-inner">
                <div class="turnout-donut-value">{{ $turnoutPercent }}%</div>
                <div class="turnout-donut-label">Turnout</div>
            </div>
        </div>
    </div>

    <div class="turnout-stats">
        <div>
            <div class="turnout-stat-label">Eligible Voters</div>
            <div class="turnout-stat-value">{{ number_format($eligibleVoters) }}</div>
        </div>
        <div>
            <div class="turnout-stat-label">Ineligible / Banned</div>
            <div class="turnout-stat-value">{{ number_format($inEligibleVoters) }}</div>
        </div>
        <div>
            <div class="turnout-stat-label">Voters Who Voted</div>
            <div class="turnout-stat-value">{{ number_format($votedCount) }}</div>
        </div>
        <div>
            <div class="turnout-stat-label">Total Ballots Cast</div>
            <div class="turnout-stat-value">{{ number_format($totalVotes) }}</div>
        </div>
    </div>
</div>

{{-- RESULTS BAR CHART  --}}
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">ELECTION RESULTS CHART</div>
            <div class="results-header-sub">Candidates Bar Chart</div>
        </div>
    </div>

    @if($results->isEmpty() || $results->every(fn ($r) => $r['candidates']->isEmpty()))
        <p class="vote-plot-empty">No candidates have been added yet.</p>
    @else
        @php
            $globalMaxVotes = max(1, $results->flatMap(fn ($r) => $r['candidates'])->max('votes') ?? 1);
        @endphp
        <div class="vote-plot-wrap">
            @foreach($results as $result)
                @continue($result['candidates']->isEmpty())
                <div class="vote-plot-group">
                    <div class="vote-plot-position-label">{{ $result['position']->title }}</div>

                    <div class="vote-plot-bars">
                        @foreach($result['candidates'] as $i => $item)
                            @php $barHeight = $item['votes'] > 0 ? max(4, round(($item['votes'] / $globalMaxVotes) * 100)) : 0; @endphp
                            <div class="vote-plot-bar-col" title="{{ $item['candidate']->name }}: {{ number_format($item['votes']) }} votes">
                                <span class="vote-plot-count">{{ number_format($item['votes']) }}</span>
                                <div class="vote-plot-bar {{ $item['votes'] === 0 ? 'is-empty' : '' }} {{ $i === 0 && $item['votes'] > 0 ? 'is-leading' : '' }}"
                                     style="height: {{ $barHeight }}%;"></div>
                            </div>
                        @endforeach
                    </div>

                    <div class="vote-plot-candidate-labels">
                        @foreach($result['candidates'] as $item)
                            <span class="vote-plot-candidate-label">{{ $item['candidate']->name }}</span>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- POSITION RESULTS --}}
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">Candidate Results</div>
            <div class="results-header-sub">List of candidates with results</div>
        </div>
    </div>
@if($results->isEmpty())
        <div class="empty-state results-empty">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/>
            </svg>
            <div class="empty-title">No results yet</div>
            <p>Results will appear once voting begins.</p>
        </div>
    </div>
@else
    @foreach($results as $result)
    <div class="card result-card">
        <div class="card-header">
            <div>
                <div class="card-title">{{ $result['position']->title }}</div>
                <div class="result-card-sub">{{ number_format($result['total_votes']) }} votes cast</div>
            </div>
            <span class="result-candidate-count">{{ count($result['candidates']) }} candidates</span>
        </div>

        <div class="candidate-list">
            @foreach($result['candidates'] as $i => $item)
                @php
                    $candidate = $item['candidate'];
                    $votes = $item['votes'];
                    $pct = $item['percent'];

                    $candidateStatus = $candidate->status;

                    // Matches App\Enums\CandidateStatusEnum with colors
                    $candidateStatusColor = match($candidateStatus) {
                        \App\Enums\CandidateStatusEnum::Active->value    => '#16a34a',
                        \App\Enums\CandidateStatusEnum::Withdrawn->value => '#6b7280',
                        default                                          => '#6b7280',
                    };
                @endphp

                <div class="candidate-row">
                    <div class="candidate-rank {{ $i === 0 && $votes > 0 ? 'is-leading' : '' }}">{{ $i + 1 }}</div>

                    <div class="candidate-photo">
                        @if(data_get($candidate, 'photo.url'))
                            <img src="{{ data_get($candidate, 'photo.url') }}" alt="{{ $candidate->name }}">
                        @endif
                    </div>

                    <div class="candidate-details">
                        <div class="candidate-details-top">
                            <span class="candidate-name">{{ $candidate->name }}</span>
                            <span class="candidate-tally">
                                {{ $pct }}%
                                <span class="candidate-tally-count">({{ number_format($votes) }})</span>
                            </span>
                        </div>

                        <div class="candidate-bar-track">
                            <div class="candidate-bar-fill" style="width: {{ $pct }}%;"></div>
                        </div>

                        <div class="candidate-status-wrap">
                            <span class="candidate-status" style="color: {{ $candidateStatusColor }};">
                                {{ $candidateStatus }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endforeach
@endif

@endsection