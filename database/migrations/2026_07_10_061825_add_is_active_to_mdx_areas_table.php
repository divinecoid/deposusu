<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mdx_areas', function (Blueprint $table) {
            if (!Schema::hasColumn('mdx_areas', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('branch_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mdx_areas', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
