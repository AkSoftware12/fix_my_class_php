@php($lead = $lead ?? null)

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label required" for="student_name">Student Name</label>
        <input type="text" id="student_name" name="student_name" maxlength="120" required
               class="form-control @error('student_name') is-invalid @enderror" value="{{ old('student_name', $lead?->student_name) }}">
        @error('student_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="guardian_name">Guardian Name</label>
        <input type="text" id="guardian_name" name="guardian_name" maxlength="120"
               class="form-control @error('guardian_name') is-invalid @enderror" value="{{ old('guardian_name', $lead?->guardian_name) }}">
        @error('guardian_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label required" for="mobile">Mobile</label>
        <input type="text" id="mobile" name="mobile" maxlength="20" required
               class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile', $lead?->mobile) }}">
        @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="email">Email</label>
        <input type="email" id="email" name="email" maxlength="180"
               class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $lead?->email) }}">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="interested_class">Interested Class</label>
        <input type="text" id="interested_class" name="interested_class" maxlength="120"
               class="form-control @error('interested_class') is-invalid @enderror" value="{{ old('interested_class', $lead?->interested_class) }}">
        @error('interested_class')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="source">Source</label>
        <select id="source" name="source" class="form-select @error('source') is-invalid @enderror">
            <option value="">Select…</option>
            @foreach (['Walk-in', 'Phone', 'Website', 'Referral', 'Social Media', 'Advertisement', 'Other'] as $source)
                <option value="{{ $source }}" @selected(old('source', $lead?->source) === $source)>{{ $source }}</option>
            @endforeach
        </select>
        @error('source')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label required" for="stage">Stage</label>
        <select id="stage" name="stage" class="form-select @error('stage') is-invalid @enderror" required>
            @foreach (\App\Models\AdmissionLead::STAGES as $stage => $label)
                <option value="{{ $stage }}" @selected(old('stage', $lead?->stage ?? 'new') === $stage)>{{ $label }}</option>
            @endforeach
        </select>
        @error('stage')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="branch_id">Branch</label>
        <select id="branch_id" name="branch_id" class="form-select select2 @error('branch_id') is-invalid @enderror">
            <option value="">Select branch…</option>
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" @selected(old('branch_id', $lead?->branch_id) == $branch->id)>{{ $branch->name }}</option>
            @endforeach
        </select>
        @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="assigned_to">Assign To</label>
        <select id="assigned_to" name="assigned_to" class="form-select select2 @error('assigned_to') is-invalid @enderror">
            <option value="">Unassigned</option>
            @foreach ($staff as $member)
                <option value="{{ $member->id }}" @selected(old('assigned_to', $lead?->assigned_to) == $member->id)>{{ $member->name }}</option>
            @endforeach
        </select>
        @error('assigned_to')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="next_follow_up_at">Next Follow-up</label>
        <input type="date" id="next_follow_up_at" name="next_follow_up_at"
               class="form-control @error('next_follow_up_at') is-invalid @enderror"
               value="{{ old('next_follow_up_at', $lead?->next_follow_up_at?->format('Y-m-d')) }}">
        @error('next_follow_up_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label" for="notes">Notes</label>
        <textarea id="notes" name="notes" rows="3" maxlength="5000"
                  class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $lead?->notes) }}</textarea>
        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ $lead ? 'Update Lead' : 'Add Lead' }}</button>
    <a href="{{ route('admin.leads.index') }}" class="btn btn-light">Cancel</a>
</div>
