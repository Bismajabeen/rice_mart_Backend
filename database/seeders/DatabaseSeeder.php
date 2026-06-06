<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RiceCategorySeeder::class,
            RoleSeeder::class,
            RolePermissionSeeder::class,  // must run before UserSeeder
            UserSeeder::class,            // replaces SuperAdminUserSeeder
        ]);
    }
}
