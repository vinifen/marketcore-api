<?php

namespace App\Http\Requests\Users\Address;

use App\Exceptions\ApiException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'street' => 'required|string|max:255',
            'number' => 'required|integer',
            'complement' => 'nullable|string|max:100',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new ApiException('Address creation request failed due to invalid data.', $validator->errors()->toArray(), 422);
    }
}
