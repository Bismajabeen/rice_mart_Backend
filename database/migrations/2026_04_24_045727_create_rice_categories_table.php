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
            // ✅ foreignId('shop_id') now correctly references shops.id
            $table->foreignId('shop_id')->constrained('shops')->onDelete('cascade');
            $table->string('name');
            $table->decimal('price_per_kg', 10, 2);
            $table->decimal('stock_kg', 10, 2)->default(0);
            $table->string('image')->nullable();
            $table->timestamps();

            $table->index('shop_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rice_categories');
    }
};
