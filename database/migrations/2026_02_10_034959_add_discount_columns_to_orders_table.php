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
            $table->decimal('total_discount', 15, 2)->default(0)->after('total_amount');
        });

        Schema::table('trx_order_items', function (Blueprint $table) {
            $table->decimal('original_price', 15, 2)->nullable()->after('product_id');
            $table->decimal('discount_amount', 15, 2)->default(0)->after('price');
            $table->foreignId('discount_id')->nullable()->after('discount_amount')->constrained('mdx_product_discounts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trx_order_items', function (Blueprint $table) {
            $table->dropForeign(['discount_id']);
            $table->dropColumn(['original_price', 'discount_amount', 'discount_id']);
        });

        Schema::table('trx_orders', function (Blueprint $table) {
            $table->dropColumn('total_discount');
        });
    }
};
