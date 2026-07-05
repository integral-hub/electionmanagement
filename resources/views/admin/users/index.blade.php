@extends('layouts.admin')
@section('page-title', 'Staff / Users')

@section('topbar-actions')
    @can('create.users')
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16"><path d="M12 5v14M5 12h14"/></svg>
        Invite Staff
    </a>
    @endcan
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title">Team Members</div>
        <span style="font-size:13px;color:var(--ink-3);">{{ $users->total() }} members</span>
    </div>

    @if($users->isEmpty())
        <div class="empty-state">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
            <div class="empty-title">No staff yet</div>
          @can('create.users')  <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm" style="margin-top:12px;">Invite Someone</a> @endcan
        </div>
    @else
    <div class="table-wrap">
        <table>
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Joined</th><th>Actions</th></tr></thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div class="user-avatar" style="width:32px;height:32px;font-size:12px;">
                                @if($url = data_get($user, 'profile_photo_path.url'))
                                <img src="{{ $url }}" alt="{{ $user->name }}">
                                @endif
                            </div>
                            <span style="font-weight:500;">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td style="color:var(--ink-3);">{{ $user->email }}</td>
                    <td>
                        @foreach($user->roles as $role)
                            <span class="badge badge-scheduled">{{ $role->name }}</span>
                        @endforeach
                    </td>
                    <td style="color:var(--ink-3);font-size:13px;">{{ $user->created_at->format('M j, Y') }}</td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            @can('update', $user) <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-secondary btn-sm">Edit</a> @endcan
                            @can('delete', $user)
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm" onclick="return confirm('Remove user?')">Remove</button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="padding-top:16px;">{{ $users->links() }}</div>
    @endif
</div>
@endsection
