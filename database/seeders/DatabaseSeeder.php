<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /*
         |---------------------------------------------------------------------------
         | SEED PERMISSIONS
         |---------------------------------------------------------------------------
         */
        $this->call([
            CatalogPermissionSeeder::class,
            /*
             |-----------------------------------------------------------------------
             | CALL OTHER PERMISSION SEEDERS ACCORDINGLY
             |-----------------------------------------------------------------------
             */
            RolePermissionSeeder::class,
        ]);
        /*
         |---------------------------------------------------------------------------
         | CREATE SUPER ADMIN
         |---------------------------------------------------------------------------
         */
        $this->call([
            CreateSuperAdmin::class,
        ]);
        /*
         |---------------------------------------------------------------------------
         | SEED CATALOGS AND PRODUCTS
         |---------------------------------------------------------------------------
         */
    }
}
