@php($role = $role ?? null)
@php($isProtected = $role && in_array($role->name, ['Super Admin', 'City Admin', 'Coaching Admin', 'Branch Admin', 'Teacher', 'Student']))

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label class="form-label required" for="name">Role Name</label>
        <input type="text" id="name" name="name" maxlength="100" required {{ $isProtected ? 'readonly' : '' }}
               class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $role?->name) }}">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if ($isProtected)
            <div class="form-text">System role names cannot be changed.</div>
        @endif
    </div>
</div>

<div class="d-flex gap-2 mb-3">
    <button type="button" class="btn btn-sm btn-light" onclick="$('.perm-check').prop('checked', true)">Select all</button>
    <button type="button" class="btn btn-sm btn-light" onclick="$('.perm-check').prop('checked', false)">Clear all</button>
</div>

<div class="row g-3">
    @foreach ($permissions as $module => $modulePermissions)
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 border">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <h3 class="h6 fw-bold text-capitalize mb-0">{{ str_replace('-', ' ', $module ?? 'other') }}</h3>
                        <div class="form-check ms-auto mb-0">
                            <input type="checkbox" class="form-check-input module-toggle" data-module="{{ $module }}">
                        </div>
                    </div>
                    @foreach ($modulePermissions as $permission)
                        <div class="form-check">
                            <input class="form-check-input perm-check module-{{ $module }}" type="checkbox"
                                   name="permissions[]" value="{{ $permission->name }}" id="perm-{{ $permission->id }}"
                                   @checked(in_array($permission->name, old('permissions', $assigned)))>
                            <label class="form-check-label small" for="perm-{{ $permission->id }}">
                                {{ \Illuminate\Support\Str::after($permission->name, '.') }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ $role ? 'Update Role' : 'Create Role' }}</button>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-light">Cancel</a>
</div>

@push('scripts')
<script>
    $('.module-toggle').on('change', function () {
        $(`.module-${$(this).data('module')}`).prop('checked', this.checked);
    });
</script>
@endpush
