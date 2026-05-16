<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            // order reference
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade');

            // shop reference
            $table->foreignId('shop_id')
                ->constrained('shops')
                ->onDelete('cascade');

            // product reference (IMPORTANT FIX)
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');

            // quantity & price snapshot
            $table->integer('quantity');
            $table->decimal('price', 10, 2);

            $table->timestamps();

            // performance index
            $table->index(['order_id', 'shop_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};