<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rice_categories', function (Blueprint $table) {
            // Only add if not already present (safe to re-run)
            if (!Schema::hasColumn('rice_categories', 'stock_kg')) {
                $table->decimal('stock_kg', 10, 2)->default(0)->after('price_per_kg');
            }
            if (!Schema::hasColumn('rice_categories', 'image')) {
                $table->string('image')->nullable()->after('stock_kg');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rice_categories', function (Blueprint $table) {
            $table->dropColumn(['stock_kg', 'image']);
        });
    }
};
