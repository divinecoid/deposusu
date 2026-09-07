<?php

namespace App\Console\Commands;

use App\Models\MdxProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LegacyBuildProductMap extends Command
{
    protected $signature = 'legacy:build-product-map {--create-missing : Create a new mdx_products row (stock 0, is_legacy_import=true) for legacy products with no barcode match}';

    protected $description = 'Match legacy tablebarang rows to mdx_products by barcode, recording the result in legacy_product_map';

    public function handle(): int
    {
        $legacyProducts = DB::connection('legacy')->table('tablebarang')
            ->where('idbarang', '!=', '')
            ->get();

        $this->info("Found {$legacyProducts->count()} legacy products to map.");

        $counts = ['barcode' => 0, 'created_new' => 0, 'unmapped' => 0];

        foreach ($legacyProducts as $legacy) {
            $product = MdxProduct::where('barcode', $legacy->idbarang)->first();
            $matchedBy = $product ? 'barcode' : null;

            if (!$product && $this->option('create-missing') && $legacy->namabarang) {
                $product = MdxProduct::create([
                    'name' => $legacy->namabarang,
                    'sku' => $legacy->idbarang,
                    'barcode' => $legacy->idbarang,
                    'price' => max(0, $legacy->harga),
                    'stock' => 0,
                    'low_stock_threshold' => 0,
                    'is_legacy_import' => true,
                ]);
                $matchedBy = 'created_new';
            }

            $matchedBy = $matchedBy ?: 'unmapped';
            $counts[$matchedBy]++;

            DB::table('legacy_product_map')->updateOrInsert(
                ['legacy_idbarang' => $legacy->idbarang],
                [
                    'legacy_name' => $legacy->namabarang,
                    'product_id' => $product?->id,
                    'matched_by' => $matchedBy,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        foreach ($counts as $type => $count) {
            $this->info("{$type}: {$count}");
        }

        return self::SUCCESS;
    }
}
