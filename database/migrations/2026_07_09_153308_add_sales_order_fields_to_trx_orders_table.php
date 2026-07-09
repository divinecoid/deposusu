<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trx_orders', function (Blueprint $table) {
            // Link to registered customer (nullable for walk-in/guest)
            $table->unsignedBigInteger('customer_id')->nullable()->after('customer_name');

            // Stored contact info (for walk-in / WhatsApp customers without an account)
            $table->string('customer_phone', 20)->nullable()->after('customer_id');
            $table->text('customer_address')->nullable()->after('customer_phone');

            // Delivery scheduling
            $table->date('delivery_date')->nullable()->after('customer_address');
            $table->string('delivery_slot', 20)->nullable()->after('delivery_date'); // pagi, siang, sore

            // Payment method for manual order
            $table->string('payment_method', 30)->nullable()->after('delivery_slot'); // transfer, cash, cod, wallet, piutang

            // Order notes from admin / customer via WhatsApp
            $table->text('notes')->nullable()->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('trx_orders', function (Blueprint $table) {
            $table->dropColumn([
                'customer_id',
                'customer_phone',
                'customer_address',
                'delivery_date',
                'delivery_slot',
                'payment_method',
                'notes',
            ]);
        });
    }
};
