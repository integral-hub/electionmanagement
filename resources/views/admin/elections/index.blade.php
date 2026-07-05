@extends('layouts.admin')
@section('page-title', 'Elections')

@push('styles')
    @vite('resources/css/admin-elections.css')
@endpush

@section('topbar-actions')
    @can('create', \App\Models\Election::class)
    <a href="{{ route('admin.elections.create') }}" class="btn btn-primary btn-sm">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        New Election
    </a>
    @endcan
@endsection

@section('content')
@if($elections->isEmpty())
    <div class="card">
        <div class="empty-state elections-empty">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            <div class="empty-title">No elections found</div>
            <p>Create your first election to get started.</p>
            @can('create', \App\Models\Election::class)
            <a href="{{ route('admin.elections.create') }}" class="btn btn-primary btn-sm">Create Election</a>
            @endcan
        </div>
    </div>
@else
    <div class="election-grid">
        @foreach($elections as $election)
        <div class="card election-card">
            <div class="election-card-body">
                <div class="election-card-top">
                    <span class="badge badge-{{ $election->status }}">
                        <span class="badge-dot"></span>{{ ucfirst($election->status) }}
                    </span>
                    @canany(['update', 'delete'], $election)
                    <div class="dropdown">
                        <button data-dropdown class="election-card-menu-btn">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18"><circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                        </button>
                        <div class="dropdown-menu">
                            @can('view', $election)
                            <a href="{{ route('admin.elections.show', $election) }}" class="dropdown-item">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="15" height="15"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                View
                            </a>
                            @endcan
                            @can('update', $election)
                            <a href="{{ route('admin.elections.edit', $election) }}" class="dropdown-item">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="15" height="15"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit
                            </a>
                            <a href="{{ route('admin.elections.settings', $election) }}" class="dropdown-item">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="15" height="15"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
                                Settings
                            </a>
                            @endcan
                            @can('delete', $election)
                            <div class="dropdown-sep"></div>
                            <form action="{{ route('admin.elections.destroy', $election) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item danger" onclick="return confirm('Delete this election?')">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="15" height="15"><polyline points="3,6 5,6 21,6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                                    Delete
                                </button>
                            </form>
                            @endcan
                        </div>
                    </div>
                    @endcanany
                </div>
                <h3 class="election-card-title">
                    @can('view', $election)
                        <a href="{{ route('admin.elections.show', $election) }}">{{ $election->name }}</a>
                    @else
                        {{ $election->name }}
                    @endcan
                </h3>
                @if($election->description)
                <p class="election-card-desc">{{ Str::limit($election->description, 80) }}</p>
                @endif
            </div>

            <div class="election-stat-strip">
                @foreach([['Positions', $election->positions_count], ['Candidates', $election->candidates_count], ['Voters', $election->voters_count]] as [$label, $count])
                <div class="election-stat">
                    <div class="election-stat-value">{{ $count ?? 0 }}</div>
                    <div class="election-stat-label">{{ $label }}</div>
                </div>
                @endforeach
            </div>

            @can('view', $election)
            <div class="election-card-actions">
                <a href="{{ route('admin.elections.show', $election) }}" class="btn btn-secondary btn-sm">Manage</a>
            </div>
            @endcan
        </div>
        @endforeach
    </div>

    <div class="elections-pagination">{{ $elections->links() }}</div>
@endif
@endsection
