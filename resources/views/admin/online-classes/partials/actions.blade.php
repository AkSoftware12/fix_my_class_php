<div class="btn-group btn-group-sm">
    @can('update', $onlineClass)
        <a class="btn btn-outline-primary" href="{{ route('admin.online-classes.edit', $onlineClass) }}" title="Edit"><i class="bi bi-pencil"></i></a>
    @endcan
    @can('delete', $onlineClass)
        <button class="btn btn-outline-danger" onclick="fmcDelete('{{ route('admin.online-classes.destroy', $onlineClass) }}')" title="Delete"><i class="bi bi-trash"></i></button>
    @endcan
</div>
