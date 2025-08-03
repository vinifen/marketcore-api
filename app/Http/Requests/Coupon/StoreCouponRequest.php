<?php

namespace App\Http\Requests\Coupon;

use App\Exceptions\ApiException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreCouponRequest extends FormRequest
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
            'code' => 'required|string|max:255|unique:coupons,code',
            'start_date' => 'nullable|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'discount_percentage' => 'required|numeric|min:0.01|max:99.99',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new ApiException('Coupon creation failed due to invalid data.', $validator->errors()->toArray(), 422);
    }
}
