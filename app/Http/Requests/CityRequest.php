<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $cityId = $this->route('city')?->id;

        return [
            'name' => [
                'required', 'string', 'max:120',
                Rule::unique('cities', 'name')
                    ->where('state', $this->input('state'))
                    ->ignore($cityId)
                    ->withoutTrashed(),
            ],
            'state' => ['required', 'string', 'max:120'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }
}
