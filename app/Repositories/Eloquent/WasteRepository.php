<?php

namespace App\Repositories\Eloquent;

use App\Models\Waste;
use App\Repositories\Contracts\WasteRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class WasteRepository implements WasteRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Waste::query();

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (! empty($filters['household_id'])) {
            $query->where('household_id', $filters['household_id']);
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    public function find(string $id): ?Waste
    {
        return Waste::find($id);
    }

    public function save(Waste $waste): Waste
    {
        $waste->save();

        return $waste;
    }
}
