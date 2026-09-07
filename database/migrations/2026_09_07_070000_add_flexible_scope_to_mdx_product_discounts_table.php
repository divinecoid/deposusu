<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mdx_product_discounts', function (Blueprint $table) {
            $table->string('scope')->default('PRODUCT')->after('product_id'); // PRODUCT or CATEGORY
            $table->foreignId('category_id')->nullable()->after('scope')->constrained('mdx_categories')->onDelete('cascade');
            $table->unsignedInteger('minimum_quantity')->nullable()->after('discount_value');
            $table->string('label')->nullable()->after('minimum_quantity'); // admin-facing name, e.g. "Promo Kemerdekaan"
        });

        Schema::table('mdx_product_discounts', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('mdx_product_discounts', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['scope', 'category_id', 'minimum_quantity', 'label']);
        });
    }
};
