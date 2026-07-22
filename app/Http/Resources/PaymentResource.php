<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'household_id' => (string) $this->household_id,
            'waste_id' => $this->waste_id ? (string) $this->waste_id : null,
            'amount' => $this->amount,
            'status' => $this->status,
            'payment_date' => optional($this->payment_date)->toIso8601String(),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
