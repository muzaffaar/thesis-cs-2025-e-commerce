<?php

use App\Http\Controllers\Api\Auth\Default\EmailVerificationController;
use App\Http\Controllers\Api\Auth\Default\LoginController;
use App\Http\Controllers\Api\Auth\Default\LogoutController;
use App\Http\Controllers\Api\Auth\Default\PasswordResetController;
use App\Http\Controllers\Api\Auth\Default\RegisterController;
use App\Http\Controllers\Api\Auth\GoogleLoginController;
use App\Http\Controllers\Api\Catalog\CatalogController;
use App\Http\Controllers\Api\Policy\RolePermissionController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

/*
 * ---------------------------------------------------------------------------------------------------------
 * SET A LOCALIZATION
 * ---------------------------------------------------------------------------------------------------------
 */
Route::group(['prefix' => '{locale}', 'where' => ['locale' => implode('|', config('locales.supported_locales'))], 'middleware' => ['setlocale']], function () {
    /*
     * ---------------------------------------------------------------------------------------------------------
     * API V1 ROUTES
     * ---------------------------------------------------------------------------------------------------------
     */
    Route::prefix('v1')->group(function () {
        /*
         * ---------------------------------------------------------------------------------------------------------
         * DEFAULT AUTHENTICATION ROUTES
         * ---------------------------------------------------------------------------------------------------------
         */
        Route::post('/auth/register', [RegisterController::class, 'register']);
        Route::post('/auth/login', [LoginController::class, 'login']);
        Route::post('/auth/forgot-password', [PasswordResetController::class, 'sendResetLink']);
        Route::post('/auth/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.reset');

        Route::middleware(['auth:sanctum'])->group(function () {
            /*
             * -----------------------------------------------------------------------------------------------------
             * EMAIL VERIFICATION ROUTES
             * -----------------------------------------------------------------------------------------------------
             */
            Route::post('/email/resend', [EmailVerificationController::class, 'resend'])->name('verification.resend');
            Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware('signed')->name('verification.verify');

            Route::middleware(['verified.api'])->group(function () {

                Route::post('/auth/logout', [LogoutController::class, 'logout']);

                /*
                 * -----------------------------------------------------------------------------------------------------
                 * ADMIN PAGE ROUTES
                 * -----------------------------------------------------------------------------------------------------
                 */
                Route::prefix('admin')->group(function () {
                    /*
                     * -------------------------------------------------------------------------------------------------
                     * POLICY MANAGEMENT ROUTES
                     * -------------------------------------------------------------------------------------------------
                     */
                    Route::post('/roles', [RolePermissionController::class, 'createRole']);
                    Route::post('/roles/assign-permissions', [RolePermissionController::class, 'assignPermissions']);
                    Route::post('/users/{user}/assign-role', [RolePermissionController::class, 'assignRole']);
                    /*
                     * --------------------------------------------------------------------------------------------------
                     * CATALOG MANAGEMENT ROUTES
                     * --------------------------------------------------------------------------------------------------
                     */
                    Route::post('/catalogs', [CatalogController::class, 'store']);
                    Route::put('/catalogs/{catalog}', [CatalogController::class, 'update']);
                    Route::delete('/catalogs/{catalog}', [CatalogController::class, 'destroy']);
                    Route::get('/catalogs/{catalog}', [CatalogController::class, 'show']);
                    Route::get('/catalogs', [CatalogController::class, 'index']);
                    /*
                     * -------------------------------------------------------------------------------------------------
                     * PRODUCT MANAGEMENT ROUTES
                     * -------------------------------------------------------------------------------------------------
                     */


                    /*
                     * -------------------------------------------------------------------------------------------------
                     * DISCOUNT MANAGEMENT ROUTES
                     * -------------------------------------------------------------------------------------------------
                     */
                });
            });

        });
    });

});
