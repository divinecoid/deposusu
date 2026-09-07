<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legacy_customer_map', function (Blueprint $table) {
            $table->id();
            $table->string('legacy_idcust')->unique();
            $table->string('legacy_name')->nullable();
            $table->string('legacy_email_decrypted')->nullable();
            $table->string('legacy_phone_decrypted')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('matched_by')->nullable(); // idcust_as_phone, decrypted_email, decrypted_phone, created_new, unmapped
            $table->timestamps();
        });

        Schema::create('legacy_product_map', function (Blueprint $table) {
            $table->id();
            $table->string('legacy_idbarang')->unique();
            $table->string('legacy_name')->nullable();
            $table->foreignId('product_id')->nullable()->constrained('mdx_products')->nullOnDelete();
            $table->string('matched_by')->nullable(); // barcode, created_new, unmapped
            $table->timestamps();
        });

        Schema::create('legacy_migration_log', function (Blueprint $table) {
            $table->id();
            $table->string('batch'); // e.g. dry-run timestamp or 'final'
            $table->string('legacy_idcust');
            $table->string('legacy_dateorder'); // stored as raw string — source data has invalid dates like '0000-00-00'
            $table->boolean('legacy_delivered');
            $table->boolean('legacy_dibayar');
            $table->string('legacy_paymentmethod');
            $table->unsignedInteger('item_count');
            $table->decimal('total_amount', 15, 2);
            $table->foreignId('order_id')->nullable()->constrained('trx_orders')->nullOnDelete();
            $table->text('legacy_transaksi_ids'); // comma-separated idTrans values, for traceability/audit
            $table->text('notes')->nullable(); // e.g. flagged edge cases
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legacy_migration_log');
        Schema::dropIfExists('legacy_product_map');
        Schema::dropIfExists('legacy_customer_map');
    }
};
