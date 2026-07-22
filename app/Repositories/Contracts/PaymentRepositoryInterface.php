<?php

namespace App\Repositories\Contracts;

use App\Models\Payment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PaymentRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function find(string $id): ?Payment;

    public function create(array $data): Payment;

    public function hasUnpaidByHousehold(string $householdId): bool;
}
