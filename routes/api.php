<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DealController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'user']);

    Route::prefix('v1')->group(function () {
        Route::apiResource('companies', CompanyController::class);
        Route::post('/companies/{company}/contacts', [CompanyController::class, 'attachContact']);
        Route::apiResource('contacts', ContactController::class);
        Route::get('contacts/{contact}/deals', [ContactController::class, 'deals']);
        Route::apiResource('deals', DealController::class);
    });
});
