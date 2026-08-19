<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('commission_amount', 10, 2)->nullable()->after('price');
            $table->decimal('net_amount', 10, 2)->nullable()->after('commission_amount');
            $table->timestamp('customer_confirmed_at')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['commission_amount', 'net_amount', 'customer_confirmed_at']);
        });
    }
};