<div class="btn-group btn-group-sm">
    @can('chat.view')
        <a class="btn btn-outline-secondary" href="{{ route('admin.chat-rooms.show', $room) }}" title="Monitor"><i class="bi bi-eye"></i></a>
    @endcan
    @can('chat.edit')
        <button class="btn btn-outline-{{ $room->is_active ? 'warning' : 'success' }}"
                onclick="fmcPost('{{ route('admin.chat-rooms.toggle-status', $room) }}', '{{ $room->is_active ? 'Deactivate' : 'Activate' }} this room?')"
                title="{{ $room->is_active ? 'Deactivate' : 'Activate' }}">
            <i class="bi bi-{{ $room->is_active ? 'pause' : 'play' }}"></i>
        </button>
    @endcan
</div>
