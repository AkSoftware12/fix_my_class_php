@php($user = $user ?? null)

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label required" for="name">Full Name</label>
        <input type="text" id="name" name="name" maxlength="255" required
               class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user?->name) }}">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label required" for="email">Email</label>
        <input type="email" id="email" name="email" maxlength="255" required
               class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user?->email) }}">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="mobile">Mobile</label>
        <input type="text" id="mobile" name="mobile" maxlength="20"
               class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile', $user?->mobile) }}">
        @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label required" for="role">Role</label>
        <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
            <option value="">Select role…</option>
            @foreach ($roles as $role)
                <option value="{{ $role }}" @selected(old('role', $user?->roles->first()?->name) === $role)>{{ $role }}</option>
            @endforeach
        </select>
        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4" id="cityField">
        <label class="form-label" for="city_id">City <span class="text-muted">(City Admin)</span></label>
        <select id="city_id" name="city_id" class="form-select select2 @error('city_id') is-invalid @enderror">
            <option value="">Select city…</option>
            @foreach ($cities as $city)
                <option value="{{ $city->id }}" @selected(old('city_id', $user?->city_id) == $city->id)>{{ $city->name }}, {{ $city->state }}</option>
            @endforeach
        </select>
        @error('city_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4" id="coachingField">
        <label class="form-label" for="coaching_id">Coaching</label>
        <select id="coaching_id" name="coaching_id" class="form-select select2 @error('coaching_id') is-invalid @enderror">
            <option value="">Select coaching…</option>
            @foreach ($coachings as $coaching)
                <option value="{{ $coaching->id }}" @selected(old('coaching_id', $user?->coaching_id) == $coaching->id)>{{ $coaching->name }}</option>
            @endforeach
        </select>
        @error('coaching_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4" id="branchField">
        <label class="form-label" for="branch_id">Branch</label>
        <select id="branch_id" name="branch_id" class="form-select select2 @error('branch_id') is-invalid @enderror"
                data-selected="{{ old('branch_id', $user?->branch_id) }}">
            <option value="">Select branch…</option>
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" data-coaching="{{ $branch->coaching_id }}"
                        @selected(old('branch_id', $user?->branch_id) == $branch->id)>{{ $branch->name }}</option>
            @endforeach
        </select>
        @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label {{ $user ? '' : 'required' }}" for="password">
            Password {{ $user ? '(leave blank to keep current)' : '' }}
        </label>
        <input type="password" id="password" name="password" autocomplete="new-password"
               class="form-control @error('password') is-invalid @enderror" {{ $user ? '' : 'required' }}>
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label {{ $user ? '' : 'required' }}" for="password_confirmation">Confirm Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" {{ $user ? '' : 'required' }}>
    </div>

    <div class="col-md-6">
        <label class="form-label" for="avatar">Avatar <span class="text-muted">(JPG/PNG, max 2&nbsp;MB)</span></label>
        <input type="file" id="avatar" name="avatar" accept="image/*"
               class="form-control @error('avatar') is-invalid @enderror">
        @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                   @checked(old('is_active', $user?->is_active ?? true))>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ $user ? 'Update User' : 'Create User' }}</button>
    <a href="{{ route('admin.users.index') }}" class="btn btn-light">Cancel</a>
</div>

@push('scripts')
<script>
    // Show only relevant tenancy fields for the chosen role.
    function syncRoleFields() {
        const role = $('#role').val();
        $('#cityField').toggle(role === 'City Admin');
        $('#coachingField').toggle(['Coaching Admin', 'Branch Admin', 'Teacher', 'Student'].includes(role));
        $('#branchField').toggle(['Branch Admin', 'Teacher', 'Student'].includes(role));
    }
    $('#role').on('change', syncRoleFields);
    syncRoleFields();

    // Filter branches by chosen coaching.
    $('#coaching_id').on('change', function () {
        const coachingId = String($(this).val() || '');
        const selected = String($('#branch_id').data('selected') || '');
        $('#branch_id option').each(function () {
            const show = !coachingId || !$(this).val() || String($(this).data('coaching')) === coachingId;
            $(this).prop('hidden', !show).prop('disabled', !show);
        });
        const current = $('#branch_id').val();
        if (current && $(`#branch_id option[value="${current}"]`).prop('disabled')) {
            $('#branch_id').val(selected && !$(`#branch_id option[value="${selected}"]`).prop('disabled') ? selected : '').trigger('change.select2');
        }
    }).trigger('change');
</script>
@endpush
