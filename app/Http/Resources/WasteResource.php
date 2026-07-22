<?php

namespace App\Http\Resources;

use App\Models\WasteElectronic;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WasteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'household_id' => (string) $this->household_id,
            'type' => $this->type,
            'status' => $this->status,
            'pickup_date' => optional($this->pickup_date)->toIso8601String(),
            'safety_check' => $this->when(
                $this->resource instanceof WasteElectronic,
                fn () => (bool) $this->safety_check
            ),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
