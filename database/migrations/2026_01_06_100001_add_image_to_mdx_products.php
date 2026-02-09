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
        Schema::table('mdx_products', function (Blueprint $table) {
            $table->string('image')->nullable()->after('description');
            $table->integer('stock')->default(100)->after('image'); // Default stock untuk testing
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mdx_products', function (Blueprint $table) {
            $table->dropColumn(['image', 'stock']);
        });
    }
};
