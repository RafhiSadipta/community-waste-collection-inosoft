<?php

namespace App\Repositories\Contracts;

use App\Models\Waste;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface WasteRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function find(string $id): ?Waste;

    public function save(Waste $waste): Waste;
}
