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
        Schema::table('trx_cart_items', function (Blueprint $table) {
            $table->boolean('is_routine')->default(false)->after('price');
            $table->json('routine_schedule')->nullable()->after('is_routine');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trx_cart_items', function (Blueprint $table) {
            $table->dropColumn(['is_routine', 'routine_schedule']);
        });
    }
};
