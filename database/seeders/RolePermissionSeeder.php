<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {

        app()[PermissionRegistrar::class]
            ->forgetCachedPermissions();

        $permissions = [

            'view customer dashboard',

            'view seller dashboard',

            'view admin dashboard',

            'view public products',
            'view own products',
            'view all products',

            'search products',

            'create products',

            'update own products',
            'update any products',

            'delete own products',
            'delete any products',

            'view public shops',
            'view own shop',
            'view all shops',

            'search shops',

            'create shop',

            'update own shop',
            'update any shop',

            'delete own shop',
            'delete any shop',

            'approve shops',
            'reject shops',

            'remove sellers',

            'create seller request',
            'view own seller request',

            'add to cart',
            'view cart',
            'update cart',
            'remove from cart',

            'create order',
            'checkout orders',

            'view own orders',
            'view shop orders',
            'view all orders',

            'view own order details',
            'view shop order details',
            'view all order details',

            'track own orders',

            'cancel own orders',

            'update order status',
            'update any order status',

            'create payment',

            'view own payments',
            'view shop payments',
            'view all payments',

            'manage payments',
            'receive payments',

            'view own payouts',

            'chat with sellers',
            'chat with customers',

            'send messages',

            'view own messages',
            'view shop messages',

            'view own notifications',
            'view all notifications',

            'send notifications',
            'send customer notifications',

            'update own profile',

            'update own settings',
            'manage settings',

            'create address',

            'view own address',

            'update own address',

            'delete own address',

            'create reviews',
            'view reviews',

            'view feedback',
            'reply feedback',

            'view customer delivery info',

            'manage own inventory',

            'view own analytics',
            'view all analytics',

            'view reports',
            'export reports',

            'view users',

            'create users',

            'update users',

            'delete users',

            'create sellers',
            'view sellers',
            'update sellers',

            'search system',

            'create categories',
            'view categories',
            'update categories',
            'delete categories',

            'manage cities',

            'file complaints',
            
            'view complaints',
            
            'manage complaints',

            'view roles',
            'create roles',
            'update roles',
            'delete roles',
            'assign roles',


            'create permissions',
            'update permissions',
            'delete permissions',
            'assign permissions',

            'manage commission',

            'manage system',
            'backup system',
            'restore system',

            'full access',
        ];

        foreach ($permissions as $permission) {

            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $customer = Role::firstOrCreate([
            'name' => 'customer',
            'guard_name' => 'web',
        ]);

        $seller = Role::firstOrCreate([
            'name' => 'seller',
            'guard_name' => 'web',
        ]);

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $superAdmin = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        $customer->syncPermissions([

            'view customer dashboard',

            'view public products',
            'search products',

            'view public shops',
            'search shops',

            'create shop',

            'create seller request',
            'view own seller request',

            'update own shop',

            'add to cart',
            'view cart',
            'update cart',
            'remove from cart',

            'create order',
            'checkout orders',

            'view own orders',
            'view own order details',

            'track own orders',

            'cancel own orders',

            'create payment',
            'view own payments',

            'chat with sellers',

            'send messages',

            'view own messages',

            'view own notifications',

            'update own profile',

            'update own settings',

            'create address',
            'view own address',
            'update own address',
            'delete own address',

            'create reviews',
            'view reviews',

            'file complaints',
        ]);

        $seller->syncPermissions([

            'view seller dashboard',

            'view public products',
            'search products',

            'view public shops',
            'search shops',

            'view own shop',
            'update own shop',
            'delete own shop',

            'create products',

            'view own products',

            'update own products',

            'delete own products',

            'view shop orders',
            'view shop order details',

            'update order status',

            'view customer delivery info',

            'view own analytics',

            'chat with customers',

            'send messages',

            'view shop messages',

            'send customer notifications',

            'view own notifications',

            'view shop payments',
            'view own payouts',

            'manage own inventory',

            'update own profile',

            'update own settings',

            'file complaints',
        ]);

       if ($admin->permissions()->count() === 0) {
         $admin->syncPermissions([
            'view admin dashboard',
        ]);
        }

        $superAdmin->syncPermissions($permissions);
    }
}