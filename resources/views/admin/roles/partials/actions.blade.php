<div class="btn-group btn-group-sm">
    @can('roles.edit')
        @if ($role->name !== 'Super Admin')
            <a class="btn btn-outline-primary" href="{{ route('admin.roles.edit', $role) }}" title="Edit permissions"><i class="bi bi-pencil"></i></a>
        @endif
    @endcan
    @can('roles.delete')
        @unless ($isProtected)
            <button class="btn btn-outline-danger" onclick="fmcDelete('{{ route('admin.roles.destroy', $role) }}')" title="Delete"><i class="bi bi-trash"></i></button>
        @endunless
    @endcan
</div>
