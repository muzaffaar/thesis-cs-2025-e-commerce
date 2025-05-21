<?php

use App\Http\Controllers\Api\Auth\Default\EmailVerificationController;
use App\Http\Controllers\Api\Auth\Default\LoginController;
use App\Http\Controllers\Api\Auth\Default\LogoutController;
use App\Http\Controllers\Api\Auth\Default\PasswordResetController;
use App\Http\Controllers\Api\Auth\Default\RegisterController;
use App\Http\Controllers\Api\Auth\GoogleLoginController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::prefix('v1')->group(function () {
    /**
     * Default login routes
     */
    Route::post('/auth/register', [RegisterController::class, 'register']);
    Route::post('/auth/login', [LoginController::class, 'login']);
    Route::post('/auth/forgot-password', [PasswordResetController::class, 'sendResetLink']);
    Route::post('/auth/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.reset');

    Route::middleware(['auth:sanctum'])->group(function () {
        /**
         * Email verification
         */
        Route::post('/email/resend', [EmailVerificationController::class, 'resend'])->name('verification.resend');
        Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware('signed')->name('verification.verify');

        Route::middleware(['verified.api'])->group(function () {

        });

        Route::post('/auth/logout', [LogoutController::class, 'logout']);
    });
});
