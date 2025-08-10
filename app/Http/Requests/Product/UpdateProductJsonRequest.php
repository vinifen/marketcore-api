<?php

namespace App\Http\Requests\Product;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use App\Exceptions\ApiException;

class UpdateProductJsonRequest extends FormRequest
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
        ];
    }

    /**
     * Custom validation to ensure JSON content type
     */
    public function prepareForValidation(): void
    {
        if (!$this->isJson()) {
            throw new ApiException(
                'This endpoint only accepts JSON data. For file uploads, use POST /products/{id}/update',
                null,
                400
            );
        }

        if ($this->hasFile('image') || $this->has('image') || $this->has('remove_image')) {
            throw new ApiException(
                'Image uploads are not allowed on this endpoint. Use POST /products/{id}/update for file uploads.',
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
