<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'integer', Rule::exists('branches', 'id')->whereNull('deleted_at')],
            'school_class_id' => ['required', 'integer', Rule::exists('school_classes', 'id')->whereNull('deleted_at')],
            'name' => ['required', 'string', 'max:120'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'subject_ids' => ['nullable', 'array'],
            'subject_ids.*' => ['integer', Rule::exists('subjects', 'id')->whereNull('deleted_at')],
            'teacher_ids' => ['nullable', 'array'],
            'teacher_ids.*' => ['integer', Rule::exists('teachers', 'id')->whereNull('deleted_at')],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['integer', Rule::exists('students', 'id')->whereNull('deleted_at')],
            'is_active' => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }
}
