<?php

namespace App\Http\Controllers\Api\Auth\Default\utils;

class UserRoleIdentifier
{
    /**
     * @param $user
     * @return string returns redirect URL according user role. E.g. if user has admin roles then returns "/admin/dashboard", otherwise it returns "/home"
     */
    public static function identifyRedirectUrlByRole($user): string
    {

        $roleNames = $user->roles->pluck('name')->toArray();

        $isAdmin = collect($roleNames)->contains(function ($role) {
            return $role === 'admin' || preg_match('/^admin_[a-z]+$/', $role);
        });

        return $isAdmin ? config('urls.home_admin') : config('urls.home');
    }
}
