@extends('layouts.admin')
@section('page-title', 'Voter Status')

@push('styles')
    @vite('resources/css/admin-voter-show.css')
@endpush

@section('page-subtitle')
    @php
        $pivotStatus = $pivot?->status ?? 'pending';

        $statusMap = [
            'validated' => ['b-running', 'Validated'],
            'banned'    => ['b-rejected', 'Banned'],
            'pending'   => ['b-scheduled', 'Pending'],
        ];

        [$badgeClass, $statusLabel] = $statusMap[$pivotStatus] ?? ['b-draft', ucfirst($pivotStatus)];
    @endphp

    <div class="voter-field-inline">
        <span class="badge {{ $badgeClass }}">
            <span class="bd"></span>{{ $statusLabel }}
        </span>
    </div>
@endsection

@section('topbar-actions')
    <a href="{{ route('admin.elections.voters.index', $election) }}" class="btn btn-s btn-sm">← Voters</a>

    @can('update', $voter)
    <a href="{{ route('admin.elections.voters.edit', [$election, $voter]) }}" class="btn btn-p btn-sm">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        Edit
    </a>
    @endcan

@endsection

@section('content')

{{-- Status bar --}}
<div class="voter-status-bar">

    @if($pivot?->validated_at)
        <span class="voter-status-note">
            {{ ucfirst($pivot?->status) }} by
            <strong>
                @if($pivot->validated_by === auth()->id())
                    You
                @else
                    {{ $pivot->validator?->name ?? 'Unknown User' }}
                    @if($pivot->validator?->getRoleNames()->isNotEmpty())
                        ({{ ucfirst($pivot->validator->getRoleNames()->first()) }})
                    @endif
                @endif
            </strong>
            on {{ \Carbon\Carbon::parse($pivot->validated_at)->format('M j, Y \a\t H:i') }}
        </span>
    @endif

    @if($pivotStatus !== 'validated')
    @can('approve', $voter)
    <form action="{{ route('admin.elections.voters.approve', [$election, $voter]) }}" method="POST" style="display:inline;">
        @csrf @method('PATCH')
        <button type="submit" class="btn btn-sg btn-sm">
            &#10003;
            Approve
        </button>
    </form>
    @endcan
    @endif

    @if($pivotStatus !== 'banned')
    @can('reject', $voter)
    <form action="{{ route('admin.elections.voters.reject', [$election, $voter]) }}" method="POST" style="display:inline;">
        @csrf @method('PATCH')
        <button type="submit" class="btn btn-d btn-sm"
                onclick="return confirm('Ban this voter from the election?')">
            &#x26A0;
            Ban
        </button>
    </form>
    @endcan
    @endif

    @can('delete', $voter)
    <form action="{{ route('admin.elections.voters.destroy', [$election, $voter]) }}" method="POST" style="display:inline;">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-d btn-sm"
                onclick="return confirm('Remove this voter from the election?')">
            Remove
        </button>
    </form>
    @endcan
</div>

