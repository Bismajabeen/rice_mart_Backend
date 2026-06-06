<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class SuperAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // =========================================
        // CREATE SUPER ADMIN TEST USER
        // =========================================

        $user = User::firstOrCreate(
            ['email' => 'superadmin@test.com'],
            [
                'name'              => 'Super Admin',
                'email'             => 'superadmin@test.com',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // =========================================
        // ASSIGN SUPER ADMIN ROLE
        // =========================================

        $superAdminRole = Role::where('name', 'super_admin')
            ->where('guard_name', 'web')
            ->firstOrFail();

        $user->assignRole($superAdminRole);

        // =========================================
        // ASSIGN ALL PERMISSIONS DIRECTLY TO USER
        // =========================================

        $user->syncPermissions(
            \Spatie\Permission\Models\Permission::where('guard_name', 'web')
                ->pluck('name')
                ->toArray()
        );

        $this->command->info('Super Admin user created: superadmin@test.com / password');
    }
}
