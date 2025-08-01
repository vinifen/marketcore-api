<?php

namespace App\Http\Requests\Discount;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use App\Exceptions\ApiException;

class UpdateDiscountRequest extends FormRequest
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
            'product_id' => 'sometimes|exists:products,id',
            'description' => 'sometimes|required|string|max:255',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after_or_equal:start_date',
            'discount_percentage' => 'sometimes|required|numeric|min:0.01|max:100',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new ApiException('Discount update request failed due to invalid data.', $validator->errors()->toArray(), 422);
    }
}
