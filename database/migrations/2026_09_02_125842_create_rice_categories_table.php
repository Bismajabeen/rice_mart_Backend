<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rice_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('cooking_time');
            $table->text('common_uses');
            $table->text('description');
            $table->string('model_label')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rice_categories');
    }
};