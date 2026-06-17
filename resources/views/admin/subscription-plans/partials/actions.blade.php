<div class="btn-group btn-group-sm">
    @can('subscriptions.edit')
        <button class="btn btn-outline-primary" onclick="editPlan({{ $plan->id }})" title="Edit"><i class="bi bi-pencil"></i></button>
    @endcan
    @can('subscriptions.delete')
        <button class="btn btn-outline-danger" onclick="fmcDelete('{{ route('admin.subscription-plans.destroy', $plan) }}')" title="Delete"><i class="bi bi-trash"></i></button>
    @endcan
</div>
