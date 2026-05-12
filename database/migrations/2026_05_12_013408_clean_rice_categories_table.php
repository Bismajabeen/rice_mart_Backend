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
    Schema::table('rice_categories', function (Blueprint $table) {

        // REMOVE FOREIGN KEY FIRST
        $table->dropForeign([
            'shop_id'
        ]);

        // REMOVE OLD COLUMNS
        $table->dropColumn([
            'shop_id',
            'price',
            'stock',
        ]);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rice_categories', function (Blueprint $table) {

            // RESTORE IF ROLLBACK
            $table->unsignedBigInteger('shop_id')
                  ->nullable();

            $table->decimal('price', 10, 2)
                  ->nullable();

            $table->integer('stock')
                  ->nullable();
        });
    }
};