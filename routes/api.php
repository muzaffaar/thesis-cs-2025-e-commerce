<?php

use App\Http\Controllers\Api\Auth\Default\LoginController;
use App\Http\Controllers\Api\Auth\Default\PasswordResetController;
use App\Http\Controllers\Api\Auth\Default\RegisterController;
use App\Http\Controllers\Api\Auth\GoogleLoginController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::prefix('v1')->group(function () {
    // NON-AUTHENTICATED ROUTES
    /**
     * Default login routes
     */
    Route::post('/auth/register', [RegisterController::class, 'register']);
    Route::post('/auth/login', [LoginController::class, 'login']);
    Route::post('/auth/forgot-password', [PasswordResetController::class, 'sendResetLink']);
    Route::post('/auth/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.reset');
    /**
     * Socialite login service routes
     */
    // AUTHENTICATED ROUTES
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout');
    });
});
