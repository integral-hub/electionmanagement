@extends('layouts.admin')
@section('page-title', 'Voters — ' . $election->name)

@push('scripts')
    @vite('resources/js/admin-voters-filter.js')
@endpush

@section('topbar-actions')
    <a href="{{ route('admin.elections.show', $election) }}" class="btn btn-secondary btn-sm">← Election</a>
    @can('viewImport', \App\Models\Voter::class)
    <a href="{{ route('admin.elections.voters.import-logs.log', $election) }}" class="btn btn-primary btn-sm">View Import Logs</a>
    @endcan
    @can('assign.voters')
    <a href="{{ route('admin.elections.voters.assign.view', $election) }}" class="btn btn-primary btn-sm">+ Assign Voter</a>
    @endcan
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title">All Voters</div>

        {{-- filter over the current page  --}}
        <form action="{{ route('admin.elections.voters.index', $election) }}" method="GET" style="display:flex;gap:8px;" data-voter-filter>
            <input type="search" name="q" class="form-input" placeholder="Search this page…" value="{{ request('q') }}" style="width:200px;" data-filter-q>
            <select name="status" class="form-input" style="width:150px;" data-filter-status>
                <option value="">Filter by status</option>
                <option value="validated" {{ request('status') == 'validated' ? 'selected' : '' }}>Approved</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>Banned</option>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm" title="Search across every voter, not just this page">Search all</button>
            @if(request()->hasAny(['q','status']))
                <a href="{{ route('admin.elections.voters.index', $election) }}" class="btn btn-secondary btn-sm">Clear</a>
            @endif
        </form>
    </div>

    @if($voters->isEmpty())
        <div class="empty-state">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            <div class="empty-title">No voters assigned</div>
            <p style="font-size:13px;margin-bottom:14px;">Assign voters manually or import from CSV.</p>
            @can('assign.voters')
            <a href="{{ route('admin.elections.voters.assign.view', $election) }}" class="btn btn-primary btn-sm">Assign Voters</a>
            @endcan
        </div>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Email Verified</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($voters as $voter)
                @php $status = $voter->pivot?->status; @endphp
                <tr data-voter-row data-search="{{ strtolower($voter->email . ' ' . $voter->phone) }}" data-status="{{ $status }}">
                    <td style="font-weight:500;">{{ $voter->email ?? '—' }}</td>
                    <td style="color:var(--ink-3);">{{ $voter->phone ?? '—' }}</td>
                    <td>
                        @if($voter->is_verified_email)
                            <span class="badge badge-running"><span class="badge-dot"></span> Yes</span>
                        @else
                            <span class="badge badge-cancelled"><span class="badge-dot"></span> No</span>
                        @endif
                    </td>
                    <td>
                        @if($status === 'validated')
                            <span class="badge badge-running"><span class="badge-dot"></span>Approved</span>
                        @elseif($status === 'banned')
                            <span class="badge badge-cancelled"><span class="badge-dot"></span>Banned</span>
                        @else
                            <span class="badge badge-scheduled"><span class="badge-dot"></span>Pending</span>
                        @endif
                    </td>
                    <td style="color:var(--ink-3);font-size:13px;">{{ $voter->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            @can('view', $voter)
                            <a href="{{ route('admin.elections.voters.show', [$election, $voter]) }}" class="btn btn-secondary btn-sm">View</a>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p id="filterEmptyNote" style="display:none;text-align:center;padding:20px;color:var(--ink-3);font-size:13px;">
            No voters on this page match your filter. Try "Search all" to search every voter.
        </p>
    </div>
    <div style="padding:16px 0 0;">{{ $voters->links() }}</div>
    @endif
</div>
@endsection
