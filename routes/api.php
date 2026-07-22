<?php

use App\Http\Controllers\Api\HouseholdController;
use App\Http\Controllers\Api\WasteController;
use Illuminate\Support\Facades\Route;

Route::apiResource('households', HouseholdController::class);

Route::post('pickups', [WasteController::class, 'store']);
Route::get('pickups', [WasteController::class, 'index']);
Route::put('pickups/{id}/schedule', [WasteController::class, 'schedule']);
Route::put('pickups/{id}/complete', [WasteController::class, 'complete']);
Route::put('pickups/{id}/cancel', [WasteController::class, 'cancel']);
