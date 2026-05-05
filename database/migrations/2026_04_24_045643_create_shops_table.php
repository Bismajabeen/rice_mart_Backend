<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('cnic_number')->unique();
            $table->string('cnic_image')->nullable();
            $table->string('shop_name');
            $table->string('owner_name');
            $table->string('phone');
            $table->string('address');
            $table->text('description')->nullable();
            $table->boolean('is_approved')->default(false);

            // ✅ Rice categories stored as JSON — no separate table, no shop_id
            $table->json('rice_categories')->nullable();

            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
