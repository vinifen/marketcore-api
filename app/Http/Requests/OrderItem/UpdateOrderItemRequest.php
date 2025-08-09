<?php

namespace App\Http\Requests\OrderItem;

use App\Exceptions\ApiException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'order_id' => 'sometimes|integer|exists:orders,id',
            'product_id' => 'sometimes|integer|exists:products,id',
            'quantity' => 'sometimes|integer|min:1',
            'unit_price' => 'sometimes|numeric|min:0',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new ApiException('Order item update request failed due to invalid data.', $validator->errors()->toArray(), 422);
    }
}
