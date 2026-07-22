<?php

namespace App\Providers;

use App\Repositories\Contracts\HouseholdRepositoryInterface;
use App\Repositories\Contracts\WasteRepositoryInterface;
use App\Repositories\Eloquent\HouseholdRepository;
use App\Repositories\Eloquent\WasteRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(HouseholdRepositoryInterface::class, HouseholdRepository::class);
        $this->app->bind(WasteRepositoryInterface::class, WasteRepository::class);
    }
}
