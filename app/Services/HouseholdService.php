<?php

namespace App\Services;

use App\Models\Household;
use App\Repositories\Contracts\HouseholdRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class HouseholdService
{
    public function __construct(
        private readonly HouseholdRepositoryInterface $households,
    ) {}

    public function list(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->households->paginate($filters, $perPage);
    }

    public function find(string $id): Household
    {
        $household = $this->households->find($id);

        abort_unless($household, 404, 'Household not found.');

        return $household;
    }

    public function create(array $data): Household
    {
        return $this->households->create($data);
    }

    public function update(string $id, array $data): Household
    {
        $household = $this->find($id);

        return $this->households->update($household, $data);
    }

    public function delete(string $id): void
    {
        $household = $this->find($id);

        $this->households->delete($household);
    }
}
