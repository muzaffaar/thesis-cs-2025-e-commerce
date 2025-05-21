<?php

namespace App\Http\Controllers\Api\Auth\Default;

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
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful', // TODO: message will be replaced to LOCALE
            'user' => $user,
            'redirectUrl' => $this->identifyRedirectUrlByRole($user),
            'token' => $token,
        ]);
    }

    /**
     * @param $user
     * @return String url according to user role
     */
    private function identifyRedirectUrlByRole($user) {

        $roleNames = $user->roles->pluck('name')->toArray();

        $isAdmin = collect($roleNames)->contains(function ($role) {
            return $role === 'admin' || preg_match('/^admin_[a-z]+$/', $role);
        });

        return $isAdmin ? config('urls.home_admin') : config('urls.home');
    }
}
