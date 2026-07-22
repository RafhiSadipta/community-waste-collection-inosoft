<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly PaymentService $payments,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'household_id', 'from', 'to']);
        $perPage = (int) $request->integer('per_page', 15);

        $payments = $this->payments->list($filters, $perPage);

        return $this->success([
            'items' => PaymentResource::collection($payments),
            'pagination' => [
                'current_page' => $payments->currentPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
                'last_page' => $payments->lastPage(),
            ],
        ]);
    }

    public function store(StorePaymentRequest $request): JsonResponse
    {
        $payment = $this->payments->create(
            $request->validated('household_id'),
            $request->validated('amount'),
            $request->validated('waste_id')
        );

        return $this->success(new PaymentResource($payment), 'Payment created.', 201);
    }

    public function confirm(string $id): JsonResponse
    {
        $payment = $this->payments->confirm($id);

        return $this->success(new PaymentResource($payment), 'Payment confirmed.');
    }
}
