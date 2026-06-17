@extends('layouts.app')

@section('title', $notice->title)

@section('content')
    @include('admin.partials.page-header', [
        'title' => $notice->title,
        'subtitle' => 'Published by '.($notice->creator?->name ?? '—'),
        'actions' => auth()->user()->can('update', $notice)
            ? '<a href="'.route('admin.notices.edit', $notice).'" class="btn btn-primary"><i class="bi bi-pencil me-1"></i> Edit</a>'
            : '',
    ])

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @include('admin.notices.partials.type', ['type' => $notice->type])
                        <span class="badge text-bg-secondary-subtle">{{ ucfirst($notice->visibility) }}</span>
                        <span class="badge text-bg-secondary-subtle">Audience: {{ ucfirst($notice->audience) }}</span>
                        {!! status_badge($notice->is_active) !!}
                    </div>
                    <div>{!! nl2br(e($notice->body)) !!}</div>
                    @if ($notice->attachment_url)
                        <hr>
                        <a href="{{ $notice->attachment_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-paperclip me-1"></i>View attachment
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-3"><i class="bi bi-clock me-1 text-primary"></i> Schedule</h3>
                    <dl class="row small mb-0">
                        <dt class="col-5">Publish at</dt><dd class="col-7">{{ $notice->publish_at?->format('d M Y H:i') ?: 'Immediately' }}</dd>
                        <dt class="col-5">Expires</dt><dd class="col-7">{{ $notice->expires_at?->format('d M Y') ?: 'Never' }}</dd>
                    </dl>
                    <hr>
                    <h4 class="small fw-bold text-muted text-uppercase">Targets</h4>
                    @forelse ($notice->targets as $target)
                        <span class="badge text-bg-primary-subtle me-1 mb-1">{{ ucfirst($target->target_type) }}: {{ $target->target_name }}</span>
                    @empty
                        <span class="text-muted small">Public — no specific targets.</span>
                    @endforelse
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-3"><i class="bi bi-check2-all me-1 text-primary"></i> Read Tracking ({{ $notice->reads->count() }})</h3>
                    <div style="max-height:320px;overflow:auto">
                        @forelse ($notice->reads as $read)
                            <div class="d-flex justify-content-between small py-1 {{ ! $loop->last ? 'border-bottom' : '' }}">
                                <span>{{ $read->user?->name }}</span>
                                <span class="text-muted">{{ $read->read_at->diffForHumans() }}</span>
                            </div>
                        @empty
                            <p class="text-muted small mb-0">No reads recorded yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
