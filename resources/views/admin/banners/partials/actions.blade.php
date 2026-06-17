<div class="btn-group btn-group-sm">
    @can('banners.edit')
        <button class="btn btn-outline-primary" onclick="editBanner({{ $banner->id }})" title="Edit"><i class="bi bi-pencil"></i></button>
    @endcan
    @can('banners.delete')
        <button class="btn btn-outline-danger" onclick="fmcDelete('{{ route('admin.banners.destroy', $banner) }}')" title="Delete"><i class="bi bi-trash"></i></button>
    @endcan
</div>
