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
        Schema::create('marketplace_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketplace_store_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mdx_product_id')->constrained('mdx_products')->cascadeOnDelete();
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->string('status')->default('success'); // success, failed
            $table->string('sync_type')->default('event'); // manual, event, cron
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_sync_logs');
    }
};
