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
            $table->timestamp('picked_up_at')->nullable()->after('prepared_at');
            $table->timestamp('delivered_at')->nullable()->after('picked_up_at');
            $table->string('delivery_proof_photo')->nullable()->after('delivered_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trx_orders', function (Blueprint $table) {
            $table->dropColumn(['picked_up_at', 'delivered_at', 'delivery_proof_photo']);
        });
    }
};
