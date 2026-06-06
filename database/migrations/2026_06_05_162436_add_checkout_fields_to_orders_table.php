<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // CUSTOMER INFO
            $table->string('customer_name')->after('user_id');

            $table->string('phone')->after('customer_name');

            $table->string('city')->after('phone');

            $table->text('address')->after('city');

            // PAYMENT METHOD
            $table->string('payment_method')
                  ->default('cod')
                  ->after('address');

            // PAYMENT SCREENSHOT
            $table->string('payment_proof')
                  ->nullable()
                  ->after('payment_method');

            // OPTIONAL NOTES
            $table->text('notes')
                  ->nullable()
                  ->after('payment_proof');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->dropColumn([
                'customer_name',
                'phone',
                'city',
                'address',
                'payment_method',
                'payment_proof',
                'notes',
            ]);
        });
    }
};