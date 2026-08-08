<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Product $product */
        $product = $this->route('product');

        return [
            'product_name' => ['required', 'string', 'max:255'],
            'product_code' => ['required', 'string', 'max:50', 'unique:products,product_code,' . $product->id],
            'hsn_code' => ['nullable', 'string', 'max:20'],
            'unit_of_measure' => ['nullable', 'string', 'max:20'],
            'gst_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'description' => ['nullable', 'string'],
            'base_price_min' => ['required', 'numeric', 'min:0'],
            'base_price_max' => ['required', 'numeric', 'min:0', 'gte:base_price_min'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function messages(): array
    {
        return [
            'base_price_max.gte' => 'Base price max must be greater than or equal to base price min.',
        ];
    }
}
