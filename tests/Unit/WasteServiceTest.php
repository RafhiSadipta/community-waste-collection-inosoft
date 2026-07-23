<?php

namespace Tests\Unit;

use App\Exceptions\InvalidStatusTransitionException;
use App\Exceptions\UnpaidPaymentExistsException;
use App\Models\Household;
use App\Models\WasteElectronic;
use App\Models\WasteOrganic;
use App\Repositories\Contracts\HouseholdRepositoryInterface;
use App\Repositories\Contracts\WasteRepositoryInterface;
use App\Services\PaymentService;
use App\Services\WasteService;
use Tests\TestCase;

class WasteServiceTest extends TestCase
{
    public function test_create_throws_when_household_has_unpaid_payment(): void
    {
        $wastes = $this->createMock(WasteRepositoryInterface::class);
        $households = $this->createMock(HouseholdRepositoryInterface::class);
        $payments = $this->createMock(PaymentService::class);

        $households->method('find')->willReturn(new Household());
        $payments->method('hasUnpaidByHousehold')->willReturn(true);

        $service = new WasteService($wastes, $households, $payments);

        $this->expectException(UnpaidPaymentExistsException::class);

        $service->create('household-id', 'organic');
    }

    public function test_schedule_throws_when_electronic_missing_safety_check(): void
    {
        $wastes = $this->createMock(WasteRepositoryInterface::class);
        $households = $this->createMock(HouseholdRepositoryInterface::class);
        $payments = $this->createMock(PaymentService::class);

        $waste = new WasteElectronic(['status' => 'pending', 'safety_check' => false]);
        $wastes->method('find')->willReturn($waste);

        $service = new WasteService($wastes, $households, $payments);

        $this->expectException(InvalidStatusTransitionException::class);

        $service->schedule('waste-id', now());
    }

    public function test_schedule_succeeds_for_organic_without_safety_check(): void
    {
        $wastes = $this->createMock(WasteRepositoryInterface::class);
        $households = $this->createMock(HouseholdRepositoryInterface::class);
        $payments = $this->createMock(PaymentService::class);

        $waste = new WasteOrganic(['status' => 'pending']);
        $wastes->method('find')->willReturn($waste);

        $service = new WasteService($wastes, $households, $payments);
        $result = $service->schedule('waste-id', now()->addDay());

        $this->assertSame('scheduled', $result->status);
    }

    public function test_complete_throws_when_status_is_not_scheduled(): void
    {
        $wastes = $this->createMock(WasteRepositoryInterface::class);
        $households = $this->createMock(HouseholdRepositoryInterface::class);
        $payments = $this->createMock(PaymentService::class);

        $waste = new WasteOrganic(['status' => 'pending']);
        $wastes->method('find')->willReturn($waste);

        $service = new WasteService($wastes, $households, $payments);

        $this->expectException(InvalidStatusTransitionException::class);

        $service->complete('waste-id');
    }

    public function test_complete_generates_payment_via_payment_service(): void
    {
        $wastes = $this->createMock(WasteRepositoryInterface::class);
        $households = $this->createMock(HouseholdRepositoryInterface::class);
        $payments = $this->createMock(PaymentService::class);

        $waste = new WasteElectronic(['status' => 'scheduled']);
        $wastes->method('find')->willReturn($waste);

        $payments->expects($this->once())
            ->method('createFromWaste')
            ->with($waste);

        $service = new WasteService($wastes, $households, $payments);
        $result = $service->complete('waste-id');

        $this->assertSame('completed', $result->status);
    }
}
