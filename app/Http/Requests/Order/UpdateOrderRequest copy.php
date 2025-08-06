<?php

namespace App\Http\Requests\Order;

use App\Enums\OrderStatus;
use App\Exceptions\ApiException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
            'address_id' => 'sometimes|exists:addresses,id',
        ];
    }
    protected function failedValidation(Validator $validator): void
    {
        throw new ApiException('Order update request failed due to invalid data.', $validator->errors()->toArray(), 422);
    }
}
