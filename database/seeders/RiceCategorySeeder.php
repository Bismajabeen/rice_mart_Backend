<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RiceCategory;

class RiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [

            [
                'name' => 'IRRI-6',
                'image' => 'null',
                'status' => true,
            ],

            [
                'name' => 'Super Kernel Basmati',
                'image' => 'null',
                'status' => true,
            ],

            [
                'name' => '1121 Steam Basmati',
                'image' => 'null',
                'status' => true,
            ],

            [
                'name' => '1121 Sella Basmati',
                'image' => 'null',
                'status' => true,
            ],

            [
                'name' => 'Brown Rice',
                'image' => 'null',
                'status' => true,
            ],

            [
                'name' => 'Jasmine Rice',
                'image' => 'null',
                'status' => true,
            ],

            [
                'name' => 'PK-386 Rice',
                'image' => 'null',
                'status' => true,
            ],

            [
                'name' => 'Sindhi Rice',
                'image' => 'null',
                'status' => true,
            ],

            [
                'name' => 'White Rice',
                'image' => 'null',
                'status' => true,
            ],

            [
                'name' => 'Parboiled Rice',
                'image' => 'null',
                'status' => true,
            ],

            [
                'name' => 'Broken Rice',
                'image' => 'null',
                'status' => true,
            ],

            [
                'name' => 'Organic Rice',
                'image' => 'null',
                'status' => true,
            ],
        ];

        foreach ($categories as $category) {

            RiceCategory::create($category);
        }
    }
}