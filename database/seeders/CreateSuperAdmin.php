<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdmin extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::transaction(function () {
                $guard = config('auth.defaults.guard');

                $user = User::firstOrCreate(
                    ['email' => config('auth.super_admin.login')],
                    [
                        'name' => 'Super Admin',
                        'password' => Hash::make(config('auth.super_admin.password')),
                        'email_verified_at' => now(),
                    ]
                );

                $role = Role::firstOrCreate(
                    ['name' => 'admin_super', 'guard_name' => $guard],
                );

                $permissions = Permission::all()->map(function ($permission) use ($guard) {
                    if ($permission->guard_name !== $guard) {
                        $permission->guard_name = $guard;
                        $permission->save();
                    }
                    return $permission;
                });

                $role->syncPermissions($permissions);

                $user->assignRole($role);

                $this->command->info('Super admin user created and assigned admin_super role with all permissions.');
            });
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
