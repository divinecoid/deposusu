<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_performance_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained('trx_orders')->onDelete('set null');
            $table->string('activity_type', 30); // 'packing_completed', 'delivery_completed'
            $table->integer('points')->default(1);
            $table->text('description')->nullable();
            $table->timestamp('activity_date');
            $table->timestamps();

            $table->index(['user_id', 'activity_type']);
            $table->index('activity_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_performance_points');
    }
};
