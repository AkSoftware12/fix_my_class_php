@php($teacher = $teacher ?? null)

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label required" for="name">Full Name</label>
        <input type="text" id="name" name="name" maxlength="255" required
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $teacher?->user?->name) }}">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label required" for="email">Email</label>
        <input type="email" id="email" name="email" maxlength="255" required
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $teacher?->user?->email) }}">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label required" for="mobile">Mobile</label>
        <input type="text" id="mobile" name="mobile" maxlength="20" required
               class="form-control @error('mobile') is-invalid @enderror"
               value="{{ old('mobile', $teacher?->user?->mobile) }}">
        @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="qualification">Qualification</label>
        <input type="text" id="qualification" name="qualification" maxlength="180"
               class="form-control @error('qualification') is-invalid @enderror"
               value="{{ old('qualification', $teacher?->qualification) }}">
        @error('qualification')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label required" for="branch_id">Branch</label>
        <select id="branch_id" name="branch_id" class="form-select select2 @error('branch_id') is-invalid @enderror" required>
            <option value="">Select branch…</option>
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" @selected(old('branch_id', $teacher?->branch_id) == $branch->id)>{{ $branch->name }}</option>
            @endforeach
        </select>
        @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="subject_id">Primary Subject</label>
        <select id="subject_id" name="subject_id" class="form-select select2 @error('subject_id') is-invalid @enderror">
            <option value="">Select subject…</option>
            @foreach ($subjects as $subject)
                <option value="{{ $subject->id }}" @selected(old('subject_id', $teacher?->subject_id) == $subject->id)>{{ $subject->name }}</option>
            @endforeach
        </select>
        @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label {{ $teacher ? '' : 'required' }}" for="password">
            Password {{ $teacher ? '(leave blank to keep current)' : '' }}
        </label>
        <input type="password" id="password" name="password" autocomplete="new-password"
               class="form-control @error('password') is-invalid @enderror" {{ $teacher ? '' : 'required' }}>
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label {{ $teacher ? '' : 'required' }}" for="password_confirmation">Confirm Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" {{ $teacher ? '' : 'required' }}>
    </div>

    <div class="col-12">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                   @checked(old('is_active', $teacher?->is_active ?? true))>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ $teacher ? 'Update Teacher' : 'Create Teacher' }}</button>
    <a href="{{ route('admin.teachers.index') }}" class="btn btn-light">Cancel</a>
</div>
