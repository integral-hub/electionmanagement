@extends('layouts.admin')
@section('page-title', $election->name)

@section('topbar-actions')
    <a href="{{ route('admin.elections.index') }}" class="btn btn-secondary btn-sm">← Elections</a>
    @can('update', $election)<a href="{{ route('admin.elections.edit', $election) }}" class="btn btn-secondary btn-sm">Edit</a>@endcan
   @can('update', $election->setting ?? $election) <a href="{{ route('admin.elections.settings', $election) }}" class="btn btn-secondary btn-sm">Settings</a> @endcan
@endsection

@section('content')
{{-- Status bar --}}
<div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;flex-wrap:wrap;">
    <span class="badge badge-{{ $election->status }}" style="font-size:13px;padding:5px 14px;">
        <span class="badge-dot"></span>{{ ucfirst($election->status) }}
    </span>
    @if($election->setting?->voting_start)
    <span style="font-size:13px;color:var(--ink-3);">
        <strong>Start:</strong> {{ $election->setting->voting_start->format('d M Y, H:i') }}
    </span>
    @endif
    @if($election->setting?->voting_end)
    <span style="font-size:13px;color:var(--ink-3);">
        <strong>End:</strong> {{ $election->setting->voting_end->format('d M Y, H:i') }}
    </span>
    @endif
    <span style="margin-left:auto;">
        <a href="{{ route('voter.login', $election) }}" target="_blank" class="btn btn-secondary btn-sm">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            Voter Portal
        </a>
    </span>
</div>
{{-- Portal Setup Progress --}}
<div class="card" style="margin-bottom:24px;">
    <div class="card-header">
        <div>
            <div class="card-title">Portal Setup Progress</div>
            <div style="font-size:13px;color:var(--ink-3);margin-top:4px;">
                Track election readiness before opening the voter portal.
            </div>
        </div>

        <div style="text-align:right;">
            <div style="
                font-size:28px;
                font-weight:700;
                color:{{ $progress === 100 ? '#16a34a' : 'var(--accent)' }};
            ">
                {{ $progress }}%
            </div>
            <div style="font-size:12px;color:var(--ink-3);">
                Complete
            </div>
        </div>
    </div>

 {{-- Progress Bar --}}
<div style="
    height:10px;
    background:#e5e7eb;
    border-radius:999px;
    overflow:hidden;
    margin-bottom:18px;
">
    <div style="
        height:100%;
        width:{{ $progress }}%;
        background:{{ $progress === 100 ? '#16a34a' : 'var(--accent)' }};
        transition:width .3s ease;
    "></div>
</div>

{{-- Checklist --}}
<div style="display:grid;gap:10px;">

    @foreach($checklist as $key => $item)

        @php
            // Hide voting_dates until core setup is done
            if (isset($item['visible']) && $item['visible'] === false) {
                continue;
            }

            // Hide completed items except voting_dates
            if ($item['done'] && $key !== 'voting_dates') {
                continue;
            }
        @endphp

        <div style="
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:12px;
            padding:14px;
            border:1px solid var(--border);
            border-radius:10px;
            background:{{ $item['done'] ? '#f0fdf4' : '#fff' }};
        ">

            {{-- LEFT SIDE --}}
            <div style="display:flex;align-items:flex-start;gap:12px;">

                {{-- ICON --}}
                @if($item['done'])
                    <div style="
                        width:24px;
                        height:24px;
                        border-radius:50%;
                        background:#dcfce7;
                        color:#16a34a;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        font-size:13px;
                        font-weight:700;
                        flex-shrink:0;
                    ">
                        &#10003;
                    </div>
                @else
                    <div style="
                        width:24px;
                        height:24px;
                        border-radius:50%;
                        background:#fee2e2;
                        color:#dc2626;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        font-size:13px;
                        font-weight:700;
                        flex-shrink:0;
                    ">
                        &#x26A0;
                    </div>
                @endif

                {{-- TEXT --}}
                <div>
                    <div style="font-weight:600;font-size:14px;">
                        {{ $item['label'] }}
                    </div>

                    @if(! $item['done'] && ! empty($item['hint']))
                        <div style="
                            font-size:12px;
                            color:#dc2626;
                            margin-top:3px;
                        ">
                            {{ $item['hint'] }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- RIGHT SIDE --}}
            <div style="display:flex;align-items:center;gap:10px;">

                @if($item['done'] && $key !== 'voting_dates')
                    <span style="
                        color:#16a34a;
                        font-size:12px;
                        font-weight:600;
                    ">
                        Completed
                    </span>

                @elseif(! empty($item['route']))
                  @can('update.election_settings') <a href="{{ $item['route'] }}" class="btn btn-primary btn-sm">
                        {{ $item['action'] }}
                    </a>
                  @endcan

                @else
                    <button type="button" class="btn btn-secondary btn-sm" disabled>
                        {{ $item['action'] }}
                    </button>
                @endif

            </div>
        </div>

    @endforeach
