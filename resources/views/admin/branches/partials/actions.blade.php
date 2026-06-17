<div class="btn-group btn-group-sm">
    @can('branches.edit')
        <button class="btn btn-outline-primary" onclick="editBranch({{ $branch->id }})" title="Edit"><i class="bi bi-pencil"></i></button>
    @endcan
    @can('branches.delete')
        <button class="btn btn-outline-danger" onclick="fmcDelete('{{ route('admin.branches.destroy', $branch) }}')" title="Delete"><i class="bi bi-trash"></i></button>
    @endcan
</div>
