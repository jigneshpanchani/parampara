<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $paymentMode = $this->input('payment_mode');
        $isDirectMode = in_array($paymentMode, ['cash', 'upi', 'gpay'], true);

        return [
            'sale_date' => ['required', 'date'],
            'seller_name' => [
                empty($paymentMode) ? 'required' : 'nullable',
                'string',
                'max:255',
            ],
            'seller_contact_number' => ['nullable', 'string', 'max:20'],
            'product_id' => ['required', 'array', 'min:1'],
            'product_id.*' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'array', 'min:1'],
            'quantity.*' => ['required', 'integer', 'min:1'],
            'selling_price' => ['required', 'array', 'min:1'],
            'selling_price.*' => ['required', 'numeric', 'min:0'],
            'payment_mode' => ['nullable', 'in:cash,upi,gpay,mix'],
            'amount_paid' => ['required', 'numeric', $isDirectMode ? 'gt:0' : 'min:0'],
            'cash_amount' => ['nullable', 'numeric', 'min:0'],
            'online_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->input('payment_mode') === 'mix') {
                $cash = (float) $this->input('cash_amount', 0);
                $online = (float) $this->input('online_amount', 0);
                if ($cash + $online <= 0) {
                    $validator->errors()->add('cash_amount', 'For Mix payment, Cash Amount or Online Amount must be greater than 0.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'seller_name.required' => 'Seller Name is required when Payment Mode is Pay Later.',
            'amount_paid.gt' => 'Amount Paid must be greater than 0 for the selected Payment Mode.',
        ];
    }
}
