<div class="btn-group btn-group-sm">
    @can('coachings.view')
        <a class="btn btn-outline-secondary" href="{{ route('admin.coachings.show', $coaching) }}" title="View"><i class="bi bi-eye"></i></a>
    @endcan
    @can('coachings.edit')
        <a class="btn btn-outline-primary" href="{{ route('admin.coachings.edit', $coaching) }}" title="Edit"><i class="bi bi-pencil"></i></a>
    @endcan
    @can('coachings.delete')
        <button class="btn btn-outline-danger" onclick="fmcDelete('{{ route('admin.coachings.destroy', $coaching) }}')" title="Delete"><i class="bi bi-trash"></i></button>
    @endcan
</div>
