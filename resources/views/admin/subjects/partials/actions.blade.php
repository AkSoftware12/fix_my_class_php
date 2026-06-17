<div class="btn-group btn-group-sm">
    @can('subjects.edit')
        <button class="btn btn-outline-primary" onclick="editSubject({{ $subject->id }})" title="Edit"><i class="bi bi-pencil"></i></button>
    @endcan
    @can('subjects.delete')
        <button class="btn btn-outline-danger" onclick="fmcDelete('{{ route('admin.subjects.destroy', $subject) }}')" title="Delete"><i class="bi bi-trash"></i></button>
    @endcan
</div>
