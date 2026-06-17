<div class="btn-group btn-group-sm">
    @can('teachers.view')
        <a class="btn btn-outline-secondary" href="{{ route('admin.teachers.show', $teacher) }}" title="View"><i class="bi bi-eye"></i></a>
    @endcan
    @can('teachers.edit')
        <a class="btn btn-outline-primary" href="{{ route('admin.teachers.edit', $teacher) }}" title="Edit"><i class="bi bi-pencil"></i></a>
    @endcan
    @can('teachers.delete')
        <button class="btn btn-outline-danger" onclick="fmcDelete('{{ route('admin.teachers.destroy', $teacher) }}')" title="Delete"><i class="bi bi-trash"></i></button>
    @endcan
</div>
