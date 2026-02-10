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
        Schema::create('mdx_product_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('mdx_products')->onDelete('cascade');
            $table->enum('discount_type', ['PERCENTAGE', 'FIXED']);
            $table->decimal('discount_value', 15, 2);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mdx_product_discounts');
    }
};
