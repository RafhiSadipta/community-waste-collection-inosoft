<?php

namespace App\Repositories\Eloquent;

use App\Models\Payment;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Payment::query();

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['household_id'])) {
            $query->where('household_id', $filters['household_id']);
        }

        if (! empty($filters['from'])) {
            $query->where('created_at', '>=', $filters['from']);
        }

        if (! empty($filters['to'])) {
            $query->where('created_at', '<=', $filters['to']);
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    public function find(string $id): ?Payment
    {
        return Payment::find($id);
    }

    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    public function hasUnpaidByHousehold(string $householdId): bool
    {
        return Payment::where('household_id', $householdId)
            ->whereIn('status', ['pending', 'failed'])
            ->exists();
    }
}
