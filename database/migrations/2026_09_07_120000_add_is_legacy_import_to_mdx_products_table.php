<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mdx_products', function (Blueprint $table) {
            $table->boolean('is_legacy_import')->default(false)->after('low_stock_threshold');
        });
    }

    public function down(): void
    {
        Schema::table('mdx_products', function (Blueprint $table) {
            $table->dropColumn('is_legacy_import');
        });
    }
};
