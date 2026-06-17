<div class="btn-group btn-group-sm">
    @can('notices.view')
        <a class="btn btn-outline-secondary" href="{{ route('admin.notices.show', $notice) }}" title="View"><i class="bi bi-eye"></i></a>
    @endcan
    @can('update', $notice)
        <a class="btn btn-outline-primary" href="{{ route('admin.notices.edit', $notice) }}" title="Edit"><i class="bi bi-pencil"></i></a>
    @endcan
    @can('delete', $notice)
        <button class="btn btn-outline-danger" onclick="fmcDelete('{{ route('admin.notices.destroy', $notice) }}')" title="Delete"><i class="bi bi-trash"></i></button>
    @endcan
</div>
