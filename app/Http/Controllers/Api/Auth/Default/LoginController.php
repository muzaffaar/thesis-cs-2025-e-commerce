<?php

namespace App\Http\Controllers\Api\Auth\Default;

use App\Http\Controllers\Api\Auth\Default\utils\CheckingEmailVerification;
use App\Http\Controllers\Api\Auth\Default\utils\UserRoleIdentifier;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(LoginRequest $request) {
        $user = User::where('email', $request['email'])->first();

        if (!$user || !Hash::check($request['password'], $user->password)) {
            return response()->json([
                'message' => __('auth.failed')
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => __('auth.login_success'),
            'user' => $user,
            'redirectUrl' => UserRoleIdentifier::identifyRedirectUrlByRole($user),
            'token' => $token,
        ]);
    }
}