<div class="voter-detail-grid">

    {{-- Left column: core identity  --}}
    <div class="voter-detail-col">
        {{-- Identity card --}}
        <div class="card">
            <div class="ch" style="margin-bottom:14px;"><div class="ct">Identity</div></div>

            <div class="voter-identity-row">
                <div class="voter-identity-avatar">
                    {{ strtoupper(substr($voter->email ?? $voter->phone ?? 'V', 0, 1)) }}
                </div>
                <div>
                    <div class="voter-identity-name">{{ $voter->email ?? $voter->phone ?? 'Unknown' }}</div>
                </div>
            </div>

            {{-- Email --}}
            <div class="voter-field-row">
                <div class="voter-field-label">Email</div>
                @if($voter->email)
                <div class="voter-field-inline">
                    <span class="voter-field-value">{{ $voter->email }}</span>
                    @if($voter->is_verified_email)
                        <span class="voter-verified-pill">&#10003; Verified</span>
                    @else
                        <span class="voter-unverified-pill">Unverified</span>
                    @endif
                </div>
                @else
                <span class="voter-field-value is-muted">—</span>
                @endif
            </div>

            {{-- Phone --}}
            <div class="voter-field-row">
                <div class="voter-field-label">Phone</div>
                <div class="voter-field-value">{{ $voter->phone ?? '—' }}</div>
            </div>

            {{-- Batch code — from voters table --}}
            <div class="voter-field-row">
                <div class="voter-field-label">Batch Code</div>
                @if($voter->batch_code)
                <code class="voter-batch-code">{{ $voter->batch_code }}</code>
                @else
                <span class="voter-field-value is-muted">—</span>
                @endif
            </div>

            {{-- Last login --}}
            <div class="voter-field-row">
                <div class="voter-field-label">Last Login</div>
                <div class="voter-field-value">{{ $voter->last_login_at?->format('M j, Y H:i') ?? '—' }}</div>
            </div>

            {{-- Registered --}}
            <div class="voter-field-row">
                <div class="voter-field-label">Registered</div>
                <div class="voter-field-value is-secondary">{{ $voter->created_at->format('M j, Y \a\t H:i') }}</div>
            </div>
        </div>

        {{-- Election status card --}}
        <div class="card">
            <div class="ch" style="margin-bottom:14px;"><div class="ct">Election Details</div></div>
            <div class="voter-election-fields">
                <div>
                    <div class="voter-field-label">Election</div>
                    <div class="voter-field-value" style="font-weight:500;">{{ $election->name }}</div>
                </div>
                <div>
                    <div class="voter-field-label">Status</div>
                    <span class="badge {{ $badgeClass }} voter-badge-lg"><span class="bd"></span>{{ $statusLabel }}</span>
                </div>
                <div>
                <div class="voter-field-label">Voting Status</div>
                    @switch($voter->voteStatus($election))
                        @case('voted')
                            <span class="badge b-completed voter-voted-badge">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="11" height="11">
                                    <polyline points="9,11 12,14 22,4"/>
                                </svg>
                                Voted
                            </span>
                            @break

                        @case('revoked')
                            <span class="badge b-rejected voter-voted-badge">
                                Revoked
                            </span>
                            @break

                        @default
                            <span class="badge b-draft voter-voted-badge">
                                Nil
                            </span>
                        @endswitch
                    </div>
                </div>
            </div>
        </div>

    {{-- ── Right column: registration data  --}}
    <div class="voter-detail-col">

        {{-- voter_data fields --}}
        @if(!empty($voter->voter_data))
        <div class="card">
            <div class="ch" style="margin-bottom:14px;">
                <div class="ct">Bio Data</div>
            </div>
            <div class="voter-data-grid">
                @foreach($voter->voter_data as $fieldName => $value)
                @php
                    $def   = $fieldDefs->get($fieldName);
                    $label = $def['label'] ?? ucwords(str_replace('_', ' ', $fieldName));
                    $type  = $def['field_type'] ?? 'text';
                    $isFile = $type === 'file';
                    // Files and long free-text answers get the full row width; short values stay compact.
                    $isWide = $isFile || (is_string($value) && strlen($value) > 60);
                @endphp
                <div class="voter-data-field {{ $isWide ? 'voter-data-field--wide' : '' }}">
                    <div class="voter-data-label">{{ $label }}</div>
                    <div>
                        @if($isFile && $value)
                            @php
                                $ext = strtolower($value['format']);
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png']);
                                $isPdf = $ext === 'pdf';
                                $previewUrl = route('admin.elections.voters.file-preview', [$election, $voter, $fieldName]);
                            @endphp
                            <div class="voter-file-preview">
                                @if($isImage)
                                <a href="{{ $previewUrl }}" target="_blank" rel="noopener" class="voter-file-image-link">
                                    <img src="{{ $previewUrl }}" alt="{{ $label }}">
                                </a>
                                @elseif($isPdf)
                                <div class="voter-file-pdf-wrap">
                                    <iframe src="{{ $previewUrl }}" title="{{ $label }}"></iframe>
                                </div>
                                @else
                                <div class="voter-file-generic">
                                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" width="26" height="26"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <div>
                                        <div class="voter-file-generic-name">{{ $filename ?? $label }}</div>
                                        <div class="voter-file-generic-ext">{{ strtoupper($ext) }}</div>
                                    </div>
                                </div>
                                @endif
                                <div class="voter-file-actions">
                                    <a href="{{ $previewUrl }}" target="_blank" rel="noopener" class="btn btn-s btn-sm">
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="12" height="12"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        Open
                                    </a>
                                </div>
                            </div>

                        @elseif(is_array($value))
                            <div class="voter-field-value">{{ implode(', ', $value) }}</div>

                        @elseif(in_array($value, [true, false, '1', '0', 1, 0], true))
                            <span class="badge {{ $value ? 'b-running' : 'b-draft' }}" style="font-size:11px;">
                                {{ $value ? 'Yes' : 'No' }}
                            </span>

                        @else
                            <div class="voter-field-value" style="font-size:13.5px;word-break:break-word;">{{ $value ?: '—' }}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- voter_unique_data fields --}}
        @if($voter->uniqueData->isNotEmpty())
        <div class="card">
            <div class="ch" style="margin-bottom:14px;">
                <div class="ct">Additional Information</div>
            </div>
            <div class="voter-unique-grid">
                @foreach($voter->uniqueData as $ud)
                @php
                    $def   = $fieldDefs->get($ud->field_name);
                    $label = $def['label'] ?? ucwords(str_replace('_', ' ', $ud->field_name));
                @endphp
                <div class="voter-unique-field">
                    <div class="voter-unique-label">{{ $label }}</div>
                    <div class="voter-unique-value">
                        <span>{{ $ud->value }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Empty state --}}
        @if(empty($voter->voter_data) && $voter->uniqueData->isEmpty())
        <div class="card">
            <div class="es" style="padding:28px;">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <div class="et">Bio data not supplied or required</div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection