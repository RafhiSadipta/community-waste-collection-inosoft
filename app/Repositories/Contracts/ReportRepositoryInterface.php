<?php

namespace App\Repositories\Contracts;

interface ReportRepositoryInterface
{
    public function wasteSummary(): array;

    public function paymentSummary(): array;

    public function householdHistory(string $householdId): array;
}
