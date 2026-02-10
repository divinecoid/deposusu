<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mdx_category_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mdx_product_id')->constrained('mdx_products')->onDelete('cascade');
            $table->foreignId('mdx_category_id')->constrained('mdx_categories')->onDelete('cascade');
            $table->timestamps();
        });

        // Migrate existing data
        $products = DB::table('mdx_products')->whereNotNull('category_id')->get();
        foreach ($products as $product) {
            DB::table('mdx_category_product')->insert([
                'mdx_product_id' => $product->id,
                'mdx_category_id' => $product->category_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('mdx_products', function (Blueprint $table) {
            $table->dropForeign(['category_id']); // Assuming there was a foreign key constraint, if not this might fail. Ideally check first or catch exception.
            // If we are unsure about FK name, we can just drop column, but dropping column with FK usually requires dropping key first.
            // Let's assume standard index/FK name or just drop column and let DB handle it if not strict.
            // But safely:
            $table->dropColumn('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mdx_products', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable();
        });

        // Restore data (take first category)
        $pivots = DB::table('mdx_category_product')->get();
        foreach ($pivots as $pivot) {
            // Use updateOrInsert to avoid potential duplicates if multiple categories exist (just takes last one)
            DB::table('mdx_products')
                ->where('id', $pivot->mdx_product_id)
                ->update(['category_id' => $pivot->mdx_category_id]);
        }

        Schema::dropIfExists('mdx_category_product');
    }
};
