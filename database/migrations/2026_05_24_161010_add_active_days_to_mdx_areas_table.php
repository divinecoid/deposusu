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
        Schema::table('mdx_areas', function (Blueprint $table) {
            $table->boolean('is_monday')->default(true)->after('description');
            $table->boolean('is_tuesday')->default(true)->after('is_monday');
            $table->boolean('is_wednesday')->default(true)->after('is_tuesday');
            $table->boolean('is_thursday')->default(true)->after('is_wednesday');
            $table->boolean('is_friday')->default(true)->after('is_thursday');
            $table->boolean('is_saturday')->default(true)->after('is_friday');
            $table->boolean('is_sunday')->default(true)->after('is_saturday');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mdx_areas', function (Blueprint $table) {
            $table->dropColumn([
                'is_monday',
                'is_tuesday',
                'is_wednesday',
                'is_thursday',
                'is_friday',
                'is_saturday',
                'is_sunday'
            ]);
        });
    }
};
