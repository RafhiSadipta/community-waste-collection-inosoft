<?php

namespace App\Providers;

use App\Repositories\Contracts\HouseholdRepositoryInterface;
use App\Repositories\Eloquent\HouseholdRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(HouseholdRepositoryInterface::class, HouseholdRepository::class);
    }
}
