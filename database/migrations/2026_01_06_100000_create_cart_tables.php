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
        Schema::create('trx_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('session_id')->nullable()->index();
            $table->timestamps();

            // Index untuk performance
            $table->index(['user_id', 'session_id']);
        });

        Schema::create('trx_cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('trx_carts')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('mdx_products')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2); // Snapshot of price at time of adding
            $table->timestamps();

            // Unique constraint: one product per cart
            $table->unique(['cart_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_cart_items');
        Schema::dropIfExists('trx_carts');
    }
};
