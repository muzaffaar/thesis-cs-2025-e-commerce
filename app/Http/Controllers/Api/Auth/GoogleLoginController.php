<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\Auth\Default\utils\UserRoleIdentifier;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller implements Loginable
{

    public function redirect(): string
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request): string
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::where('email', $googleUser->email)->first();

        if(!$user){
            $user = User::create([
                'google_id' => $googleUser->id,
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'password' => bcrypt(Str::random(40)),
                'email_verified_at' => now(),
            ]);
        }

        Auth::login($user);

        return response()->json([
            'message' => 'Login successful', // TODO: message will be replaced to LOCALE
            'user' => $user,
            'redirectUrl' => UserRoleIdentifier::identifyRedirectUrlByRole($user),
            'token' => $googleUser->token,
        ]);
    }
}
