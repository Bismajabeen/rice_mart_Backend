<?php 

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RiceCategorySeeder::class,
            RoleSeeder::class,
            RolePermissionSeeder::class,
        ]);
    }
}