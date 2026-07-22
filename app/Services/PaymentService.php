<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Waste;
use App\Repositories\Contracts\HouseholdRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\WasteRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaymentService
{
    public function __construct(
        private readonly PaymentRepositoryInterface $payments,
        private readonly HouseholdRepositoryInterface $households,
        private readonly WasteRepositoryInterface $wastes,
    ) {}

    public function list(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->payments->paginate($filters, $perPage);
    }

    public function find(string $id): Payment
    {
        $payment = $this->payments->find($id);

        abort_unless($payment, 404, 'Payment not found.');

        return $payment;
    }

    public function create(string $householdId, string $amount, ?string $wasteId = null): Payment
    {
        abort_unless($this->households->find($householdId), 404, 'Household not found.');

        if ($wasteId !== null) {
            $waste = $this->wastes->find($wasteId);

            abort_unless($waste, 404, 'Waste pickup not found.');
            abort_if($waste->household_id !== $householdId, 422, 'Waste pickup does not belong to this household.');
        }

        return $this->payments->create([
            'household_id' => $householdId,
            'waste_id' => $wasteId,
            'amount' => $amount,
            'status' => 'pending',
        ]);
    }

    /**
     * Business Rule #3 — auto-generate a payment record when a pickup is completed.
     * Amount is resolved polymorphically via $waste->getCompletionAmount().
     */
    public function createFromWaste(Waste $waste): Payment
    {
        return $this->payments->create([
            'household_id' => $waste->household_id,
            'waste_id' => $waste->id,
            'amount' => $waste->getCompletionAmount(),
            'status' => 'pending',
        ]);
    }

    public function confirm(string $id): Payment
    {
        $payment = $this->find($id);

        $payment->confirm();

        return $payment;
    }

    /**
     * Business Rule #1 support — used by WasteService before allowing a new pickup request.
     */
    public function hasUnpaidByHousehold(string $householdId): bool
    {
        return $this->payments->hasUnpaidByHousehold($householdId);
    }
}
