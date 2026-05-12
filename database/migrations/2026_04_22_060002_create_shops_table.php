<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')->constrained()->onDelete('cascade');

        // CNIC Info
        $table->string('cnic');
        $table->string('cnic_image');

        // Shop Info
        $table->string('shop_name');
        $table->string('owner_name');
        $table->string('phone');
        $table->string('address');
        $table->text('description')->nullable();

        // Admin approval
        $table->boolean('is_approved')->default(false);

        $table->timestamps();
        
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
