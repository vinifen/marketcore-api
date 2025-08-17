<?php

namespace App\Http\Requests\Catalog\Product;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use App\Exceptions\ApiException;

class UpdateProductFormRequest extends FormRequest
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
            'remove_image' => 'nullable|boolean',
        ];
    }

    public function prepareForValidation(): void
    {
        $contentType = $this->header('Content-Type', '');
        $hasFileFields = $this->hasFile('image') || $this->has('remove_image');

        if (!$hasFileFields && !str_starts_with($contentType, 'multipart/form-data')) {
            throw new ApiException(
                'This endpoint only accepts multipart/form-data. For JSON updates, use PUT /products/{id}',
                null,
                400
            );
        }
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new ApiException('Product update request failed due to invalid data.', $validator->errors()->toArray(), 422);
    }
}
