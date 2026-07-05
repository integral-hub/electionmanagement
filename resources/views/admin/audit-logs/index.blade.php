@extends('layouts.admin')
@section('page-title', 'Audit Logs')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title">System Activity Log</div>
        <span style="font-size:13px;color:var(--ink-3);">{{ $logs->total() }} entries</span>
    </div>

    @if($logs->isEmpty())
        <div class="empty-state">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <div class="empty-title">No activity logged</div>
        </div>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Causer</th>
                    <th>Action</th>
                    <th>Subject</th>
                    <th>Action Details</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td style="color:var(--ink-3);font-size:12px;white-space:nowrap;">
                        <span title="{{ $log->created_at }}">{{ $log->created_at->format('M j Y, H:i') }}</span>
                    </td>
                    <td style="font-size:13px;">{{ $log->causer?->name ?? 'System' }}</td>
                    <td>
                        <span style="font-size:12px;font-family:monospace;background:var(--surface);padding:2px 8px;border-radius:4px;color:var(--accent);">
                            {{ $log->description ?? $log->event ?? '—' }}
                        </span>
                    </td>
                    <td style="font-size:13px;color:var(--ink-2);">
                        {{ $log->subject_type ? class_basename($log->subject_type).' #'.$log->subject_id : '—' }}
                    </td>
                    <td style="font-size:12px;color:var(--ink-3);font-family:monospace;white-space:pre-wrap;">
                        {{ collect([
                            'IP: ' . data_get($log->properties, 'ip'),
                            'UA: ' . data_get($log->properties, 'user_agent'),
                            'OLD: ' . json_encode(data_get($log->properties, 'old'), JSON_UNESCAPED_SLASHES)
                        ])->filter()->implode(' || ') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="padding-top:16px;">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
