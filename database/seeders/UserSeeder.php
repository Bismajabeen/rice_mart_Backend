<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Shop;
use App\Models\Product;
use App\Models\RiceCategory;

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
        // RICE CATEGORIES (needed so products have something to attach to)
        // firstOrCreate by name, so if you already have a RiceCategorySeeder
        // with these exact names, it will just reuse them instead of duplicating.
        // =========================================

        $categoryNames = ['Basmati', 'Sella', 'IRRI-6', 'Kainat'];

        $categories = collect($categoryNames)->map(function ($name) {
            return RiceCategory::firstOrCreate(
                ['name' => $name],
                ['status' => true]
            );
        });

        // =========================================
        // 4 SELLERS + 4 APPROVED SHOPS + 4 PRODUCTS EACH
        // =========================================

        for ($i = 1; $i <= 4; $i++) {

            $seller = User::firstOrCreate(
                ['email' => "seller{$i}@test.com"],
                [
                    'name'              => "Seller User {$i}",
                    'password'          => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            $seller->syncRoles('seller');

            $shop = Shop::firstOrCreate(
                ['user_id' => $seller->id],
                [
                    'shop_name'   => "Rice Mart Shop {$i}",
                    'owner_name'  => "Seller User {$i}",
                    'phone'       => '0300123456' . $i,
                    'address'     => 'Lahore, Pakistan',
                    'cnic'        => '352021234567' . $i,
                    'cnic_image'  => null,
                    'description' => "Approved test shop #{$i} for Rice Mart demo",
                    'status'      => 'approved',
                    'is_approved' => 1,
                ]
            );

            for ($p = 1; $p <= 4; $p++) {
                $category = $categories[($p - 1) % $categories->count()];

                Product::firstOrCreate(
                    [
                        'shop_id' => $shop->id,
                        'name'    => "{$category->name} Rice {$p}",
                    ],
                    [
                        'user_id'          => $seller->id,
                        'rice_category_id' => $category->id,
                        'price'            => rand(150, 400),
                        'stock'            => rand(50, 500),
                        'image'            => null,
                    ]
                );
            }
        }

        // =========================================
        // 4 CUSTOMERS (BUYERS)
        // =========================================

        for ($i = 1; $i <= 4; $i++) {
            $customer = User::firstOrCreate(
                ['email' => "customer{$i}@test.com"],
                [
                    'name'              => "Customer User {$i}",
                    'password'          => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            $customer->syncRoles('customer');
        }

        // =========================================
        // SUMMARY
        // =========================================

        $rows = [
            ['super_admin', 'superadmin@test.com', 'password'],
            ['admin',       'admin@test.com',      'password'],
        ];

        for ($i = 1; $i <= 4; $i++) {
            $rows[] = ["seller {$i}", "seller{$i}@test.com", 'password'];
        }

        for ($i = 1; $i <= 4; $i++) {
            $rows[] = ["customer {$i}", "customer{$i}@test.com", 'password'];
        }

        $this->command->info('Users seeded successfully:');
        $this->command->table(['Role', 'Email', 'Password'], $rows);
        $this->command->info('4 approved shops seeded, each with 4 products.');
    }
}
