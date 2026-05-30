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
            $table->dateTime('pickup_time')->nullable()->after('status');
            $table->enum('delivery_type', ['regular', 'instant', 'sameday'])->default('regular')->after('pickup_time');
            $table->string('packing_photo_isi')->nullable()->after('prepared_at');
            $table->string('packing_photo_final')->nullable()->after('packing_photo_isi');
            $table->json('packing_logs')->nullable()->after('packing_photo_final');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trx_orders', function (Blueprint $table) {
            $table->dropColumn([
                'pickup_time',
                'delivery_type',
                'packing_photo_isi',
                'packing_photo_final',
                'packing_logs'
            ]);
        });
    }
};
