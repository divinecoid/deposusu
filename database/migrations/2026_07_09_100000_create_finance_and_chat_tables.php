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
        // Create finance_transactions table
        Schema::create('finance_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // income, expense, refund
            $table->string('category'); // sales_app, sales_pos, refund, operational_rent, operational_utilities, operational_salary, operational_marketing, operational_others
            $table->decimal('amount', 15, 2);
            $table->string('reference_id')->nullable(); // invoice or order number
            $table->text('description')->nullable();
            $table->timestamp('transaction_date')->useCurrent();
            $table->timestamps();
        });

        // Create live_chats table
        Schema::create('live_chats', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('status')->default('unread'); // unread, active, history
            $table->string('last_message')->nullable();
            $table->string('agent_name')->nullable();
            $table->timestamps();
        });

        // Create chat_messages table
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained('live_chats')->onDelete('cascade');
            $table->string('sender'); // customer, agent
            $table->text('message');
            $table->timestamps();
        });

        // Add cost_price to mdx_products
        if (Schema::hasTable('mdx_products')) {
            Schema::table('mdx_products', function (Blueprint $table) {
                if (!Schema::hasColumn('mdx_products', 'cost_price')) {
                    $table->decimal('cost_price', 15, 2)->default(0.00)->after('price');
                }
            });
        }

        // Add membership to mdx_customers
        if (Schema::hasTable('mdx_customers')) {
            Schema::table('mdx_customers', function (Blueprint $table) {
                if (!Schema::hasColumn('mdx_customers', 'membership')) {
                    $table->string('membership')->default('Silver')->after('address'); // Bronze, Silver, Gold, VIP
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('live_chats');
        Schema::dropIfExists('finance_transactions');

        if (Schema::hasTable('mdx_products')) {
            Schema::table('mdx_products', function (Blueprint $table) {
                if (Schema::hasColumn('mdx_products', 'cost_price')) {
                    $table->dropColumn('cost_price');
                }
            });
        }

        if (Schema::hasTable('mdx_customers')) {
            Schema::table('mdx_customers', function (Blueprint $table) {
                if (Schema::hasColumn('mdx_customers', 'membership')) {
                    $table->dropColumn('membership');
                }
            });
        }
    }
};
