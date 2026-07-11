<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('marketplace_product_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketplace_store_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mdx_product_id')->constrained('mdx_products')->cascadeOnDelete();
            $table->string('marketplace_item_id'); // External ID from API
            $table->string('marketplace_sku')->nullable();
            $table->boolean('sync_price')->default(true);
            $table->boolean('sync_stock')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_product_maps');
    }
};
