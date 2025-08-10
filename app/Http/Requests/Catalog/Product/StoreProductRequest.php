<?php

namespace App\Http\Requests\Catalog\Product;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use App\Exceptions\ApiException;

class StoreProductRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:8192'
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new ApiException('Product creation request failed due to invalid data.', $validator->errors()->toArray(), 422);
    }
}
