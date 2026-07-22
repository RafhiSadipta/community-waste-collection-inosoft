<?php

namespace App\Support;

use App\Models\Waste;
use App\Models\WasteElectronic;
use App\Models\WasteOrganic;
use App\Models\WastePaper;
use App\Models\WastePlastic;
use InvalidArgumentException;

class WasteFactory
{
    public const TYPES = ['organic', 'plastic', 'paper', 'electronic'];

    public static function make(string $type, array $attributes = []): Waste
    {
        $class = match ($type) {
            'organic' => WasteOrganic::class,
            'plastic' => WastePlastic::class,
            'paper' => WastePaper::class,
            'electronic' => WasteElectronic::class,
            default => throw new InvalidArgumentException("Unknown waste type: {$type}"),
        };

        return new $class(array_merge($attributes, ['type' => $type]));
    }
}
