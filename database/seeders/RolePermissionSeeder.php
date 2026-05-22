<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache
        app()[\Spatie\Permission\PermissionRegistrar::class]
            ->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'manage users',
            'manage roles',
            'manage shops',
            'manage permissions',
            'approve shops',
            'manage products',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // Create Roles
        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);

        $seller = Role::firstOrCreate([
            'name' => 'seller',
            'guard_name' => 'web'
        ]);

        $buyer = Role::firstOrCreate([
            'name' => 'buyer',
            'guard_name' => 'web'
        ]);

        // Assign Permissions To Admin
        $admin->givePermissionTo(Permission::all());
    }
}