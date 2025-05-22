<?php

namespace App\Http\Controllers\Api\Auth\Default;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function logout(Request $request) {

        $request->user()->token()->revoke();

        return response()->json([
            'message' => __('auth.logout_success')
        ]);
    }
}
