<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('order_id')
                  ->unique()
                  ->constrained()
                  ->cascadeOnDelete();

            $table->string('payment_method');

            $table->string('payment_type');

            $table->decimal('amount', 10, 2);

            $table->string('transaction_id')->nullable();

            $table->string('screenshot_path')->nullable();

            $table->string('status')->default('pending');

            $table->foreignId('verified_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamp('verified_at')->nullable();

            $table->text('rejection_reason')->nullable();

            $table->json('gateway_response')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('payment_method');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};