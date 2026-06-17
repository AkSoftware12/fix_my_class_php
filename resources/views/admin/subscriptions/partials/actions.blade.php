<div class="btn-group btn-group-sm">
    @can('subscriptions.edit')
        <button class="btn btn-outline-primary" onclick="editSubscription({{ $subscription->id }})" title="Edit"><i class="bi bi-pencil"></i></button>
        @if ($subscription->status === 'active')
            <button class="btn btn-outline-warning" onclick="fmcPost('{{ route('admin.subscriptions.cancel', $subscription) }}', 'Cancel this subscription?')" title="Cancel"><i class="bi bi-x-circle"></i></button>
        @endif
    @endcan
    @can('subscriptions.delete')
        <button class="btn btn-outline-danger" onclick="fmcDelete('{{ route('admin.subscriptions.destroy', $subscription) }}')" title="Delete"><i class="bi bi-trash"></i></button>
    @endcan
</div>
