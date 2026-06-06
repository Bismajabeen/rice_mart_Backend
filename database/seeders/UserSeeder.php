<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Shop;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // =========================================
        // SUPER ADMIN
        // =========================================

        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@test.com'],
            [
                'name'              => 'Super Admin',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $superAdmin->syncRoles('super_admin');
        $superAdmin->syncPermissions(
            \Spatie\Permission\Models\Permission::where('guard_name', 'web')
                ->pluck('name')
                ->toArray()
        );

        // =========================================
        // ADMIN
        // =========================================

        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name'              => 'Admin User',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $admin->syncRoles('admin');

        // =========================================
        // SELLER + SHOP
        // =========================================

        $seller = User::firstOrCreate(
            ['email' => 'seller@test.com'],
            [
                'name'              => 'Seller User',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $seller->syncRoles('seller');

        // Create shop for seller
        Shop::firstOrCreate(
            ['user_id' => $seller->id],
            [
                'shop_name'   => 'Rice Mart Test Shop',
                'owner_name'  => 'Seller User',
                'phone'       => '03001234567',
                'address'     => 'Lahore, Pakistan',
                'cnic'        => '3520212345678',
                'cnic_image'  => null,
                'description' => 'Test shop for Rice Mart demo',
                'status'      => 'approved',
                'is_approved' => 1,
            ]
        );

        // =========================================
        // CUSTOMER (BUYER)
        // =========================================

        $customer = User::firstOrCreate(
            ['email' => 'customer@test.com'],
            [
                'name'              => 'Customer User',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $customer->syncRoles('customer');

        // =========================================
        // SUMMARY
        // =========================================

        $this->command->info('Users seeded successfully:');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['super_admin', 'superadmin@test.com', 'password'],
                ['admin',       'admin@test.com',      'password'],
                ['seller',      'seller@test.com',     'password'],
                ['customer',    'customer@test.com',   'password'],
            ]
        );

        $this->command->info('Shop seeded: Rice Mart Test Shop (seller@test.com)');
    }
}
