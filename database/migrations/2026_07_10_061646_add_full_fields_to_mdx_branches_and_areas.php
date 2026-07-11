<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add missing fields to mdx_branches
        Schema::table('mdx_branches', function (Blueprint $table) {
            if (!Schema::hasColumn('mdx_branches', 'phone')) {
                $table->string('phone', 30)->nullable()->after('address');
            }
            if (!Schema::hasColumn('mdx_branches', 'pic_name')) {
                $table->string('pic_name', 100)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('mdx_branches', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('pic_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mdx_branches', function (Blueprint $table) {
            $table->dropColumn(['phone', 'pic_name', 'is_active']);
        });
    }
};
