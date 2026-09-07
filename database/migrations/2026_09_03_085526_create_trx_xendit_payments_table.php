<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trx_xendit_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('trx_orders')->cascadeOnDelete();

            // Xendit Payment Requests API identifiers
            $table->string('channel_code'); // QRIS, OVO, DANA, SHOPEEPAY, LINKAJA, *_VIRTUAL_ACCOUNT
            $table->string('reference_id')->unique(); // what we send Xendit as reference_id
            $table->string('xendit_payment_request_id')->nullable()->index();

            // Money: gross is the order's goods total, fee is the convenience fee
            // charged to the customer for this channel, total is what Xendit
            // actually collects (gross + fee).
            $table->decimal('gross_amount', 15, 2);
            $table->decimal('convenience_fee', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);

            $table->string('status')->default('PENDING'); // PENDING, SUCCEEDED, FAILED, EXPIRED, CANCELED

            // Channel-specific payment instructions returned by Xendit
            $table->text('qr_string')->nullable();
            $table->string('virtual_account_number')->nullable();
            $table->string('virtual_account_bank')->nullable();
            $table->text('checkout_url')->nullable();

            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->json('webhook_payload')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trx_xendit_payments');
    }
};
