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
        Schema::table('rice_categories', function (Blueprint $table) {

            // STATUS
            $table->boolean('status')
                  ->default(true)
                  ->after('image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rice_categories', function (Blueprint $table) {

            $table->dropColumn('status');
        });
    }
};