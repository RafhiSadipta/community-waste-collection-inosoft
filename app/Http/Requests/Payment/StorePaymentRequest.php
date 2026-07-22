<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'household_id' => ['required', 'string'],
            'waste_id' => ['sometimes', 'nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
        ];
    }
}
