<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Named "notifications_app" (not "notifications") so it never collides
        // with Laravel's own built-in notifications table if that gets added later.
        Schema::create('notifications_app', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('type');        // order_placed, order_status, payment_status, shop_status, shop_pending, chat_message, review, low_stock, payment_release
            $table->string('title');
            $table->text('body')->nullable();
            $table->json('data')->nullable(); // e.g. {"order_id": 12, "conversation_id": 3}

            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications_app');
    }
};
