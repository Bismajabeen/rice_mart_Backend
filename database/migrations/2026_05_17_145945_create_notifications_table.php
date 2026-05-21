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
    Schema::create('notifications', function (Blueprint $table) {

        $table->id();

        // WHICH USER RECEIVES NOTIFICATION
        $table->foreignId('user_id')
            ->constrained()
            ->onDelete('cascade');

        // NOTIFICATION TITLE
        $table->string('title');

        // NOTIFICATION MESSAGE
        $table->text('message');

        // TYPE:
        // order / shop / system
        $table->string('type');

        // READ OR NOT
        $table->boolean('is_read')
            ->default(false);

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