</div>
</div>

@php
    $pendingItems = collect($checklist)->filter(function ($item, $key) {
        if (isset($item['visible']) && $item['visible'] === false) return false;
        if ($item['done'] && $key !== 'voting_dates') return false;
        return !$item['done'];
    })->count();
@endphp

{{-- Status Message --}}
@if($progress === 100)
    <div class="alert alert-success" style="margin-top:16px;">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Election portal is fully configured and ready.
    </div>
@else
    <div class="alert alert-info" style="margin-top:16px;">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <path d="M12 8v4m0 4h.01"/>
        </svg>
        {{ $pendingItems }} setup item {{ $pendingItems > 1 ? 's' : '' }} still require attention.
    </div>
@endif
{{-- Quick Stats --}}
<div class="stats-grid" style="margin-bottom:24px;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));">
    @foreach([['Positions',$election->positions->count(),'#eef2ff','#4f46e5'],['Candidates',$election->candidates->count(),'#fef9c3','#d97706'],['Voters',$election->voters_count,'#dcfce7','#059669'],['Votes Cast',$election->votes_count,'#ede9fe','#7c3aed']] as [$lbl,$val,$bg,$clr])
    <div class="stat-card" style="padding:16px;">
        <div style="font-size:24px;font-weight:700;color:{{ $clr }};">{{ $val ?? 0 }}</div>
        <div class="stat-label">{{ $lbl }}</div>
    </div>
    @endforeach
</div>

{{-- Tabs --}}
<div style="border-bottom:1px solid var(--border);margin-bottom:24px;display:flex;gap:0;overflow-x:auto;" id="electionTabs">
    @foreach(['positions'=>'Positions','candidates'=>'Candidates','voters'=>'Voters','registration'=>'Reg. Form','results'=>'Results'] as $tab => $label)
    <button onclick="showTab('{{ $tab }}')" id="tab-{{ $tab }}" class="tab-btn" style="padding:10px 18px;font-size:14px;font-weight:500;border:none;background:none;cursor:pointer;border-bottom:2px solid transparent;color:var(--ink-3);white-space:nowrap;transition:all .15s;font-family:inherit;">{{ $label }}</button>
    @endforeach
</div>

{{--  Positions Tab  --}}
<div id="pane-positions" class="tab-pane">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Positions</div>
          @can('create.positions')  <a href="{{ route('admin.elections.positions.create', $election) }}" class="btn btn-primary btn-sm">+ Add Position</a> @endcan
        </div>
        @if($election->positions->isEmpty())
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>
                <div class="empty-title">No positions yet</div>
                <p style="font-size:13px;">Add a position like "President" or "Secretary".</p>
            </div>
        @else
        <div class="table-wrap">
            <table>
                <thead><tr><th>Title</th><th>Description</th><th>Candidates</th><th>Actions</th></tr></thead>
                <tbody>
                @foreach($election->positions as $position)
                <tr>
                    <td style="font-weight:500;">{{ $position->title }}</td>
                    <td style="color:var(--ink-3);font-size:13px;">{{ $position->description ?: '—' }}</td>
                    <td>{{ $position->candidates->count() }}</td>
                    <td>
                        <div style="display:flex;gap:6px;">
                          @can('update', $position)  <a href="{{ route('admin.elections.positions.edit', [$election, $position]) }}" class="btn btn-secondary btn-sm">Edit</a> @endcan
                          @can('delete', $position)
                            <form action="{{ route('admin.elections.positions.destroy', [$election, $position]) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete position?')">Remove</button>
                            </form>
                          @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

