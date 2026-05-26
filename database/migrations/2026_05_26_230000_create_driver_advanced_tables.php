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
        // 1. driver_locations table for tracking coordinates ping
        Schema::create('driver_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->timestamp('created_at')->nullable();
        });

        // 2. driver_activity_logs table for audit trail
        Schema::create('driver_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('activity'); // e.g. 'check_in', 'check_out', 'pickup_order', 'complete_order'
            $table->text('description');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamp('created_at')->nullable();
        });

        // 3. Add signature, recipient, coordinates to trx_orders table
        Schema::table('trx_orders', function (Blueprint $table) {
            $table->string('recipient_name')->nullable()->after('delivery_proof_photo');
            $table->longText('recipient_signature')->nullable()->after('recipient_name'); // base64 representation of signature drawing
            $table->decimal('delivery_latitude', 10, 8)->nullable()->after('recipient_signature');
            $table->decimal('delivery_longitude', 11, 8)->nullable()->after('delivery_latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trx_orders', function (Blueprint $table) {
            $table->dropColumn([
                'recipient_name',
                'recipient_signature',
                'delivery_latitude',
                'delivery_longitude'
            ]);
        });

        Schema::dropIfExists('driver_activity_logs');
        Schema::dropIfExists('driver_locations');
    }
};
