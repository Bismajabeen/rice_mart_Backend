<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate(
            ['key' => 'super_admin_email'],
            ['value' => 'samiairshad090@gmail.com']
        );

        Setting::updateOrCreate(
            ['key' => 'super_admin_phone'],
            ['value' => '+92302 4539786']
        );
    }
}