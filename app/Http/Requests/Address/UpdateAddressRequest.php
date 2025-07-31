<?php

namespace App\Http\Requests\Address;

use App\Exceptions\ApiException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
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
            'street' => 'sometimes|string|max:255',
            'number' => 'sometimes|integer',
            'complement' => 'nullable|string|max:100',
            'city' => 'sometimes|string|max:100',
            'state' => 'sometimes|string|max:100',
            'postal_code' => 'sometimes|string|max:20',
            'country' => 'sometimes|string|max:100',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new ApiException('Address update request failed due to invalid data.', $validator->errors()->toArray(), 422);
    }
}
