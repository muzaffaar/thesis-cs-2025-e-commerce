<?php

namespace App\Http\Controllers\Api\Auth\Default;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => __('auth.email_already_verified')]);
        }

        $request->fulfill();
        return response()->json(['message' => __('auth.email_verified_success')]);
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => __('auth.email_already_verified')]);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => __('passwords.sent')]);
    }
}
