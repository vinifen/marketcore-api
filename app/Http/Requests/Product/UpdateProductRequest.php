<?php

namespace App\Http\Requests\Product;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use App\Exceptions\ApiException;

class UpdateProductRequest extends FormRequest
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
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'sometimes|required|string|max:255',
            'stock' => 'sometimes|required|integer|min:0',
            'price' => 'sometimes|required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:8192',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new ApiException('Product update request failed due to invalid data.', $validator->errors()->toArray(), 422);
    }
}
