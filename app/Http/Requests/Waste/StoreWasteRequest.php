<?php

namespace App\Http\Requests\Waste;

use App\Support\WasteFactory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWasteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'household_id' => ['required', 'string'],
            'type' => ['required', 'string', Rule::in(WasteFactory::TYPES)],
        ];
    }
}
