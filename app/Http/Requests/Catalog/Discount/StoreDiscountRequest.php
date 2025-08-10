<?php

namespace App\Http\Requests\Catalog\Discount;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use App\Exceptions\ApiException;

class StoreDiscountRequest extends FormRequest
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
            'product_id' => 'required|exists:products,id',
            'description' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'discount_percentage' => 'required|numeric|min:0.01|max:100',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new ApiException('Discount creation request failed due to invalid data.', $validator->errors()->toArray(), 422);
    }
}
