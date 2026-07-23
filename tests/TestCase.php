<?php

namespace Tests;

use App\Models\Household;
use App\Models\Payment;
use App\Models\Waste;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // MongoDB has no SQL-style transaction rollback between tests, so we
        // clear the domain collections before each test to keep them isolated.
        Household::query()->delete();
        Waste::query()->delete();
        Payment::query()->delete();
    }
}
