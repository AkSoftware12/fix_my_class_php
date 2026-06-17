@extends('layouts.app')

@section('title', 'Chat: '.$room->name)

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Chat Room: '.$room->name,
        'subtitle' => ucfirst($room->type).' room · '.$room->members->count().' members',
        'actions' => '<a href="'.route('admin.chat-rooms.show', [$room, 'flagged' => request('flagged') ? null : 1]).'" class="btn btn-outline-'.(request('flagged') ? 'secondary' : 'warning').'">'
            .'<i class="bi bi-flag me-1"></i>'.(request('flagged') ? 'Show all' : 'Flagged only').'</a>',
    ])

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-3"><i class="bi bi-chat-dots me-1 text-primary"></i> Messages</h3>

                    @forelse ($messages as $message)
                        <div class="d-flex gap-2 py-2 {{ ! $loop->last ? 'border-bottom' : '' }}">
                            <img src="{{ $message->user?->avatar_url }}" class="avatar-sm" alt="">
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-semibold small">{{ $message->user?->name }}</span>
                                    <span class="text-muted" style="font-size:.72rem">{{ $message->created_at->format('d M Y H:i') }}</span>
                                    @if ($message->is_flagged)
                                        <span class="badge text-bg-warning-subtle">Flagged</span>
                                    @endif
                                </div>
                                @if ($message->deleted_by_admin_at)
                                    <div class="small text-muted fst-italic">Message removed by moderator.</div>
                                @else
                                    <div class="small">{{ $message->body }}</div>
                                    @if ($message->attachment_url)
                                        <a class="small" href="{{ $message->attachment_url }}" target="_blank"><i class="bi bi-paperclip"></i> Attachment</a>
                                    @endif
                                @endif
                            </div>
                            @unless ($message->deleted_by_admin_at)
                                <div class="btn-group btn-group-sm align-self-start">
                                    @can('chat.edit')
                                        <button class="btn btn-outline-warning" title="Toggle flag"
                                                onclick="fmcPost('{{ route('admin.chat-rooms.messages.flag', [$room, $message]) }}'); setTimeout(() => location.reload(), 900)">
                                            <i class="bi bi-flag"></i>
                                        </button>
                                    @endcan
                                    @can('chat.delete')
                                        <button class="btn btn-outline-danger" title="Remove message"
                                                onclick="fmcDelete('{{ route('admin.chat-rooms.messages.remove', [$room, $message]) }}'); setTimeout(() => location.reload(), 1200)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endcan
                                </div>
                            @endunless
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No messages {{ request('flagged') ? 'flagged' : 'yet' }}.</p>
                    @endforelse

                    <div class="mt-3">
                        {{ $messages->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-3"><i class="bi bi-people me-1 text-primary"></i> Members & Activity</h3>
                    @forelse ($room->members as $member)
                        <div class="d-flex align-items-center gap-2 py-2 {{ ! $loop->last ? 'border-bottom' : '' }}">
                            <img src="{{ $member->avatar_url }}" class="avatar-sm" alt="">
                            <div class="flex-grow-1">
                                <div class="fw-semibold small">{{ $member->name }}</div>
                                <div class="text-muted" style="font-size:.72rem">
                                    Last read: {{ $member->pivot->last_read_at ? \Carbon\Carbon::parse($member->pivot->last_read_at)->diffForHumans() : 'never' }}
                                </div>
                            </div>
                            {!! status_badge($member->is_active) !!}
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No members.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
