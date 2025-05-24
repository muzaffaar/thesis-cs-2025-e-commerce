<?php

namespace App\Http\Controllers\Api\Policy;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignPermissionRequest;
use App\Http\Requests\AssignRoleRequest;
use App\Http\Requests\CreateRoleRequest;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function createRole(CreateRoleRequest $request)
    {
        $role = Role::create(['name' => $request->name, 'guard_name' => config('auth.defaults.guard')]);

        return response()->json(['message' => 'Role created', 'role' => $role], 201);
    }

    public function assignPermissions(AssignPermissionRequest $request)
    {
        $role = Role::findByName($request->role);
        $role->syncPermissions($request->permissions);

        return response()->json(['message' => 'Permissions assigned to role']);
    }

    public function assignRole(AssignRoleRequest $request, string $locale, User $user)
    {
        $role = Role::where('name', $request['role'])->where('guard_name', config('auth.defaults.guard'))->firstOrFail();

        $user->assignRole($role);

        return response()->json([
            'message' => "Role '{$role->name}' assigned to user.",
            'user' => $user->load('roles.permissions'),
        ]);
    }

    public function removeRole(AssignRoleRequest $request, string $locale, User $user) {
        $user->removeRole($request['role']);

        return response()->json([
            'message' => "Role '{$request['role']}' removed from user.",
            'user' => $user->load('roles'),
        ]);
    }

    public function permissions() {
        return response()->json([
            'roles' => Permission::all(),
        ]);
    }

    public function roles() {
        return response()->json([
            'roles' => Role::all(),
        ]);
    }
}
