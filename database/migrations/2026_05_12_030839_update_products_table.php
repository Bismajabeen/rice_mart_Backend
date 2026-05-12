<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {

            // USER
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');

            // SHOP
            $table->foreignId('shop_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');

            // CATEGORY
            $table->foreignId('rice_category_id')
                ->nullable()
                ->constrained('rice_categories')
                ->onDelete('cascade');

            // PRODUCT NAME
            $table->string('name')->nullable();

            // PRICE
            $table->decimal('price', 10, 2)
                ->nullable();

            // STOCK
            $table->integer('stock')
                ->nullable();

            // IMAGE
            $table->string('image')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {

            $table->dropForeign(['user_id']);
            $table->dropForeign(['shop_id']);
            $table->dropForeign(['rice_category_id']);

            $table->dropColumn([
                'user_id',
                'shop_id',
                'rice_category_id',
                'name',
                'price',
                'stock',
                'image',
            ]);
        });
    }
};