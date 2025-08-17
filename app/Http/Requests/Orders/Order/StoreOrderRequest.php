<?php

namespace App\Http\Requests\Orders\Order;

use App\Exceptions\ApiException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'address_id' => 'required|exists:addresses,id',
            'coupon_code' => 'nullable|exists:coupons,code',
            'order_date' => 'sometimes|date',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new ApiException('Order creation request failed due to invalid data.', $validator->errors()->toArray(), 422);
    }
}
