<?php

namespace App\Http\Requests\CartItem;

use App\Exceptions\ApiException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCartItemRequest extends FormRequest
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
            'quantity' => 'required|integer|min:1',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new ApiException('Cart item update failed due to invalid data.', $validator->errors()->toArray(), 422);
    }
}
