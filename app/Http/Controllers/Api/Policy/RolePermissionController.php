<?php

namespace App\Http\Controllers\Api\Policy;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignPermissionRequest;
use App\Http\Requests\AssignRoleRequest;
use App\Http\Requests\CreateRoleRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function createRole(CreateRoleRequest $request): \Illuminate\Http\JsonResponse
    {
        $role = Role::create(['name' => $request->name, 'guard_name' => config('auth.defaults.guard')]);

        return response()->json(['message' => __('messages.role_created'), 'role' => $role], 201);
    }

    public function assignPermissions(AssignPermissionRequest $request): \Illuminate\Http\JsonResponse
    {
        $role = Role::findByName($request->role);
        $role->syncPermissions($request->permissions);

        return response()->json(['message' => __('messages.permissions_assigned_to_role')]);
    }

    public function assignRole(AssignRoleRequest $request, string $locale, User $user): \Illuminate\Http\JsonResponse
    {
        if (Auth::user()->id !== $user->id) {

            $role = Role::where('name', $request['role'])->where('guard_name', config('auth.defaults.guard'))->firstOrFail();

            $user->assignRole($role);

            return response()->json([
                'message' => __('messages.role_assigned_to_user'),
                'user' => $user->load('roles.permissions'),
            ]);
        }

        return response()->json([
            'message' => __('messages.unable_to_assign_own_role'),
        ], 403);
    }

    public function unassignRole(AssignRoleRequest $request, string $locale, User $user): \Illuminate\Http\JsonResponse
    {
        if (Auth::user()->id !== $user->id) {

            $user->removeRole($request['role']);

            return response()->json([
                'message' => __('messages.role_removed_from_user'),
                'user' => $user->load('roles'),
            ]);
        }

        return response()->json([
            'message' => __('messages.unable_to_remove_own_role')
        ], 403);
    }
}
