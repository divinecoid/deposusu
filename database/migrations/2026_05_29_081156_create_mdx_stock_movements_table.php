<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mdx_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('mdx_products')->onDelete('cascade');
            $table->foreignId('warehouse_id')->nullable()->constrained('mdx_warehouses')->onDelete('set null');
            $table->foreignId('to_warehouse_id')->nullable()->constrained('mdx_warehouses')->onDelete('set null');
            $table->enum('type', ['IN', 'OUT', 'TRANSFER', 'OPNAME', 'RETURN']);
            $table->integer('quantity');
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->string('reference')->nullable(); // PO number, order ID, note
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mdx_stock_movements');
    }
};
