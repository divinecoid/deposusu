<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mdx_warehouse_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('mdx_warehouses')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('mdx_products')->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->integer('min_stock')->default(5);
            $table->string('rack_location')->nullable(); // e.g. "A-01-03"
            $table->timestamps();

            $table->unique(['warehouse_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mdx_warehouse_stocks');
    }
};
