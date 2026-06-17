<div class="btn-group btn-group-sm">
    @can('cities.edit')
        <button class="btn btn-outline-primary" onclick="editCity({{ $city->id }})" title="Edit"><i class="bi bi-pencil"></i></button>
    @endcan
    @can('cities.delete')
        <button class="btn btn-outline-danger" onclick="fmcDelete('{{ route('admin.cities.destroy', $city) }}')" title="Delete"><i class="bi bi-trash"></i></button>
    @endcan
</div>
