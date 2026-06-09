<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\DealController;
use Illuminate\Support\Facades\Route;

Route::apiResource('deals', DealController::class);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
