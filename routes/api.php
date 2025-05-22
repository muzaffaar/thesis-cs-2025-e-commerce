<?php

use App\Http\Controllers\Api\Auth\Default\EmailVerificationController;
use App\Http\Controllers\Api\Auth\Default\LoginController;
use App\Http\Controllers\Api\Auth\Default\LogoutController;
use App\Http\Controllers\Api\Auth\Default\PasswordResetController;
use App\Http\Controllers\Api\Auth\Default\RegisterController;
use App\Http\Controllers\Api\Auth\GoogleLoginController;
use App\Http\Controllers\Api\Catalog\CatalogController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::group(['prefix' => '{locale}', 'where' => ['locale' => implode('|', config('locales.supported_locales'))], 'middleware' => ['setlocale']], function () {


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

            /**
             * Admin and admin related routes
             */
            Route::prefix('admin')->group(function () {
                Route::post('/catalogs', [CatalogController::class, 'store']);
                //....
                Route::put('/catalogs/{catalog}', [CatalogController::class, 'update']);
                Route::delete('/catalogs/{catalog}', [CatalogController::class, 'destroy']);
                Route::get('/catalogs/{catalog}', [CatalogController::class, 'show']);
                Route::get('/catalogs', [CatalogController::class, 'index']);
            });
        });
    });

});
