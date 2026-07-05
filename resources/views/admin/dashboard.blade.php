@extends('layouts.admin')
@section('page-title', 'Dashboard')

@section('topbar-actions')
    @can('view.elections')
    <a href="{{ route('admin.elections.index') }}" class="btn btn-primary btn-sm">
        <span class="badge-dot"></span>
        Elections
    </a>
    @endcan
@endsection

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#eef2ff;">
            <svg width="20" height="20" fill="none" stroke="#4f46e5" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/></svg>
        </div>
        <div class="stat-label">Total Elections</div>
        <div class="stat-value">{{ $stats['elections'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef9c3;">
            <svg width="20" height="20" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        </div>
        <div class="stat-label">Total Voters</div>
        <div class="stat-value">{{ number_format($stats['total_voters']) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#cdc3fe;">
            <svg width="20" height="20" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        </div>
        <div class="stat-label">Staff</div>
        <div class="stat-value">{{ number_format($stats['staff']) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#dcfce7;">
            <svg width="20" height="20" fill="none" stroke="#059669" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
        </div>
        <div class="stat-label">Running Elections</div>
        <div class="stat-value">{{ $stats['active'] }}</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 340px;gap:24px;">
    <!-- Recent Elections -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">Recent Elections</div>
          @can('view.elections')  <a href="{{ route('admin.elections.index') }}" class="btn btn-secondary btn-sm">View all</a> @endcan
        </div>
        @if($recentElections->isEmpty())
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/></svg>
                <div class="empty-title">No elections yet</div>
                <p style="font-size:13px;">Create your first election to get started.</p>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Voters</th>
                            <th>Created</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentElections as $election)
                        <tr>
                           @can('view.elections') <td><a href="{{ route('admin.elections.show', $election) }}" style="font-weight:500;color:var(--ink);text-decoration:none;">{{ $election->name }}</a></td> @endcan
                            <td><span class="badge badge-{{ $election->status }}"><span class="badge-dot"></span>{{ ucfirst($election->status) }}</span></td>
                            <td>{{ $election->voters->count() ?? '—' }}</td>
                            <td style="color:var(--ink-3);font-size:13px;">{{ $election->created_at->format('M j, Y') }}</td>
                           @can('view.elections') <td><a href="{{ route('admin.elections.show', $election) }}" class="btn btn-secondary btn-sm">Manage</a></td> @endcan
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Recent Audit Logs -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">Recent Activity</div>
           @can('view.audit_logs') <a href="{{ route('admin.audit-logs') }}" class="btn btn-secondary btn-sm">All</a> @endcan
        </div>
        <div style="display:flex;flex-direction:column;gap:12px;">
            @forelse($recentLogs as $log)
            <div style="display:flex;gap:10px;align-items:flex-start;">
                <div style="width:8px;height:8px;border-radius:50%;background:var(--accent);margin-top:5px;flex-shrink:0;"></div>
                <div>
                    <div style="font-size:13px;font-weight:500;">{{ $log->description ?? 'Action logged' }}</div>
                    <div style="font-size:11px;color:var(--ink-3);">{{ $log->created_at?->diffForHumans() }}</div>
                </div>
            </div>
            @empty
            <div style="font-size:13px;color:var(--ink-3);text-align:center;padding:20px 0;">No activity yet.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
