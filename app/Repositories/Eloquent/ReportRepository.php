<?php

namespace App\Repositories\Eloquent;

use App\Models\Payment;
use App\Models\Waste;
use App\Repositories\Contracts\ReportRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ReportRepository implements ReportRepositoryInterface
{
    /**
     * Aggregated pickups grouped by waste type & status.
     */
    public function wasteSummary(): array
    {
        $pipeline = [
            [
                '$group' => [
                    '_id' => ['type' => '$type', 'status' => '$status'],
                    'count' => ['$sum' => 1],
                ],
            ],
            [
                '$project' => [
                    '_id' => 0,
                    'type' => '$_id.type',
                    'status' => '$_id.status',
                    'count' => 1,
                ],
            ],
            ['$sort' => ['type' => 1, 'status' => 1]],
        ];

        return $this->runAggregate('wastes', $pipeline);
    }

    /**
     * Total payments by status + total revenue (sum of "paid" only).
     */
    public function paymentSummary(): array
    {
        $pipeline = [
            [
                '$group' => [
                    '_id' => '$status',
                    'total_amount' => ['$sum' => '$amount'],
                    'count' => ['$sum' => 1],
                ],
            ],
            [
                '$project' => [
                    '_id' => 0,
                    'status' => '$_id',
                    'total_amount' => 1,
                    'count' => 1,
                ],
            ],
            ['$sort' => ['status' => 1]],
        ];

        $byStatus = $this->runAggregate('payments', $pipeline);

        $totalRevenue = 0;
        foreach ($byStatus as $row) {
            if ($row['status'] === 'paid') {
                $totalRevenue = $row['total_amount'];
            }
        }

        return [
            'by_status' => $byStatus,
            'total_revenue' => $totalRevenue,
        ];
    }

    /**
     * Pickup requests + payment history for one household.
     */
    public function householdHistory(string $householdId): array
    {
        return [
            'pickups' => Waste::where('household_id', $householdId)->orderByDesc('created_at')->get(),
            'payments' => Payment::where('household_id', $householdId)->orderByDesc('created_at')->get(),
        ];
    }

    private function runAggregate(string $collection, array $pipeline): array
    {
        $cursor = DB::connection('mongodb')->getCollection($collection)->aggregate($pipeline);

        return array_map(fn ($document) => (array) $document, iterator_to_array($cursor));
    }
}
