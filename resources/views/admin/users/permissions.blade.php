@extends('layouts.app')

@section('title', 'User Permissions')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Permissions: '.$user->name,
        'subtitle' => 'Direct permissions on top of role permissions (role: '.($user->roles->pluck('name')->join(', ') ?: 'none').')',
    ])

    <form method="POST" action="{{ route('admin.users.permissions.sync', $user) }}">
        @csrf
        @method('PUT')

        <div class="card mb-3">
            <div class="card-body d-flex flex-wrap align-items-center gap-2">
                <span class="small text-muted">
                    <span class="badge text-bg-info-subtle">Inherited</span> from role — cannot be removed here.
                    <span class="badge text-bg-primary-subtle">Direct</span> — granted specifically to this user.
                </span>
                <div class="ms-auto d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-light" onclick="$('.perm-direct:not(:disabled)').prop('checked', true)">Select all</button>
                    <button type="button" class="btn btn-sm btn-light" onclick="$('.perm-direct:not(:disabled)').prop('checked', false)">Clear all</button>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-check-lg me-1"></i>Save Permissions</button>
                </div>
            </div>
        </div>

        <div class="row g-3">
            @foreach ($permissions as $module => $modulePermissions)
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h3 class="h6 fw-bold text-capitalize mb-3">{{ str_replace('-', ' ', $module ?? 'other') }}</h3>
                            @foreach ($modulePermissions as $permission)
                                @php
                                    $inherited = in_array($permission->name, $viaRoles);
                                    $isDirect = in_array($permission->name, $direct);
                                @endphp
                                <div class="form-check">
                                    <input class="form-check-input perm-direct" type="checkbox"
                                           name="permissions[]" value="{{ $permission->name }}"
                                           id="perm-{{ $permission->id }}"
                                           @checked($inherited || $isDirect) @disabled($inherited)>
                                    <label class="form-check-label small d-flex align-items-center gap-1" for="perm-{{ $permission->id }}">
                                        {{ \Illuminate\Support\Str::after($permission->name, '.') }}
                                        @if ($inherited)
                                            <span class="badge text-bg-info-subtle" style="font-size:.6rem">role</span>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Permissions</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-light">Back to users</a>
        </div>
    </form>
@endsection
