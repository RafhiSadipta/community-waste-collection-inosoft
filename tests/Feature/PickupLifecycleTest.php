<?php

namespace Tests\Feature;

use App\Models\Household;
use Tests\TestCase;

class PickupLifecycleTest extends TestCase
{
    public function test_full_pickup_lifecycle_enforces_business_rules(): void
    {
        $household = Household::create([
            'owner_name' => 'Test Household',
            'address' => 'Jl. Testing No. 1',
        ]);

        // Create pickup (organic)
        $create = $this->postJson('/api/pickups', [
            'household_id' => $household->id,
            'type' => 'organic',
        ]);
        $create->assertStatus(201);
        $create->assertJsonPath('data.type', 'organic');
        $pickupId = $create->json('data.id');

        // Rule #2: schedule from pending succeeds
        $schedule = $this->putJson("/api/pickups/{$pickupId}/schedule", [
            'pickup_date' => now()->addDays(2)->toDateTimeString(),
        ]);
        $schedule->assertStatus(200);
        $schedule->assertJsonPath('data.status', 'scheduled');

        // Rule #2: scheduling again (no longer pending) is rejected
        $this->putJson("/api/pickups/{$pickupId}/schedule", [
            'pickup_date' => now()->addDays(3)->toDateTimeString(),
        ])->assertStatus(409);

        // Complete pickup -> Rule #3: payment auto-generated
        $complete = $this->putJson("/api/pickups/{$pickupId}/complete");
        $complete->assertStatus(200);
        $complete->assertJsonPath('data.status', 'completed');

        $pending = $this->getJson("/api/payments?household_id={$household->id}&status=pending");
        $pending->assertStatus(200);
        $pending->assertJsonCount(1, 'data.items');
        $pending->assertJsonPath('data.items.0.amount', 50000);
        $paymentId = $pending->json('data.items.0.id');

        // Rule #1: new pickup blocked while payment unpaid
        $this->postJson('/api/pickups', [
            'household_id' => $household->id,
            'type' => 'plastic',
        ])->assertStatus(409);

        // Settle the payment
        $confirm = $this->putJson("/api/payments/{$paymentId}/confirm");
        $confirm->assertStatus(200);
        $confirm->assertJsonPath('data.status', 'paid');

        // Rule #1: new pickup allowed again
        $this->postJson('/api/pickups', [
            'household_id' => $household->id,
            'type' => 'plastic',
        ])->assertStatus(201);
    }

    public function test_electronic_pickup_requires_safety_confirmation_before_scheduling(): void
    {
        $household = Household::create([
            'owner_name' => 'Test Household Electronic',
            'address' => 'Jl. Testing No. 2',
        ]);

        $create = $this->postJson('/api/pickups', [
            'household_id' => $household->id,
            'type' => 'electronic',
        ]);
        $create->assertStatus(201);
        $create->assertJsonPath('data.safety_check', false);
        $pickupId = $create->json('data.id');

        // Without safety confirmation -> blocked
        $this->putJson("/api/pickups/{$pickupId}/schedule", [
            'pickup_date' => now()->addDay()->toDateTimeString(),
        ])->assertStatus(409);

        // With safety confirmation -> succeeds
        $scheduled = $this->putJson("/api/pickups/{$pickupId}/schedule", [
            'pickup_date' => now()->addDay()->toDateTimeString(),
            'safety_confirmed' => true,
        ]);
        $scheduled->assertStatus(200);
        $scheduled->assertJsonPath('data.safety_check', true);

        // Completing electronic should generate a 100000 payment (not 50000)
        $this->putJson("/api/pickups/{$pickupId}/complete")->assertStatus(200);

        $pending = $this->getJson("/api/payments?household_id={$household->id}&status=pending");
        $pending->assertJsonPath('data.items.0.amount', 100000);
    }
}
