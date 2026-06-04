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
        // =========================================
        // RESET CACHE
        // =========================================

        app()[PermissionRegistrar::class]
            ->forgetCachedPermissions();

        // =========================================
        // ALL PERMISSIONS
        // =========================================

        $permissions = [

            // =========================
            // CUSTOMER DASHBOARD
            // =========================

            'view customer dashboard',

            // =========================
            // SELLER DASHBOARD
            // =========================

            'view seller dashboard',

            // =========================
            // ADMIN DASHBOARD
            // =========================

            'view admin dashboard',

            // =========================
            // PRODUCTS
            // =========================

            'view public products',
            'view own products',
            'view all products',

            'search products',

            'create products',

            'update own products',
            'update any products',

            'delete own products',
            'delete any products',

            // =========================
            // SHOPS
            // =========================

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

            // =========================
            // SELLER REQUESTS
            // =========================

            'create seller request',
            'view own seller request',

            // =========================
            // CART
            // =========================

            'add to cart',
            'view cart',
            'update cart',
            'remove from cart',

            // =========================
            // ORDERS
            // =========================

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

            // =========================
            // PAYMENTS
            // =========================

            'create payment',

            'view own payments',
            'view shop payments',
            'view all payments',

            'manage payments',
            'receive payments',

            // =========================
            // CHAT & MESSAGES
            // =========================

            'chat with sellers',
            'chat with customers',

            'send messages',

            'view own messages',
            'view shop messages',

            // =========================
            // NOTIFICATIONS
            // =========================

            'view own notifications',
            'view all notifications',

            'send notifications',
            'send customer notifications',

            // =========================
            // PROFILE & SETTINGS
            // =========================

            'update own profile',

            'update own settings',
            'manage settings',

            // =========================
            // ADDRESS MANAGEMENT
            // =========================

            'create address',

            'view own address',

            'update own address',

            'delete own address',

            // =========================
            // REVIEWS & FEEDBACK
            // =========================

            'create reviews',
            'view reviews',

            'view feedback',
            'reply feedback',

            // =========================
            // CUSTOMER DELIVERY INFO
            // =========================

            'view customer delivery info',

            // =========================
            // INVENTORY
            // =========================

            'manage own inventory',

            // =========================
            // ANALYTICS
            // =========================

            'view own analytics',
            'view all analytics',

            // =========================
            // REPORTS
            // =========================

            'view reports',
            'export reports',

            // =========================
            // USERS
            // =========================

            'view users',

            'create users',

            'update users',

            'delete users',

            // =========================
            // SELLERS
            // =========================

            'create sellers',
            'view sellers',
            'update sellers',

            // =========================
            // SEARCH SYSTEM
            // =========================

            'search system',

            // =========================
            // CATEGORIES
            // =========================

            'create categories',
            'view categories',
            'update categories',
            'delete categories',

            // =========================
            // ROLES
            // =========================

            'create roles',
            'update roles',
            'delete roles',
            'assign roles',

            // =========================
            // PERMISSIONS
            // =========================

            'create permissions',
            'update permissions',
            'delete permissions',
            'assign permissions',

            // =========================
            // SYSTEM
            // =========================

            'manage system',
            'backup system',
            'restore system',

            // =========================
            // FULL ACCESS
            // =========================

            'full access',
        ];

        // =========================================
        // CREATE PERMISSIONS
        // =========================================

        foreach ($permissions as $permission) {

            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // =========================================
        // CREATE ROLES
        // =========================================

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

        // =========================================
        // CUSTOMER PERMISSIONS
        // =========================================

        $customer->syncPermissions([

            'view customer dashboard',

            'view public products',
            'search products',

            'view public shops',
            'search shops',

            'create shop',

            'create seller request',
            'view own seller request',

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
        ]);

        // =========================================
        // SELLER PERMISSIONS
        // =========================================

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

            'manage own inventory',

            'update own profile',

            'update own settings',
        ]);

        // =========================================
        // ADMIN PERMISSIONS
        // =========================================

        $admin->syncPermissions([

            'view admin dashboard',

            'view all shops',
            'update any shop',
            'delete any shop',
            'approve shops',
            'reject shops',

            'view all products',
            'update any products',
            'delete any products',

            'view all orders',
            'view all order details',

            'update any order status',

            'view all payments',
            'manage payments',
            'receive payments',

            'view users',
            'create users',
            'update users',
            'delete users',

            'create sellers',
            'view sellers',
            'update sellers',

            'send notifications',
            'view all notifications',

            'view reports',
            'export reports',

            'view feedback',
            'reply feedback',

            'view all analytics',

            'search system',

            'create categories',
            'view categories',
            'update categories',
            'delete categories',

            'manage settings',
        ]);

        // =========================================
        // SUPER ADMIN PERMISSIONS
        // =========================================

        $superAdmin->syncPermissions($permissions);
    }
}