@php($student = $student ?? null)

<div class="row g-3">
    <div class="col-12"><h3 class="h6 fw-bold text-primary mb-0"><i class="bi bi-person me-1"></i>Student Details</h3></div>

    <div class="col-md-6">
        <label class="form-label required" for="name">Student Name</label>
        <input type="text" id="name" name="name" maxlength="255" required
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $student?->user?->name) }}">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label required" for="email">Email</label>
        <input type="email" id="email" name="email" maxlength="255" required
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $student?->user?->email) }}">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label required" for="mobile">Mobile</label>
        <input type="text" id="mobile" name="mobile" maxlength="20" required
               class="form-control @error('mobile') is-invalid @enderror"
               value="{{ old('mobile', $student?->user?->mobile) }}">
        @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="date_of_birth">Date of Birth</label>
        <input type="date" id="date_of_birth" name="date_of_birth"
               class="form-control @error('date_of_birth') is-invalid @enderror"
               value="{{ old('date_of_birth', $student?->date_of_birth?->format('Y-m-d')) }}">
        @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="photo">Photo <span class="text-muted">(JPG/PNG, max 2&nbsp;MB)</span></label>
        <input type="file" id="photo" name="photo" accept="image/*"
               class="form-control @error('photo') is-invalid @enderror">
        @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    @if ($student)
        <div class="col-md-4">
            <label class="form-label">Admission Number</label>
            <input type="text" class="form-control" value="{{ $student->admission_number }}" readonly>
        </div>
    @endif

    <div class="col-12 mt-4"><h3 class="h6 fw-bold text-primary mb-0"><i class="bi bi-diagram-3 me-1"></i>Placement</h3></div>

    <div class="col-md-4">
        <label class="form-label required" for="branch_id">Branch</label>
        <select id="branch_id" name="branch_id" class="form-select select2 @error('branch_id') is-invalid @enderror" required>
            <option value="">Select branch…</option>
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" @selected(old('branch_id', $student?->branch_id) == $branch->id)>{{ $branch->name }}</option>
            @endforeach
        </select>
        @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="school_class_id">Class</label>
        <select id="school_class_id" name="school_class_id" class="form-select select2 @error('school_class_id') is-invalid @enderror">
            <option value="">Select class…</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}" @selected(old('school_class_id', $student?->school_class_id) == $class->id)>{{ $class->name }}</option>
            @endforeach
        </select>
        @error('school_class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="batch_id">Batch</label>
        <select id="batch_id" name="batch_id" class="form-select select2 @error('batch_id') is-invalid @enderror">
            <option value="">Select batch…</option>
            @foreach ($batches as $batch)
                <option value="{{ $batch->id }}" @selected(old('batch_id', $student?->batch_id) == $batch->id)>{{ $batch->name }}</option>
            @endforeach
        </select>
        @error('batch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12 mt-4"><h3 class="h6 fw-bold text-primary mb-0"><i class="bi bi-people me-1"></i>Guardian</h3></div>

    <div class="col-md-6">
        <label class="form-label" for="guardian_name">Guardian Name</label>
        <input type="text" id="guardian_name" name="guardian_name" maxlength="120"
               class="form-control @error('guardian_name') is-invalid @enderror"
               value="{{ old('guardian_name', $student?->guardian_name) }}">
        @error('guardian_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="guardian_mobile">Guardian Mobile</label>
        <input type="text" id="guardian_mobile" name="guardian_mobile" maxlength="20"
               class="form-control @error('guardian_mobile') is-invalid @enderror"
               value="{{ old('guardian_mobile', $student?->guardian_mobile) }}">
        @error('guardian_mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12 mt-4"><h3 class="h6 fw-bold text-primary mb-0"><i class="bi bi-shield-lock me-1"></i>Login & Documents</h3></div>

    <div class="col-md-6">
        <label class="form-label {{ $student ? '' : 'required' }}" for="password">
            Password {{ $student ? '(leave blank to keep current)' : '' }}
        </label>
        <input type="password" id="password" name="password" autocomplete="new-password"
               class="form-control @error('password') is-invalid @enderror" {{ $student ? '' : 'required' }}>
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label {{ $student ? '' : 'required' }}" for="password_confirmation">Confirm Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" {{ $student ? '' : 'required' }}>
    </div>

    <div class="col-md-6">
        <label class="form-label" for="documents">Documents <span class="text-muted">(PDF/Image/DOC, max 5&nbsp;MB each)</span></label>
        <input type="file" id="documents" name="documents[]" multiple accept=".pdf,.doc,.docx,image/*"
               class="form-control @error('documents.*') is-invalid @enderror">
        @error('documents.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    @if ($student && $student->documents->isNotEmpty())
        <div class="col-12">
            <label class="form-label">Existing Documents</label>
            <ul class="list-group">
                @foreach ($student->documents as $document)
                    <li class="list-group-item d-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-text text-primary"></i>
                        <a href="{{ $document->file_url }}" target="_blank" class="text-decoration-none flex-grow-1">{{ $document->title }}</a>
                        @can('students.edit')
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                    onclick="fmcDelete('{{ route('admin.students.documents.destroy', [$student, $document]) }}', '#noTable'); setTimeout(() => location.reload(), 1200)">
                                <i class="bi bi-trash"></i>
                            </button>
                        @endcan
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="col-12">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                   @checked(old('is_active', $student?->is_active ?? true))>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ $student ? 'Update Student' : 'Admit Student' }}</button>
    <a href="{{ route('admin.students.index') }}" class="btn btn-light">Cancel</a>
</div>
