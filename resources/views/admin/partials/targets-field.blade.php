{{--
    Shared target audience selector.
    Expects: $branches, $classes, $batches, $students collections and
    optional $selectedTargets = ['branch:1', 'student:5', ...]
--}}
@php($selectedTargets = $selectedTargets ?? [])

<label class="form-label required" for="targets">Target Audience</label>
<select id="targets" name="targets[]" multiple
        class="form-select select2 @error('targets') is-invalid @enderror @error('targets.*') is-invalid @enderror">
    <optgroup label="Coaching">
        @if (auth()->user()->coaching_id)
            <option value="coaching:{{ auth()->user()->coaching_id }}"
                    @selected(in_array('coaching:'.auth()->user()->coaching_id, old('targets', $selectedTargets)))>
                Entire Coaching
            </option>
        @else
            @foreach (\App\Models\Coaching::visibleTo(auth()->user())->active()->orderBy('name')->get() as $coaching)
                <option value="coaching:{{ $coaching->id }}"
                        @selected(in_array('coaching:'.$coaching->id, old('targets', $selectedTargets)))>
                    {{ $coaching->name }}
                </option>
            @endforeach
        @endif
    </optgroup>
    <optgroup label="Branches">
        @foreach ($branches as $branch)
            <option value="branch:{{ $branch->id }}" @selected(in_array('branch:'.$branch->id, old('targets', $selectedTargets)))>
                {{ $branch->name }}
            </option>
        @endforeach
    </optgroup>
    <optgroup label="Classes">
        @foreach ($classes as $class)
            <option value="class:{{ $class->id }}" @selected(in_array('class:'.$class->id, old('targets', $selectedTargets)))>
                {{ $class->name }}
            </option>
        @endforeach
    </optgroup>
    <optgroup label="Batches">
        @foreach ($batches as $batch)
            <option value="batch:{{ $batch->id }}" @selected(in_array('batch:'.$batch->id, old('targets', $selectedTargets)))>
                {{ $batch->name }}
            </option>
        @endforeach
    </optgroup>
    <optgroup label="Students">
        @foreach ($students as $student)
            <option value="student:{{ $student->id }}" @selected(in_array('student:'.$student->id, old('targets', $selectedTargets)))>
                {{ $student->user?->name }} ({{ $student->admission_number }})
            </option>
        @endforeach
    </optgroup>
</select>
@error('targets')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
@error('targets.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
<div class="form-text">Select one or more: entire coaching, branch, class, batch or specific students.</div>
