@extends('layouts.admin')
@section('page-title', 'Assign Voters')

@push('scripts')
    @vite(['resources/js/admin-voters-filter.js', 'resources/js/admin-voters-assign.js'])
@endpush

@section('topbar-actions')
    <a href="{{ route('admin.elections.voters.index', $election) }}" class="btn btn-secondary btn-sm">← Voters</a>
    @can('viewImport', \App\Models\Voter::class)
    <a href="{{ route('admin.elections.voters.import-logs.log', $election) }}" class="btn btn-primary btn-sm">View Import Logs</a>
    @endcan
    @can('import.voters')
    <a href="{{ route('admin.elections.voters.import.template', $election) }}" class="btn btn-primary btn-sm">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13">
            <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
        Download Template
    </a>
    @endcan
@endsection

@section('content')

{{-- Import panel --}}
@can('import.voters')
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <div>
            <div class="card-title">Bulk Import </div><span style="font-style: italic;">Read the following instructions before uploading</span>
                <p style="font-size:13px;color:var(--ink-3);margin-top:3px;max-width:560px;">
                    Download the template, fill in the voters' details, and upload it to bulk import voters into <strong>{{ $election->name }}</strong>.
                </p>

                <p style="font-size:13px;color:var(--ink-3);margin-top:8px;max-width:560px;">
                    Imported voters are automatically <strong>verified and validated</strong>. Only use this feature for voters already confirmed by your organisation.
                </p>

                <p style="font-size:13px;color:var(--ink-3);margin-top:8px;max-width:560px;">
                    Only <strong>CSV</strong>, <strong>XLSX</strong>, and <strong>XLS</strong> files are supported. Do not modify the template header. The <strong>Phone</strong> and <strong>Batch Code</strong> columns are optional.
                </p>
        </div>
    </div>
    <form action="{{ route('admin.elections.voters.import', $election) }}"
          method="POST" enctype="multipart/form-data"
          style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;padding-top:4px;">
        @csrf
        <div style="flex:1;min-width:200px;">
            <label class="form-label" style="display:block;margin-bottom:5px;">File <span style="color:#dc2626;">*</span></label>
            <input type="file" name="file" accept=".csv,.xlsx,.xls" class="form-input" style="padding:7px;" required>
        </div>
        <button type="submit" class="btn btn-primary">Upload &amp; Import</button>
    </form>
</div>
@endcan
{{-- Assign panel --}}
@can('assign.voters')
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">Assign Existing Voters to this Election</div>
            <p style="font-size:13px;color:var(--ink-3);margin-top:3px;">
                List of all validated voters in your organisation not yet assigned to <strong>{{ $election->name }}</strong>.
            </p>
            <p style="font-style:italic;font-size:13px;color:var(--ink-3);margin-top:3px;">
                NOTE: Any selected voters will be automatically validated by the system.
            </p>
        </div>
    </div>

    {{-- filter over the current page --}}
    <form method="GET" style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;" data-voter-filter>
        <input type="search" name="q" class="form-input" placeholder="Search this page…"
               value="{{ request('q') }}" style="width:200px;" data-filter-q>
        <input type="text" name="batch_code" class="form-input" placeholder="Batch code…"
               value="{{ request('batch_code') }}" style="width:160px;" data-filter-batch>
        <button type="submit" class="btn btn-secondary btn-sm" title="Search across every unassigned voter, not just this page">Search all</button>
        @if(request()->hasAny(['q','batch_code']))
        <a href="{{ route('admin.elections.voters.assign.view', $election) }}" class="btn btn-secondary btn-sm">Clear</a>
        @endif
    </form>

    <form method="POST" action="{{ route('admin.elections.voters.assign.store', $election) }}">
        @csrf

        {{-- Validate toggle + submit --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:10px;">
            <div style="display:flex;align-items:center;gap:18px;flex-wrap:wrap;">
                <label style="display:flex;align-items:center;gap:7px;font-size:13.5px;cursor:pointer;font-weight:600;">
                    <input type="checkbox" id="selectAll" style="width:15px;height:15px;accent-color:var(--primary);">
                    Select All
                </label>
            </div>
            <button type="submit" class="btn btn-primary" id="assignBtn" disabled style="opacity:.4;transition:opacity .15s;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                </svg>
                Assign Selected
            </button>
        </div>

        @if($voters->isEmpty())
        <div class="empty-state">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
            </svg>
            <div class="empty-title">No unassigned voters found</div>
            <p style="font-size:13px;">All voters in your organisation are already assigned, or none match your filter.</p>
        </div>
        @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th width="40"></th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Batch Code</th>
                        <th>Email Verified</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($voters as $voter)
                    <tr data-voter-row data-search="{{ strtolower($voter->email . ' ' . $voter->phone) }}" data-batch="{{ $voter->batch_code }}">
                        <td>
                            <input type="checkbox" name="voters[]" value="{{ $voter->id }}"
                                   class="voter-check"
                                   style="width:15px;height:15px;accent-color:var(--primary);">
                        </td>
                        <td style="font-weight:500;">{{ $voter->email ?? '—' }}</td>
                        <td style="color:var(--ink-3);">{{ $voter->phone ?? '—' }}</td>
                        <td>
                            @if($voter->batch_code)
                            <code style="font-size:11.5px;background:var(--surface);padding:2px 6px;border-radius:4px;">
                                {{ $voter->batch_code }}
                            </code>
                            @else
                            <span style="color:var(--ink-3);">—</span>
                            @endif
                        </td>
                        <td>
                            @if($voter->is_verified_email)
                                <span class="badge badge-running"><span class="badge-dot"></span>Yes</span>
                            @else
                                <span class="badge badge-cancelled"><span class="badge-dot"></span>No</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <p id="filterEmptyNote" style="display:none;text-align:center;padding:20px;color:var(--ink-3);font-size:13px;">
                No voters on this page match your filter. Try "Search all" to search every unassigned voter.
            </p>
        </div>
        <div style="padding-top:13px;">{{ $voters->links() }}</div>
        @endif
    </form>
</div>
@endcan

@endsection
