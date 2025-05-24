<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAndRelatedRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$this->hasAdminRole($user)) {
            return response()->json(['message' => 'Forbidden. Admin access only.'], 403);
        }

        return $next($request);
    }

    private function hasAdminRole($user): bool {
        $prefix = config('permission.admin_roles_prefix');
        foreach ($user->getRoleNames() as $roleName) {
            if (preg_match("/^{$prefix}/", $roleName)) {
                return true;
            }
        }

        return false;
    }
}
