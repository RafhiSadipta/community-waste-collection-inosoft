<?php

use App\Http\Controllers\Api\HouseholdController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\WasteController;
use Illuminate\Support\Facades\Route;

Route::apiResource('households', HouseholdController::class);

Route::post('pickups', [WasteController::class, 'store']);
Route::get('pickups', [WasteController::class, 'index']);
Route::put('pickups/{id}/schedule', [WasteController::class, 'schedule']);
Route::put('pickups/{id}/complete', [WasteController::class, 'complete']);
Route::put('pickups/{id}/cancel', [WasteController::class, 'cancel']);

Route::post('payments', [PaymentController::class, 'store']);
Route::get('payments', [PaymentController::class, 'index']);
Route::put('payments/{id}/confirm', [PaymentController::class, 'confirm']);

Route::get('reports/waste-summary', [ReportController::class, 'wasteSummary']);
Route::get('reports/payment-summary', [ReportController::class, 'paymentSummary']);
Route::get('reports/households/{id}/history', [ReportController::class, 'householdHistory']);
