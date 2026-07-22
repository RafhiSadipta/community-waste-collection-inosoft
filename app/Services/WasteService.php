<?php

namespace App\Services;

use App\Exceptions\InvalidStatusTransitionException;
use App\Models\Waste;
use App\Repositories\Contracts\HouseholdRepositoryInterface;
use App\Repositories\Contracts\WasteRepositoryInterface;
use App\Support\WasteFactory;
use DateTimeInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class WasteService
{
    public function __construct(
        private readonly WasteRepositoryInterface $wastes,
        private readonly HouseholdRepositoryInterface $households,
    ) {}

    public function list(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->wastes->paginate($filters, $perPage);
    }

    public function find(string $id): Waste
    {
        $waste = $this->wastes->find($id);

        abort_unless($waste, 404, 'Waste pickup not found.');

        return $waste;
    }

    public function create(string $householdId, string $type): Waste
    {
        abort_unless($this->households->find($householdId), 404, 'Household not found.');

        // TODO (Checkpoint 3): Business Rule #1 — block if household has any unpaid payment.

        $waste = WasteFactory::make($type, [
            'household_id' => $householdId,
            'status' => 'pending',
        ]);

        return $this->wastes->save($waste);
    }

    public function schedule(string $id, DateTimeInterface $pickupDate, bool $safetyConfirmed = false): Waste
    {
        $waste = $this->find($id);

        if ($safetyConfirmed) {
            $waste->confirmSafetyCheck();
        }

        if (! $waste->canSchedule()) {
            throw new InvalidStatusTransitionException('Pickup tidak dapat dijadwalkan pada kondisi saat ini.');
        }

        $waste->schedule($pickupDate);

        return $waste;
    }

    public function complete(string $id): Waste
    {
        $waste = $this->find($id);

        if ($waste->status !== 'scheduled') {
            throw new InvalidStatusTransitionException('Pickup hanya dapat diselesaikan dari status scheduled.');
        }

        $waste->complete();

        // TODO (Checkpoint 3): Business Rule #3 — auto-generate payment record.

        return $waste;
    }

    public function cancel(string $id): Waste
    {
        $waste = $this->find($id);

        if (in_array($waste->status, ['completed', 'canceled'], true)) {
            throw new InvalidStatusTransitionException('Pickup yang sudah selesai/dibatalkan tidak dapat dibatalkan lagi.');
        }

        $waste->cancel();

        return $waste;
    }
}
