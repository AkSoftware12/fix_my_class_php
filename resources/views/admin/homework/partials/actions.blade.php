<div class="btn-group btn-group-sm">
    @can('homework.view')
        <a class="btn btn-outline-secondary" href="{{ route('admin.homework.show', $homework) }}" title="View / Submissions"><i class="bi bi-eye"></i></a>
    @endcan
    @can('update', $homework)
        <a class="btn btn-outline-primary" href="{{ route('admin.homework.edit', $homework) }}" title="Edit"><i class="bi bi-pencil"></i></a>
    @endcan
    @can('delete', $homework)
        <button class="btn btn-outline-danger" onclick="fmcDelete('{{ route('admin.homework.destroy', $homework) }}')" title="Delete"><i class="bi bi-trash"></i></button>
    @endcan
</div>
