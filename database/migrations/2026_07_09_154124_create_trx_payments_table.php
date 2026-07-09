<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trx_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('trx_invoices')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('trx_orders')->nullOnDelete();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('payment_method', 30); // transfer, cash, qris, wallet, cod, piutang
            $table->decimal('amount', 15, 2);
            $table->date('payment_date');
            $table->string('reference_number', 100)->nullable(); // nomor referensi / nomor bukti transfer
            $table->string('proof_image', 500)->nullable();      // path foto bukti transfer
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();

            // Kuitansi fields (untuk corporate/PT)
            $table->string('receipt_number', 50)->nullable();
            $table->string('company_name', 255)->nullable();
            $table->text('payment_purpose')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trx_payments');
    }
};
