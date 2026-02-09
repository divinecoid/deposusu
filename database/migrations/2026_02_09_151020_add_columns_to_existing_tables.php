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
        // Add Role to Users
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('customer')->after('email'); // admin, driver, cashier, customer, warehouse_staff
        });

        // Add Columns to Products
        Schema::table('mdx_products', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('id');
            $table->string('sku')->nullable()->unique()->after('name');
            $table->integer('low_stock_threshold')->default(10)->after('stock');

            // Foreign key constraint if category exists (it will due to previous migration)
            $table->foreign('category_id')->references('id')->on('mdx_categories')->nullOnDelete();
        });

        // Add Columns to Orders
        Schema::table('trx_orders', function (Blueprint $table) {
            $table->foreignId('driver_id')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->foreignId('warehouse_id')->nullable()->after('driver_id')->constrained('mdx_warehouses')->nullOnDelete();
            // Status is likely already there but we ensure we use our defined statuses in code
            $table->string('payment_status')->default('UNPAID')->after('total_amount'); // UNPAID, PAID, CANCELLED
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trx_orders', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn(['driver_id', 'warehouse_id', 'payment_status']);
        });

        Schema::table('mdx_products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'sku', 'low_stock_threshold']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
