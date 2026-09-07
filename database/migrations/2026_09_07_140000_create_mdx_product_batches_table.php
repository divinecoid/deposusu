<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mdx_product_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('mdx_products')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('mdx_warehouses')->onDelete('cascade');
            $table->date('expiry_date');
            $table->integer('quantity')->default(0); // remaining in this batch
            $table->integer('initial_quantity')->default(0); // as originally received, for reporting
            $table->string('rack_location')->nullable();
            $table->string('reference')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();

            $table->unique(['warehouse_id', 'product_id', 'expiry_date'], 'batch_wh_product_expiry_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mdx_product_batches');
    }
};
