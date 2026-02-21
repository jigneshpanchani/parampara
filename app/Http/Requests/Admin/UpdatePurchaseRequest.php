<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'purchase_date' => ['required', 'date'],
            'supplier_name' => ['required', 'string', 'max:255'],
            'bill_type' => ['required', 'in:gst,without_gst'],
            'bill_details' => ['nullable', 'string'],
            'transportation_cost' => ['nullable', 'numeric', 'min:0'],
            'bill_due_date' => ['nullable', 'date'],
            'expense' => ['nullable', 'numeric', 'min:0'],
            'expense_details' => ['nullable', 'string'],
            'product_id' => ['required', 'array', 'min:1'],
            'product_id.*' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'array', 'min:1'],
            'quantity.*' => ['required', 'integer', 'min:1'],
            'purchase_price' => ['required', 'array', 'min:1'],
            'purchase_price.*' => ['required', 'numeric', 'min:0'],
        ];
    }
}
