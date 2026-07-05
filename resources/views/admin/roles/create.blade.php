@extends('layouts.admin')
@section('page-title', isset($role) ? 'Edit Role' : 'Create Role')

@section('topbar-actions')
    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-sm">← Roles</a>
@endsection

@section('content')
<div style="max-width:700px;">
    <div class="card">
        <div style="margin-bottom:24px;">
            <h2 style="font-size:18px;font-weight:700;">{{ isset($role) ? 'Edit Role: '.$role->name : 'New Role' }}</h2>
        </div>

        <form action="{{ isset($role) ? route('admin.roles.update', $role) : route('admin.roles.store') }}" method="POST" class="form-section">
            @csrf
            @if(isset($role)) @method('PUT') @endif

            <div class="form-group">
                <label class="form-label">Role Name <span style="color:var(--danger)">*</span></label>
                <input type="text" name="name" class="form-input {{ $errors->has('name')?'error':'' }}" value="{{ old('name',$role->name??'') }}" placeholder="e.g. Election Manager" required>
                @error('name') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="form-label" style="margin-bottom:12px;display:block;">Permissions</label>
                <div style="display:flex;justify-content:flex-end;gap:8px;margin-bottom:10px;">
                    <button type="button" onclick="toggleAll(true)" class="btn btn-secondary btn-sm">Select All</button>
                    <button type="button" onclick="toggleAll(false)" class="btn btn-secondary btn-sm">Clear All</button>
                </div>

    @php
        $actionOrder = [
            'view' => 1, 'create' => 2, 'update' => 3, 'delete' => 4, 'assign' => 5,
            'import' => 5, 'export' => 5, 'approve' => 6, 'reject' => 6, 'reset' => 7, 'restore' => 7,
        ];

        $grouped = collect($permissions)
            ->groupBy(fn($p) => explode('.', $p->value)[1] ?? 'other')
            ->map(function ($items) use ($actionOrder) {
                return $items->sortBy(function ($item) use ($actionOrder) {
                    $action = explode('.', $item->value)[0] ?? '';

                    return $actionOrder[$action] ?? 999;
                });
        });

        $activePerms = isset($role)
            ? $role->permissions->pluck('name')->toArray()
            : [];
    @endphp

                @foreach($grouped as $group => $perms)
                <div style="margin-bottom:16px;border:1px solid var(--border);border-radius:10px;overflow:hidden;">
                    <div style="background:var(--surface);padding:10px 14px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--ink-3);">{{ ucfirst($group) }}</div>
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:0;">
                        @foreach($perms as $perm)
                        <label style="display:flex;align-items:center;gap:8px;padding:10px 14px;cursor:pointer;font-size:13px;border-top:1px solid var(--border);">
                            <input type="checkbox" name="permissions[]" value="{{ $perm->value }}" class="perm-check"
                                {{ in_array($perm->value, old('permissions', $activePerms)) ? 'checked' : '' }}
                                style="accent-color:var(--accent);width:15px;height:15px;">
                            {{ str_replace(['.','_'], [' ', ' '], $perm->value) }}
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            <div style="display:flex;gap:10px;padding-top:8px;border-top:1px solid var(--border);">
                <button type="submit" class="btn btn-primary">{{ isset($role) ? 'Update Role' : 'Create Role' }}</button>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
function toggleAll(check) {
    document.querySelectorAll('.perm-check').forEach(c => c.checked = check);
}
</script>
@endsection
