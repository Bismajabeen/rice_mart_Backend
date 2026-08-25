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
            RolePermissionSeeder::class, 
            UserSeeder::class,
            SettingsSeeder::class,        
        ]);
    }
}
