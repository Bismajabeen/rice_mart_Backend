<?php

namespace Database\Seeders;

use App\Models\RiceCategory;
use Illuminate\Database\Seeder;

class RiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Basmati Rice',
                'model_label' => 'basmati',
                'cooking_time' => '18-20 minutes',
                'common_uses' => 'Biryani, Pulao, Fried Rice, Roz ka khana',
                'description' => 'Long grain, aromatic rice mostly grown in Pakistan and India, best for biryani and pulao because of its fluffy and separate grains after cooking.',
            ],
            [
                'name' => 'Parboiled Rice',
                'model_label' => 'parboiled',
                'cooking_time' => '20-25 minutes',
                'common_uses' => 'Daily cooking, Khichdi, Rice and curry combos',
                'description' => 'Partially boiled in the husk before milling, retains more nutrients and does not stick together, commonly used for everyday meals.',
            ],
            [
                'name' => 'Sella Rice',
                'model_label' => 'sella',
                'cooking_time' => '20 minutes',
                'common_uses' => 'Biryani, Wedding events, Restaurant style rice dishes',
                'description' => 'A type of parboiled basmati rice, golden in color before cooking, known for extra-long grains and firmness, popular for large-scale cooking.',
            ],
            [
                'name' => 'Brown Rice',
                'model_label' => 'brown',
                'cooking_time' => '35-45 minutes',
                'common_uses' => 'Healthy diet meals, Weight loss diets, Salads',
                'description' => 'Whole grain rice with the bran layer intact, higher in fiber and nutrients, takes longer to cook compared to white rice.',
            ],
            [
                'name' => 'Sticky (Glutinous) Rice',
                'model_label' => 'sticky',
                'cooking_time' => '20-30 minutes (soaking required)',
                'common_uses' => 'Desserts, Asian dishes, Rice cakes',
                'description' => 'Short grain rice that becomes sticky when cooked, mainly used in Asian sweet and savory dishes.',
            ],
        ];

        foreach ($categories as $category) {
            RiceCategory::updateOrCreate(
                ['model_label' => $category['model_label']],
                $category
            );
        }
    }
}