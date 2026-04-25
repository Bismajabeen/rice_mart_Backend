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
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('cnic_number')->unique();
            $table->string('cnic_image')->nullable();
            $table->string('shop_name');
            $table->string('owner_name');
            $table->string('phone');
            $table->string('address');
            $table->text('description')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();

            // Index for faster queries
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
