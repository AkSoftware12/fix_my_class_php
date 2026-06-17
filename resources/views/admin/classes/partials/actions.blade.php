<div class="btn-group btn-group-sm">
    @can('classes.edit')
        <button class="btn btn-outline-primary" onclick="editClass({{ $class->id }})" title="Edit"><i class="bi bi-pencil"></i></button>
    @endcan
    @can('classes.delete')
        <button class="btn btn-outline-danger" onclick="fmcDelete('{{ route('admin.classes.destroy', $class) }}')" title="Delete"><i class="bi bi-trash"></i></button>
    @endcan
</div>
