<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('shops', function (Blueprint $table) {
            $table->string('cnic_number')->nullable()->after('address');
            $table->string('cnic_image')->nullable()->after('cnic_number');
            $table->string('phone')->nullable()->after('cnic_image');
            $table->string('owner_name')->nullable()->after('phone');
        });
    }

    public function down(): void {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn(['cnic_number', 'cnic_image', 'phone', 'owner_name']);
        });
    }
};