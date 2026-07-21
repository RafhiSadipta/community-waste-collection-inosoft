<?php

namespace App\Repositories\Eloquent;

use App\Models\Household;
use App\Repositories\Contracts\HouseholdRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class HouseholdRepository implements HouseholdRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Household::query();

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('owner_name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['block'])) {
            $query->where('block', $filters['block']);
        }

        if (! empty($filters['no'])) {
            $query->where('no', $filters['no']);
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    public function find(string $id): ?Household
    {
        return Household::find($id);
    }

    public function create(array $data): Household
    {
        return Household::create($data);
    }

    public function update(Household $household, array $data): Household
    {
        $household->fill($data);
        $household->save();

        return $household;
    }

    public function delete(Household $household): bool
    {
        return (bool) $household->delete();
    }
}
