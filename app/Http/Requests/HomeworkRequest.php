<?php

namespace App\Http\Requests;

use App\Models\Homework;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HomeworkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->route('homework') !== null;

        return [
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:20000'],
            'subject_id' => ['nullable', 'integer', Rule::exists('subjects', 'id')->whereNull('deleted_at')],
            'type' => ['required', Rule::in(Homework::TYPES)],
            'attachment' => [
                Rule::requiredIf(! $isUpdate && $this->input('type') !== 'text'),
                'nullable', 'file', 'max:51200',
                ...$this->attachmentMimeRule(),
            ],
            'visibility' => ['required', Rule::in(['public', 'private'])],
            'due_date' => ['nullable', 'date', $isUpdate ? 'after_or_equal:2000-01-01' : 'after_or_equal:today'],
            'status' => ['nullable', Rule::in(Homework::STATUSES)],
            'targets' => ['required', 'array', 'min:1'],
            'targets.*' => ['string', 'regex:/^(coaching|branch|class|batch|student):\d+$/'],
        ];
    }

    protected function attachmentMimeRule(): array
    {
        return match ($this->input('type')) {
            'pdf' => ['mimes:pdf'],
            'image' => ['mimes:jpg,jpeg,png,webp,gif'],
            'video' => ['mimes:mp4,mkv,avi,mov,webm'],
            default => ['mimes:pdf,jpg,jpeg,png,webp,gif,mp4,mkv,avi,mov,webm'],
        };
    }

    public function messages(): array
    {
        return [
            'targets.required' => 'Select at least one target audience for this homework.',
            'targets.*.regex' => 'Invalid target selection.',
        ];
    }
}
