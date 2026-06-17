<div class="btn-group btn-group-sm">
    @can('users.edit')
        <a class="btn btn-outline-primary" href="{{ route('admin.users.edit', $user) }}" title="Edit"><i class="bi bi-pencil"></i></a>
        <button class="btn btn-outline-{{ $user->is_active ? 'warning' : 'success' }}"
                onclick="fmcPost('{{ route('admin.users.toggle-status', $user) }}', '{{ $user->is_active ? 'Deactivate' : 'Activate' }} {{ $user->name }}?')"
                title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
            <i class="bi bi-{{ $user->is_active ? 'pause' : 'play' }}"></i>
        </button>
        <button class="btn btn-outline-secondary" onclick="resetUserPassword({{ $user->id }}, '{{ e(addslashes($user->name)) }}')" title="Reset password">
            <i class="bi bi-key"></i>
        </button>
    @endcan
    @can('users.assign')
        <a class="btn btn-outline-info" href="{{ route('admin.users.permissions', $user) }}" title="Permissions"><i class="bi bi-shield-check"></i></a>
    @endcan
    @can('users.delete')
        <button class="btn btn-outline-danger" onclick="fmcDelete('{{ route('admin.users.destroy', $user) }}')" title="Delete"><i class="bi bi-trash"></i></button>
    @endcan
</div>
