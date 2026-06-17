<div class="btn-group btn-group-sm">
    @can('batches.view')
        <a class="btn btn-outline-secondary" href="{{ route('admin.batches.show', $batch) }}" title="View"><i class="bi bi-eye"></i></a>
        @can('students.view')
            <a class="btn btn-outline-info" href="{{ route('admin.students.id-cards.batch', $batch) }}" target="_blank" title="Print ID Cards"><i class="bi bi-printer"></i></a>
        @endcan
    @endcan
    @can('batches.edit')
        <a class="btn btn-outline-primary" href="{{ route('admin.batches.edit', $batch) }}" title="Edit / Assign"><i class="bi bi-pencil"></i></a>
    @endcan
    @can('batches.delete')
        <button class="btn btn-outline-danger" onclick="fmcDelete('{{ route('admin.batches.destroy', $batch) }}')" title="Delete"><i class="bi bi-trash"></i></button>
    @endcan
</div>
