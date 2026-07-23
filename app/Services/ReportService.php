<?php

namespace App\Services;

use App\Repositories\Contracts\HouseholdRepositoryInterface;
use App\Repositories\Contracts\ReportRepositoryInterface;

class ReportService
{
    public function __construct(
        private readonly ReportRepositoryInterface $reports,
        private readonly HouseholdRepositoryInterface $households,
    ) {}

    public function wasteSummary(): array
    {
        return $this->reports->wasteSummary();
    }

    public function paymentSummary(): array
    {
        return $this->reports->paymentSummary();
    }

    public function householdHistory(string $householdId): array
    {
        abort_unless($this->households->find($householdId), 404, 'Household not found.');

        return $this->reports->householdHistory($householdId);
    }
}
