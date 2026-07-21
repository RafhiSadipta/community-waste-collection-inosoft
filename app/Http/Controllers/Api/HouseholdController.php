<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Household\StoreHouseholdRequest;
use App\Http\Requests\Household\UpdateHouseholdRequest;
use App\Http\Resources\HouseholdResource;
use App\Services\HouseholdService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HouseholdController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly HouseholdService $households,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'block', 'no']);
        $perPage = (int) $request->integer('per_page', 15);

        $households = $this->households->list($filters, $perPage);

        return $this->success([
            'items' => HouseholdResource::collection($households),
            'pagination' => [
                'current_page' => $households->currentPage(),
                'per_page' => $households->perPage(),
                'total' => $households->total(),
                'last_page' => $households->lastPage(),
            ],
        ]);
    }

    public function store(StoreHouseholdRequest $request): JsonResponse
    {
        $household = $this->households->create($request->validated());

        return $this->success(new HouseholdResource($household), 'Household created.', 201);
    }

    public function show(string $id): JsonResponse
    {
        $household = $this->households->find($id);

        return $this->success(new HouseholdResource($household));
    }

    public function update(UpdateHouseholdRequest $request, string $id): JsonResponse
    {
        $household = $this->households->update($id, $request->validated());

        return $this->success(new HouseholdResource($household), 'Household updated.');
    }

    public function destroy(string $id): JsonResponse
    {
        $this->households->delete($id);

        return $this->success(null, 'Household deleted.');
    }
}
