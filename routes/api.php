<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\DealController;
use Illuminate\Support\Facades\Route;
use App\Enums\DealStage;
use App\Http\Controllers\Api\CompanyController;

Route::apiResource('deals', DealController::class);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Bedrijven (met contacten) voor de dropdowns
Route::get('companies', [CompanyController::class, 'index']);

// Fase-opties rechtstreeks uit de enum → single source of truth
Route::get('deal-stages', fn () => collect(DealStage::cases())->map(fn ($stage) => [
    'value' => $stage->value,
    'label' => $stage->label(),
]));
