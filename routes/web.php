<?php

use App\Http\Controllers\Api\Auth\GoogleLoginController;
use Illuminate\Support\Facades\Route;

Route::get('/api/v1/auth/google/redirect', [GoogleLoginController::class, 'redirect']);
Route::get('/api/v1/auth/google/callback', [GoogleLoginController::class, 'callback']);
