<?php

use App\Http\Controllers\Api\Auth\Default\LoginController;
use App\Http\Controllers\Api\Auth\Default\RegisterController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // NON-AUTHENTICATED ROUTES
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/password/reset');
    Route::post('/login/google');
    // AUTHENTICATED ROUTES
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout');
    });
});
