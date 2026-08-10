<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Nullable + nullOnDelete: if a city is ever deleted, old orders
            // keep their historical 'city' name string and just lose the FK link.
            $table->foreignId('city_id')
                ->nullable()
                ->after('city')
                ->constrained('cities')
                ->nullOnDelete();

            $table->decimal('delivery_charge', 10, 2)
                ->default(0)
                ->after('total_price');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('city_id');
            $table->dropColumn('delivery_charge');
        });
    }
};