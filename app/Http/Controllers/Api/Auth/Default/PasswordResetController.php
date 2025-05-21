<?php

namespace App\Http\Controllers\Api\Auth\Default;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordChangeRequest;
use App\Http\Requests\PasswordResetRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    public function sendResetLink(PasswordResetRequest $request) {

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return response()->json(['message' => __($status)]);
    }

    public function resetPassword(PasswordChangeRequest $request) {

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password)
                ])->save();
            }
        );

        return $status == Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password reset successful.']) // TODO: This will be replaced with LOCALE
            : response()->json(['message' => __($status)], 400);
    }
}
