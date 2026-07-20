<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->text('correction_reason')->nullable()->after('status');
            $table->timestamp('correction_requested_at')->nullable()->after('correction_reason');
        });
    }

    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn(['correction_reason', 'correction_requested_at']);
        });
    }
};