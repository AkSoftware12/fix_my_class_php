<?php

namespace App\Http\Requests;

use App\Models\Coaching;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;
        $isUpdate = $userId !== null;

        $auth = $this->user();

        if ($auth?->hasRole('Super Admin')) {
            $assignableRoles = ['Super Admin', 'City Admin', 'Coaching Admin', 'Branch Admin', 'Teacher', 'Student'];
        } elseif ($auth?->hasRole('City Admin')) {
            $assignableRoles = ['Coaching Admin', 'Branch Admin', 'Teacher', 'Student'];
        } elseif ($auth?->hasRole('Branch Admin')) {
            $assignableRoles = ['Teacher', 'Student'];
        } else {
            $assignableRoles = ['Teacher', 'Student'];
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email:rfc', 'max:255',
                Rule::unique('users', 'email')->ignore($userId)->withoutTrashed(),
            ],
            'mobile' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]{7,20}$/'],
            'password' => [
                $isUpdate ? 'nullable' : 'required',
                'confirmed',
                Password::min(8)->letters()->numbers(),
            ],
            'role' => ['required', 'string', Rule::in($assignableRoles)],
            'city_id' => [
                Rule::requiredIf($this->input('role') === 'City Admin'),
                'nullable', 'integer', Rule::exists('cities', 'id')->whereNull('deleted_at'),
                function ($attribute, $value, $fail) use ($userId) {
                    if ($this->input('role') !== 'City Admin' || ! $value) {
                        return;
                    }
                    $exists = User::whereHas('roles', fn ($q) => $q->where('name', 'City Admin'))
                        ->where('city_id', $value)
                        ->whereNull('deleted_at')
                        ->when($userId, fn ($q) => $q->where('id', '!=', $userId))
                        ->exists();
                    if ($exists) {
                        $fail('This city already has a City Admin assigned.');
                    }
                },
            ],
            'coaching_id' => [
                Rule::requiredIf(in_array($this->input('role'), ['Coaching Admin', 'Branch Admin', 'Teacher', 'Student'])),
                'nullable', 'integer', Rule::exists('coachings', 'id')->whereNull('deleted_at'),
                function ($attribute, $value, $fail) {
                    $auth = $this->user();
                    if (! $auth?->hasRole('City Admin') || ! $value) {
                        return;
                    }
                    $belongs = Coaching::where('id', $value)
                        ->where('city_id', $auth->city_id)
                        ->whereNull('deleted_at')
                        ->exists();
                    if (! $belongs) {
                        $fail('The selected coaching does not belong to your city.');
                    }
                },
            ],
            'branch_id' => [
                Rule::requiredIf(in_array($this->input('role'), ['Branch Admin', 'Teacher', 'Student'])),
                'nullable', 'integer',
                Rule::exists('branches', 'id')
                    ->where('coaching_id', $this->input('coaching_id'))
                    ->whereNull('deleted_at'),
            ],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }
}
