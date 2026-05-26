<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('trx_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('preparist_id')->nullable()->after('warehouse_id');
            $table->timestamp('on_preparation_at')->nullable()->after('status');
            $table->timestamp('prepared_at')->nullable()->after('on_preparation_at');

            $table->foreign('preparist_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trx_orders', function (Blueprint $table) {
            $table->dropForeign(['preparist_id']);
            $table->dropColumn(['preparist_id', 'on_preparation_at', 'prepared_at']);
        });
    }
};
