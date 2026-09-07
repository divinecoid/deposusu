<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trx_orders', function (Blueprint $table) {
            // Convenience fee charged to the customer for the chosen Xendit
            // payment channel, kept alongside total_amount (goods only) so
            // invoices/reports can show them separately.
            $table->decimal('convenience_fee', 15, 2)->default(0)->after('total_discount');
        });
    }

    public function down(): void
    {
        Schema::table('trx_orders', function (Blueprint $table) {
            $table->dropColumn('convenience_fee');
        });
    }
};