{{-- ── Candidates Tab  --}}
<div id="pane-candidates" class="tab-pane" style="display:none;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Candidates</div>
            @if($election->positions->isNotEmpty())
            @can('create.candidates')
            <a href="{{ route('admin.elections.candidates.create', $election) }}" class="btn btn-primary btn-sm">+ Add Candidate</a>
            @endcan
            @endif
        </div>
        @php $allCandidates = $election->positions->flatMap(fn($p) => $p->candidates->map(fn($c) => ['candidate'=>$c,'position'=>$p])); @endphp
        @if($allCandidates->isEmpty())
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <div class="empty-title">No candidates yet</div>
            </div>
        @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:14px;">
            @foreach($allCandidates as ['candidate' => $c, 'position' => $p])
            <div style="border:1px solid var(--border);border-radius:10px;padding:16px;display:flex;gap:12px;">
                <div style="width:44px;height:44px;border-radius:50%;overflow:hidden;flex-shrink:0;background:var(--surface);">
                @if(data_get($c, 'photo.url'))
                    <img src="{{ data_get($c, 'photo.url') }}"
                    alt="{{ $c->name }}">
                @endif
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:600;font-size:14px;">{{ $c->name }}</div>
                    <div style="font-size:12px;color:var(--accent);margin-bottom:4px;">{{ $p->title }}</div>
                    <span class="badge badge-{{ $c->status }}" style="font-size:11px;">{{ ucfirst($c->status) }}</span>
                    <div style="display:flex;gap:6px;margin-top:8px;">
                    @can('update', $c)  <a href="{{ route('admin.elections.candidates.edit', [$election, $c]) }}" class="btn btn-secondary btn-sm" style="padding:4px 10px;font-size:12px;">Edit</a> @endcan
                    @can('delete', $c) 
                        <form action="{{ route('admin.elections.candidates.destroy', [$election, $c]) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" style="padding:4px 10px;font-size:12px;" onclick="return confirm('Remove candidate?')">Remove</button>
                        </form> 
                    @endcan
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{--  Voters Tab  --}}
<div id="pane-voters" class="tab-pane" style="display:none;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Voters</div>
            <div style="display:flex;gap:8px;">
              @can('view.voters')  <a href="{{ route('admin.elections.voters.index', $election) }}" class="btn btn-primary btn-sm"> View Voters</a> @endcan
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead><tr><th>Email / Phone</th><th>Verified</th><th>Last Login</th><th>Actions</th></tr></thead>
                <tbody>
                    <tr><td colspan="4" style="text-align:center;color:var(--ink-3);padding:30px;">Loading voters…</td></tr>
                </tbody>
            </table>
        </div>
        <p style="font-size:13px;color:var(--ink-3);margin-top:12px;">
         @can('view.voters') <a href="{{ route('admin.elections.voters.index', $election) }}" style="color:var(--accent);">View all voters →</a> @endcan
        </p>
    </div>
</div>

{{--  Registration Form Tab  --}}
<div id="pane-registration" class="tab-pane" style="display:none;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Voter Registration Form</div>
          @can('update', $election->setting ?? $election)  <a href="{{ route('admin.elections.registration.show', $election) }}" class="btn btn-primary btn-sm">Form Builder</a> @endcan
        </div>
        @if($election->registrationField)
            <div class="alert alert-success" style="margin-bottom:0;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Registration form configured with {{ count($election->registrationField->fields ?? []) }} field(s).
            </div>
        @else
            <div class="alert alert-info" style="margin-bottom:0;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
             @can('update', $election->setting ?? $election)   No registration form set up yet. <a href="{{ route('admin.elections.registration.show', $election) }}" style="color:var(--accent);">Create one →</a> @endcan
            </div>
        @endif
    </div>
</div>

{{--  Results Tab  --}}
<div id="pane-results" class="tab-pane" style="display:none;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Live Results</div>
            <a href="{{ route('admin.elections.results', $election) }}" class="btn btn-primary btn-sm">Full Results</a>
        </div>
        @if($election->votes_count === 0)
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/></svg>
                <div class="empty-title">No votes cast yet</div>
            </div>
        @else
            <p style="font-size:13px;color:var(--ink-3);">{{ number_format($election->votes_count) }} votes counted. <a href="{{ route('admin.elections.results', $election) }}" style="color:var(--accent);">View full breakdown →</a></p>
        @endif
    </div>
</div>

<script>
function showTab(name) {
    document.querySelectorAll('.tab-pane').forEach(p => p.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.style.borderBottomColor = 'transparent';
        b.style.color = 'var(--ink-3)';
    });
    document.getElementById('pane-' + name).style.display = 'block';
    const btn = document.getElementById('tab-' + name);
    btn.style.borderBottomColor = 'var(--accent)';
    btn.style.color = 'var(--accent)';
}
showTab('positions');
</script>
@endsection
