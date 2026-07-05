@extends('layouts.admin')
@section('page-title', 'Roles & Permissions')

@section('topbar-actions')
  @can('create.roles') <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">+ New Role</a> @endcan
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title">Roles</div>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Role Name</th><th>Permissions</th><th>Users</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($roles as $role)
                <tr>
                    <td style="font-weight:600;">{{ $role->name }}</td>
                    <td>
                        <span style="font-size:13px;color:var(--ink-3);">{{ $role->permissions->count() }} permissions</span>
                    </td>
                    <td>{{ $role->users->count() ?? '—' }}</td>
                    <td>
                        <div style="display:flex;gap:6px;">
                          @can('update', $role)  <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-secondary btn-sm">Edit</a> @endcan
                          @can('delete', $role)
                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm" onclick="return confirm('Delete role?')">Delete</button>
                            </form>
                          @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;color:var(--ink-3);padding:30px;">No roles defined yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding-top:16px;">{{ $roles->links() }}</div>
</div>
@endsection
