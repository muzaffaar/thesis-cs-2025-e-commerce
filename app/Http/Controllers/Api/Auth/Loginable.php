<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;

interface Loginable
{
    public function redirect() : string;
    public function callback(Request $request) : string;
}
