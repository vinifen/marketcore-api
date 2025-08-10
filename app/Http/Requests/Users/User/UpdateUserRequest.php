<?php

namespace App\Http\Requests\Users\User;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use App\Exceptions\ApiException;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): true
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|min:2|max:255',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users')->ignore(optional($this->user())->id),
            ],
            'new_password' => 'sometimes|string|min:8|confirmed',
            'current_password' => [
                Rule::requiredIf(function () {
                    return $this->filled('email') || $this->filled('new_password');
                }),
                'string',
                function ($attribute, $value, $fail) {
                    if ($this->filled('new_password') && $value === $this->input('new_password')) {
                        $fail('The current password cannot be the same as the new password.');
                    }
                },
            ],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new ApiException('User update request failed due to invalid data.', $validator->errors()->toArray(), 422);
    }
}
