<?php

namespace App\Models;

use DateTimeInterface;
use MongoDB\Laravel\Eloquent\Model;

class Waste extends Model
{
    protected $connection = 'mongodb';

    protected $table = 'wastes';

    protected $fillable = [
        'household_id',
        'type',
        'status',
        'pickup_date',
        'safety_check',
    ];

    protected $casts = [
        'pickup_date' => 'datetime',
        'safety_check' => 'boolean',
    ];

    /**
     * Hydrate query results into the correct subclass based on the `type`
     * discriminator field, so polymorphic behavior (schedule/complete/cancel,
     * etc.) works regardless of which concrete class the query was run on.
     */
    public function newFromBuilder($attributes = [], $connection = null): static
    {
        $data = (array) $attributes;

        $class = match ($data['type'] ?? null) {
            'organic' => WasteOrganic::class,
            'plastic' => WastePlastic::class,
            'paper' => WastePaper::class,
            'electronic' => WasteElectronic::class,
            default => static::class,
        };

        $model = new $class;
        $model->exists = true;
        $model->setRawAttributes($data, true);
        $model->setConnection($connection ?: $this->getConnectionName());

        return $model;
    }

    public function canSchedule(): bool
    {
        return $this->status === 'pending';
    }

    public function getCompletionAmount(): int
    {
        return 50000;
    }

    public function getAutoCancelAfterDays(): ?int
    {
        return null;
    }

    public function confirmSafetyCheck(): void
    {
        // No-op by default — only meaningful for waste types that require it.
    }

    public function schedule(DateTimeInterface $pickupDate): void
    {
        $this->pickup_date = $pickupDate;
        $this->status = 'scheduled';
        $this->save();
    }

    public function complete(): void
    {
        $this->status = 'completed';
        $this->save();
    }

    public function cancel(): void
    {
        $this->status = 'canceled';
        $this->save();
    }
}
