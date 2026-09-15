<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RiceCategory;
use Illuminate\Support\Facades\Schema;

class RiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing categories so this seeder reflects the
        // current, final category list rather than appending to it.
        Schema::disableForeignKeyConstraints();
        RiceCategory::truncate();
        Schema::enableForeignKeyConstraints();

        $categories = [

            [
                'name' => 'Basmati',
                'image' => null,
                'status' => true,
            ],

            [
                'name' => 'Extra Long Grain / Premium',
                'image' => null,
                'status' => true,
            ],

            [
                'name' => 'Long Grain Non-Basmati',
                'image' => null,
                'status' => true,
            ],

            [
                'name' => 'Medium Grain',
                'image' => null,
                'status' => true,
            ],

            [
                'name' => 'Sella Rice',
                'image' => null,
                'status' => true,
            ],

            [
                'name' => 'Steam Rice',
                'image' => null,
                'status' => true,
            ],

            [
                'name' => 'Brown Rice',
                'image' => null,
                'status' => true,
            ],

            [
                'name' => 'Broken Rice',
                'image' => null,
                'status' => true,
            ],

            [
                'name' => 'Hybrid Rice',
                'image' => null,
                'status' => true,
            ],

            [
                'name' => 'Super Gold',
                'image' => null,
                'status' => true,
            ],

            [
                'name' => 'Kainat Rice',
                'image' => null,
                'status' => true,
            ],

            [
                'name' => 'Long Grain White Basmati',
                'image' => null,
                'status' => true,
            ],

        ];

        foreach ($categories as $category) {

            RiceCategory::create($category);
        }
    }
}