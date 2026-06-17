<div class="btn-group btn-group-sm">
    @can('leads.view')
        <a class="btn btn-outline-secondary" href="{{ route('admin.leads.show', $lead) }}" title="View / Follow-ups"><i class="bi bi-eye"></i></a>
    @endcan
    @can('leads.edit')
        <a class="btn btn-outline-primary" href="{{ route('admin.leads.edit', $lead) }}" title="Edit"><i class="bi bi-pencil"></i></a>
    @endcan
    @can('leads.delete')
        <button class="btn btn-outline-danger" onclick="fmcDelete('{{ route('admin.leads.destroy', $lead) }}')" title="Delete"><i class="bi bi-trash"></i></button>
    @endcan
</div>
