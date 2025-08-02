<?php

namespace App\Http\Requests\CartItem;

use App\Exceptions\ApiException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreCartItemRequest extends FormRequest
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
            'cart_id' => 'required|exists:carts,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'sometimes|integer|min:1',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new ApiException('Cart item creation failed due to invalid data.', $validator->errors()->toArray(), 422);
    }
}
