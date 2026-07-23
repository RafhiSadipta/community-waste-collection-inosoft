<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\WasteResource;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ReportService $reports,
    ) {}

    public function wasteSummary(): JsonResponse
    {
        return $this->success($this->reports->wasteSummary());
    }

    public function paymentSummary(): JsonResponse
    {
        return $this->success($this->reports->paymentSummary());
    }

    public function householdHistory(string $id): JsonResponse
    {
        $history = $this->reports->householdHistory($id);

        return $this->success([
            'pickups' => WasteResource::collection($history['pickups']),
            'payments' => PaymentResource::collection($history['payments']),
        ]);
    }
}
