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
        Schema::table('trx_invoices', function (Blueprint $table) {
            $table->integer('print_count')->default(0)->after('total_amount');
            $table->timestamp('last_printed_at')->nullable()->after('print_count');
            $table->foreignId('last_printed_by_id')->nullable()->constrained('users')->nullOnDelete()->after('last_printed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trx_invoices', function (Blueprint $table) {
            $table->dropForeign(['last_printed_by_id']);
            $table->dropColumn(['print_count', 'last_printed_at', 'last_printed_by_id']);
        });
    }
};
