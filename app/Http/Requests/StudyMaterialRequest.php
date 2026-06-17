<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudyMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->route('study_material') !== null;

        return [
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
            'subject_id' => ['nullable', 'integer', Rule::exists('subjects', 'id')->whereNull('deleted_at')],
            'file' => [
                $isUpdate ? 'nullable' : 'required',
                'file',
                'mimes:pdf,doc,docx,txt,rtf,ppt,pptx,zip,rar,7z,jpg,jpeg,png,webp,gif,mp4,mkv,avi,mov,webm',
                'max:102400',
            ],
            'targets' => ['required', 'array', 'min:1'],
            'targets.*' => ['string', 'regex:/^(coaching|branch|class|batch|student):\d+$/'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }
}
