<?php

namespace App\Repositories\Contracts;

use App\Models\Household;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface HouseholdRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function find(string $id): ?Household;

    public function create(array $data): Household;

    public function update(Household $household, array $data): Household;

    public function delete(Household $household): bool;
}
