<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaxInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_date' => ['required', 'date'],

            'buyer_name' => ['required', 'string', 'max:255'],
            'buyer_address' => ['nullable', 'string', 'max:1000'],
            'buyer_gstin' => ['nullable', 'string', 'max:20'],
            'buyer_state' => ['nullable', 'string', 'max:60'],
            'buyer_contact_number' => ['nullable', 'string', 'max:20'],

            'vehicle_no' => ['nullable', 'string', 'max:40'],
            'transport' => ['nullable', 'string', 'max:120'],
            'broker' => ['nullable', 'string', 'max:120'],
            'eway_bill_no' => ['nullable', 'string', 'max:40'],

            'is_interstate' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:2000'],

            'product_name' => ['required', 'array', 'min:1'],
            'product_name.*' => ['required', 'string', 'max:255'],
            'product_id' => ['nullable', 'array'],
            'product_id.*' => ['nullable', 'exists:products,id'],
            'hsn_code' => ['nullable', 'array'],
            'hsn_code.*' => ['nullable', 'string', 'max:20'],
            'unit_of_measure' => ['nullable', 'array'],
            'unit_of_measure.*' => ['nullable', 'string', 'max:20'],
            'quantity' => ['required', 'array', 'min:1'],
            'quantity.*' => ['required', 'numeric', 'min:0.001'],
            'rate' => ['required', 'array', 'min:1'],
            'rate.*' => ['required', 'numeric', 'min:0'],
            'gst_rate' => ['required', 'array', 'min:1'],
            'gst_rate.*' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Manually-typed rows (no product picked) submit an empty product_id — turn
        // those into null so the "exists" rule is skipped for them.
        $productIds = $this->input('product_id', []);
        if (is_array($productIds)) {
            $productIds = array_map(
                fn ($id) => ($id === '' || $id === null) ? null : $id,
                $productIds
            );
        }

        $this->merge([
            'is_interstate' => $this->boolean('is_interstate'),
            'product_id' => $productIds,
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $names = $this->input('product_name', []);
            $quantities = $this->input('quantity', []);
            $rates = $this->input('rate', []);

            // Every row must carry the same set of parallel-array keys.
            if (count($names) !== count($quantities) || count($names) !== count($rates)) {
                $validator->errors()->add('product_name', 'Each product row must have a quantity and a rate.');
                return;
            }

            $total = 0.0;
            foreach ($quantities as $i => $qty) {
                $total += (float) $qty * (float) ($rates[$i] ?? 0);
            }

            if ($total <= 0) {
                $validator->errors()->add('product_name', 'The invoice total must be greater than 0.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'product_name.required' => 'Add at least one product row.',
            'quantity.*.min' => 'Quantity must be greater than 0.',
        ];
    }
}
