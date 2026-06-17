@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Notifications',
        'actions' => '<button class="btn btn-outline-primary" onclick="$.post(\''.route('admin.notifications.read-all').'\').done(() => location.reload())"><i class="bi bi-check2-all me-1"></i> Mark all read</button>',
    ])

    <div class="card">
        <div class="card-body">
            @forelse ($notifications as $notification)
                <div class="d-flex gap-3 py-3 {{ ! $loop->last ? 'border-bottom' : '' }} {{ $notification->read_at ? '' : 'bg-body-secondary rounded px-2' }}">
                    <div class="stat-icon text-bg-primary-subtle" style="width:40px;height:40px;border-radius:.6rem;display:grid;place-items:center">
                        <i class="bi bi-bell"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">{{ $notification->data['title'] ?? 'Notification' }}</div>
                        <div class="text-muted small">{{ $notification->data['body'] ?? '' }}</div>
                        <div class="text-muted" style="font-size:.72rem">{{ $notification->created_at->diffForHumans() }}</div>
                    </div>
                    <div class="d-flex gap-2 align-items-start">
                        @if (! empty($notification->data['url']))
                            <a href="{{ $notification->data['url'] }}" class="btn btn-sm btn-outline-primary">Open</a>
                        @endif
                        @if (! $notification->read_at)
                            <button class="btn btn-sm btn-outline-secondary"
                                    onclick="$.post('{{ route('admin.notifications.read', $notification->id) }}').done(() => location.reload())">
                                Mark read
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-muted mb-0">No notifications yet.</p>
            @endforelse

            <div class="mt-3">
                {{ $notifications->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
