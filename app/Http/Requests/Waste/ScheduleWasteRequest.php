<?php

namespace App\Http\Requests\Waste;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleWasteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pickup_date' => ['required', 'date'],
            'safety_confirmed' => ['sometimes', 'boolean'],
        ];
    }
}
