<?php

namespace App\Http\Requests\Coupons;

use App\Exceptions\ApiException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCouponRequest extends FormRequest
{
    public function authorize(): true
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'code' => 'sometimes|string|max:255|unique:coupons,code,' . $this->route('coupon'),
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|required|date|after_or_equal:start_date',
            'discount_percentage' => 'sometimes|numeric|min:0.01|max:99.99',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new ApiException('Coupon update failed due to invalid data.', $validator->errors()->toArray(), 422);
    }
}
