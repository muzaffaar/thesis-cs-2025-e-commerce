<?php

namespace App\Http\Controllers\Api\Auth\Default;

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
                'message' => 'Invalid credentials' // TODO: message will be replaced to LOCALE
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('auth_token')->plainTextToken;

        // TODO: This return body must be replaced into another dedicated method that received params and makes decorated return body
        return response()->json([
            'message' => 'Login successful', // TODO: message will be replaced to LOCALE
            'user' => $user,
            'redirectUrl' => UserRoleIdentifier::identifyRedirectUrlByRole($user),
            'token' => $token,
        ]);
    }
}
