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
        Schema::table('trx_orders', function (Blueprint $table) {
            $table->string('source')->default('customer')->after('status'); // customer or admin
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trx_orders', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};
