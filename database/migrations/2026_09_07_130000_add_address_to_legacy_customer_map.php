<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('legacy_customer_map', function (Blueprint $table) {
            $table->text('legacy_address_decrypted')->nullable()->after('legacy_phone_decrypted');
        });
    }

    public function down(): void
    {
        Schema::table('legacy_customer_map', function (Blueprint $table) {
            $table->dropColumn('legacy_address_decrypted');
        });
    }
};
