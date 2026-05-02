<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AddSellPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:cash,upi,gpay,bank_transfer,cheque,other'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
