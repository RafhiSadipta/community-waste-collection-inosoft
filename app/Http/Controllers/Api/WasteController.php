<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Waste\ScheduleWasteRequest;
use App\Http\Requests\Waste\StoreWasteRequest;
use App\Http\Resources\WasteResource;
use App\Services\WasteService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WasteController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly WasteService $wastes,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'type', 'household_id']);
        $perPage = (int) $request->integer('per_page', 15);

        $wastes = $this->wastes->list($filters, $perPage);

        return $this->success([
            'items' => WasteResource::collection($wastes),
            'pagination' => [
                'current_page' => $wastes->currentPage(),
                'per_page' => $wastes->perPage(),
                'total' => $wastes->total(),
                'last_page' => $wastes->lastPage(),
            ],
        ]);
    }

    public function store(StoreWasteRequest $request): JsonResponse
    {
        $waste = $this->wastes->create(
            $request->validated('household_id'),
            $request->validated('type')
        );

        return $this->success(new WasteResource($waste), 'Waste pickup request created.', 201);
    }

    public function schedule(ScheduleWasteRequest $request, string $id): JsonResponse
    {
        $waste = $this->wastes->schedule(
            $id,
            Carbon::parse($request->validated('pickup_date')),
            (bool) $request->boolean('safety_confirmed')
        );

        return $this->success(new WasteResource($waste), 'Waste pickup scheduled.');
    }

    public function complete(string $id): JsonResponse
    {
        $waste = $this->wastes->complete($id);

        return $this->success(new WasteResource($waste), 'Waste pickup completed.');
    }

    public function cancel(string $id): JsonResponse
    {
        $waste = $this->wastes->cancel($id);

        return $this->success(new WasteResource($waste), 'Waste pickup canceled.');
    }
}
