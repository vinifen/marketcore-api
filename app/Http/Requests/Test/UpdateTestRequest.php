<?php

namespace App\Http\Requests\Test;

use App\Exceptions\ApiException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTestRequest extends FormRequest
{
    public function authorize(): bool
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
            'description' => 'sometimes|string|max:1000',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new ApiException('Update test request error.', $validator->errors()->toArray(), 422);
    }
}
