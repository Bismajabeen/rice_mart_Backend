<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
   {
     Schema::create('shop_reviews', function (Blueprint $table) {

         $table->id();

         $table->foreignId('customer_id')
            ->constrained('users')
            ->cascadeOnDelete();

         $table->foreignId('order_item_id')
            ->constrained('order_items')
            ->cascadeOnDelete();

         $table->foreignId('shop_id')
            ->constrained('shops')
            ->cascadeOnDelete();

         $table->integer('rating');

         $table->text('review')
            ->nullable();

         $table->timestamps();


         $table->unique([
            'customer_id',
            'order_item_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_reviews');
    }
};
