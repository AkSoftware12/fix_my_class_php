<div class="btn-group btn-group-sm">
    @can('study-materials.view')
        <a class="btn btn-outline-secondary" href="{{ route('admin.study-materials.download', $material) }}" title="Download"><i class="bi bi-download"></i></a>
    @endcan
    @can('update', $material)
        <a class="btn btn-outline-primary" href="{{ route('admin.study-materials.edit', $material) }}" title="Edit"><i class="bi bi-pencil"></i></a>
    @endcan
    @can('delete', $material)
        <button class="btn btn-outline-danger" onclick="fmcDelete('{{ route('admin.study-materials.destroy', $material) }}')" title="Delete"><i class="bi bi-trash"></i></button>
    @endcan
</div>
