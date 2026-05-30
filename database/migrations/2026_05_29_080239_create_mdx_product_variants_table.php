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
        Schema::create('mdx_product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mdx_product_id')->constrained('mdx_products')->onDelete('cascade');
            $table->string('sku')->unique()->nullable();
            $table->string('name');
            $table->decimal('price', 15, 2);
            $table->integer('stock')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mdx_product_variants');
    }
};
