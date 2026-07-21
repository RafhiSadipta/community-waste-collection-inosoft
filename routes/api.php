<?php

use App\Http\Controllers\Api\HouseholdController;
use Illuminate\Support\Facades\Route;

Route::apiResource('households', HouseholdController::class);
