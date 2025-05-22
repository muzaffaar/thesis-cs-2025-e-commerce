<?php

namespace App\Http\Controllers\Api\Auth\Default;

use App\Http\Controllers\Api\Auth\Default\utils\CheckingEmailVerification;
use App\Http\Controllers\Api\Auth\Default\utils\UserRoleIdentifier;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request) {

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => __('auth.failed')
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => __('auth.login_success'),
            'user' => $user,
            'redirectUrl' => UserRoleIdentifier::identifyRedirectUrlByRole($user),
            'token' => $token,
        ]);
    }
}
