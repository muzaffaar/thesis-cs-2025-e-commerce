<?php

namespace App\Http\Controllers\Api\Auth\Default;

use App\Http\Controllers\Api\Auth\Default\utils\UserRoleIdentifier;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request) {

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token_e_commerce')->plainTextToken;

        return response()->json([
            'message' => __('auth.registered'),
            'user' => $user,
            'redirectUrl' => UserRoleIdentifier::identifyRedirectUrlByRole($user),
            'token' => $token,
        ], 201);
    }
}
