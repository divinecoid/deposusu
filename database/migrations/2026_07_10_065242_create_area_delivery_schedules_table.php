<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('area_delivery_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('mdx_areas')->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->tinyInteger('day_of_week'); // 1=Senin, 2=Selasa, ..., 7=Minggu
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['area_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('area_delivery_schedules');
    }
};
