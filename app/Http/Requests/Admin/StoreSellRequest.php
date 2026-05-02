<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSellRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sell_date' => ['required', 'date'],
            'seller_name' => ['nullable', 'string', 'max:255'],
            'seller_contact_number' => ['nullable', 'string', 'max:20'],
            'product_id' => ['required', 'array', 'min:1'],
            'product_id.*' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'array', 'min:1'],
            'quantity.*' => ['required', 'integer', 'min:1'],
            'selling_price' => ['required', 'array', 'min:1'],
            'selling_price.*' => ['required', 'numeric', 'min:0'],
            'payment_mode' => ['nullable', 'in:cash,upi,gpay,mix'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
            'cash_amount' => ['nullable', 'numeric', 'min:0'],
            'online_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
