<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trx_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('mdx_products')->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->json('days_of_week'); // Indonesian day names, e.g. ["Senin", "Rabu"]
            $table->text('shipping_address');
            $table->string('payment_method'); // 'COD' or a Xendit channel code
            $table->boolean('is_active')->default(true);
            $table->date('last_generated_date')->nullable();
            $table->timestamps();

            $table->unique(['customer_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trx_subscriptions');
    }
};
