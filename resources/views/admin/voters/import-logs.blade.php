@extends('layouts.admin')
@section('page-title','Import Logs')

@section('topbar-actions')
    <a href="{{ route('admin.elections.voters.index', $election) }}" class="btn btn-s btn-sm">← Voters</a>
    @can('assign.voters')
    <a href="{{ route('admin.elections.voters.assign.view', $election) }}" class="btn btn-primary btn-sm">+ Assign Voter</a>
    @endcan
@endsection

@section('content')

<div class="alert a-info" style="margin-bottom:16px;">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
    Import results (success/failure counts and row errors) are emailed to the staff member
    who uploaded each file once processing completes.
</div>

<div class="card">
    <div class="ch">
        <div class="ct">Import History — {{ $election->name }}</div>
        <span style="font-size:11.5px;color:var(--ink3);">{{ $logs->total() }} import(s)</span>
    </div>

    @if($logs->isEmpty())
    <div class="es">
        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
        <div class="et">No imports yet</div>
        <p style="font-size:12.5px;">Upload a CSV or XLSX file to bulk-import voters.</p>
    </div>
    @else
    <div class="tw">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>File</th>
                    <th>Batch Code</th>
                    <th>Rows Processed</th>
                    <th>Uploaded By</th>
                </tr>
            </thead>
            <tbody>
            @foreach($logs as $log)
            <tr>
                <td style="font-size:11.5px;color:var(--ink3);white-space:nowrap;">
                    {{ $log->created_at?->format('M j, Y H:i') }}
                </td>
                <td style="font-size:12.5px;font-family:monospace;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $log->file_name }}">
                    {{ $log->file_name ?? 'unknown' }}
                </td>
                <td>
                    @if($log->batch_code)
                    <code style="font-size:11.5px;background:var(--sur);padding:2px 6px;border-radius:4px;">{{ $log->batch_code }}</code>
                    @else
                    <span style="color:var(--ink3);">—</span>
                    @endif
                </td>
                <td style="font-weight:600;">{{ $log->total_records ?? 0 }}</td>
                <td style="font-size:12.5px;">{{ $log->uploader?->name ?? 'System' }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div style="padding-top:13px;">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
