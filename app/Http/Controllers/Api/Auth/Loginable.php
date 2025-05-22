<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;

/**
 * This class defines how Socialite login controllers should be.
 * E.g, Google, Facebook, Amazon, Apple, ... login controllers should have those methods.
 */
interface Loginable
{
    public function redirect() : string;
    public function callback(Request $request) : string;
}
