<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trx_complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('trx_orders')->onDelete('cascade');
            $table->string('category'); // produk_rusak, pesanan_tidak_sesuai, keterlambatan, lainnya
            $table->text('description');
            $table->string('status')->default('open'); // open, in_progress, resolved
            $table->text('resolution_note')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trx_complaints');
    }
};
