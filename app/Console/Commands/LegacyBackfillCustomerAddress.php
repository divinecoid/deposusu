<?php

namespace App\Console\Commands;

use App\Models\TrxOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * One-off repair: legacy:migrate-transaksi ran before customer addresses
 * were being decrypted/stored, so every legacy_migration order got the
 * "(alamat historis tidak tercatat)" placeholder instead of the real
 * (now-decrypted) address. Run this after legacy:build-customer-map has
 * backfilled mdx_customers.address to fix orders already created.
 */
class LegacyBackfillCustomerAddress extends Command
{
    protected $signature = 'legacy:backfill-customer-address';

    protected $description = 'Backfill trx_orders.customer_address for legacy_migration orders from the now-decrypted mdx_customers.address';

    public function handle(): int
    {
        $updated = DB::table('trx_orders as o')
            ->join('mdx_customers as mc', 'mc.user_id', '=', 'o.customer_id')
            ->where('o.source', 'legacy_migration')
            ->whereNotNull('mc.address')
            ->where('mc.address', '!=', '')
            ->where(function ($q) {
                $q->whereNull('o.customer_address')
                    ->orWhere('o.customer_address', '(alamat historis tidak tercatat)');
            })
            ->update(['o.customer_address' => DB::raw('mc.address')]);

        $this->info("Updated customer_address on {$updated} legacy order(s).");

        return self::SUCCESS;
    }
}
