<div class="btn-group btn-group-sm">
    @can('students.view')
        <a class="btn btn-outline-secondary" href="{{ route('admin.students.show', $student) }}" title="Profile"><i class="bi bi-eye"></i></a>
        <a class="btn btn-outline-info" href="{{ route('admin.students.id-card', $student) }}" title="ID Card"><i class="bi bi-person-badge"></i></a>
    @endcan
    @can('students.edit')
        <a class="btn btn-outline-primary" href="{{ route('admin.students.edit', $student) }}" title="Edit"><i class="bi bi-pencil"></i></a>
    @endcan
    @can('students.delete')
        <button class="btn btn-outline-danger" onclick="fmcDelete('{{ route('admin.students.destroy', $student) }}')" title="Delete"><i class="bi bi-trash"></i></button>
    @endcan
</div>
