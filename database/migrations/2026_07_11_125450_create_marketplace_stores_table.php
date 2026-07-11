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
        Schema::create('marketplace_stores', function (Blueprint $table) {
            $table->id();
            $table->string('platform_name'); // e.g. shopee, tokopedia, tiktok
            $table->string('store_name');
            $table->string('api_key')->nullable();
            $table->string('api_secret')->nullable();
            $table->text('access_token')->nullable();
            $table->string('status')->default('disconnected'); // connected, disconnected, error
            $table->timestamp('last_sync')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_stores');
    }
};
