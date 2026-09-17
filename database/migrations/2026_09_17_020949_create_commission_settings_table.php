<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('percentage', 5, 2); // e.g. 5.00, 4.50, 7.25
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // Seed an initial 5% row so the table isn't empty on day one
        \DB::table('commission_settings')->insert([
            'percentage' => 5.00,
            'updated_by' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_settings');
    }
};