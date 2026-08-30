<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn(['payout_method', 'payout_account_number', 'payout_account_name']);

            $table->string('payout_easypaisa_number')->nullable()->after('address');
            $table->string('payout_easypaisa_account_name')->nullable()->after('payout_easypaisa_number');
            $table->string('payout_jazzcash_number')->nullable()->after('payout_easypaisa_account_name');
            $table->string('payout_jazzcash_account_name')->nullable()->after('payout_jazzcash_number');
        });
    }

    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn([
                'payout_easypaisa_number', 'payout_easypaisa_account_name',
                'payout_jazzcash_number', 'payout_jazzcash_account_name',
            ]);

            $table->string('payout_method')->nullable();
            $table->string('payout_account_number')->nullable();
            $table->string('payout_account_name')->nullable();
        });
    }
};
