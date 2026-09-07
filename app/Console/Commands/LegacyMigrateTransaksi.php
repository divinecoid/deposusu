<?php

namespace App\Console\Commands;

use App\Enums\OrderStatusEnum;
use App\Models\TrxOrder;
use App\Models\TrxOrderItem;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Groups the legacy `transaksi` table (one row per line item, no invoice
 * concept at all) into synthetic invoices, keyed by
 * (idcust, dateorder, delivered, dibayar, paymentmethod) — see the
 * conversation this was planned in for the reasoning behind that key.
 *
 * Run legacy:build-customer-map and legacy:build-product-map first.
 */
class LegacyMigrateTransaksi extends Command
{
    protected $signature = 'legacy:migrate-transaksi {--dry-run} {--batch=}';

    protected $description = 'Group legacy transaksi rows into synthetic invoices and migrate them';

    private const STATUS_MAP = [
        '1-1' => [OrderStatusEnum::DONE, 'PAID'],
        '1-0' => [OrderStatusEnum::DELIVERED, 'UNPAID'],
        '0-0' => [OrderStatusEnum::PENDING, 'UNPAID'],
        '0-1' => [OrderStatusEnum::PENDING, 'PAID'], // unusual combo, flagged in notes
    ];

    /** @var array<string,int> per-date sequence counter, lazily seeded from existing order numbers so reruns never collide. */
    private array $sequenceCounters = [];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $batch = $this->option('batch') ?: ($dryRun ? 'dry-run-' . now()->format('YmdHis') : 'final');

        $this->info($dryRun ? "DRY RUN — batch: {$batch}" : "LIVE RUN — batch: {$batch}");

        $customerMap = DB::table('legacy_customer_map')->pluck('customer_id', 'legacy_idcust');
        $productMap = DB::table('legacy_product_map')->pluck('product_id', 'legacy_idbarang');

        $rows = DB::connection('legacy')->table('transaksi')
            ->orderBy('idcust')->orderBy('dateorder')->orderBy('delivered')->orderBy('dibayar')->orderBy('paymentmethod')
            ->cursor();

        $currentKey = null;
        $group = [];
        $stats = ['invoices' => 0, 'items' => 0, 'skipped_invalid_date' => 0, 'skipped_no_customer' => 0, 'skipped_no_items' => 0, 'skipped_line_no_product' => 0, 'amount_paid' => 0, 'amount_unpaid' => 0];

        foreach ($rows as $row) {
            $key = "{$row->idcust}|{$row->dateorder}|{$row->delivered}|{$row->dibayar}|{$row->paymentmethod}";

            if ($currentKey !== null && $key !== $currentKey) {
                $this->processGroup($group, $customerMap, $productMap, $batch, $dryRun, $stats);
                $group = [];
            }

            $currentKey = $key;
            $group[] = $row;
        }

        if (!empty($group)) {
            $this->processGroup($group, $customerMap, $productMap, $batch, $dryRun, $stats);
        }

        $this->newLine();
        $this->info("Invoices created: {$stats['invoices']}");
        $this->info("Items migrated: {$stats['items']}");
        $this->info("Groups skipped (invalid dateorder): {$stats['skipped_invalid_date']}");
        $this->info("Groups skipped (customer not mapped): {$stats['skipped_no_customer']}");
        $this->info("Groups skipped (zero mappable items): {$stats['skipped_no_items']}");
        $this->info("Lines skipped (product not mapped): {$stats['skipped_line_no_product']}");
        $this->info('Total PAID amount: Rp ' . number_format($stats['amount_paid'], 0, ',', '.'));
        $this->info('Total UNPAID amount: Rp ' . number_format($stats['amount_unpaid'], 0, ',', '.'));

        return self::SUCCESS;
    }

    private function processGroup(array $rows, $customerMap, $productMap, string $batch, bool $dryRun, array &$stats): void
    {
        $first = $rows[0];

        // strtotime() is too lenient for legacy junk like '0200-00-00' (it
        // happily returns *some* timestamp instead of failing) — validate
        // strictly instead: a real calendar date within a sane year range.
        $dateParts = DateTime::createFromFormat('Y-m-d', (string) $first->dateorder);
        $orderDateTimestamp = ($dateParts && $dateParts->format('Y-m-d') === $first->dateorder && $dateParts->format('Y') >= 2015 && $dateParts->format('Y') <= 2030)
            ? $dateParts->getTimestamp()
            : false;

        if (!$orderDateTimestamp) {
            $stats['skipped_invalid_date']++;
            $this->logGroup($rows, $batch, null, 0, 'dateorder tidak valid (0000-00-00 atau kosong) — grup dilewati');
            return;
        }

        $customerId = $customerMap[$first->idcust] ?? null;

        if (!$customerId) {
            $stats['skipped_no_customer']++;
            $this->logGroup($rows, $batch, null, 0, 'Customer tidak termapping (idcust: ' . $first->idcust . ')');
            return;
        }

        $validRows = [];
        foreach ($rows as $row) {
            if (isset($productMap[$row->idbarang]) && $productMap[$row->idbarang]) {
                $validRows[] = $row;
            } else {
                $stats['skipped_line_no_product']++;
            }
        }

        if (empty($validRows)) {
            $stats['skipped_no_items']++;
            $this->logGroup($rows, $batch, null, 0, 'Semua item dalam grup ini produknya tidak termapping');
            return;
        }

        $totalAmount = array_sum(array_map(fn ($r) => $r->harga * $r->jumlah, $validRows));
        [$status, $paymentStatus] = self::STATUS_MAP["{$first->delivered}-{$first->dibayar}"];
        $notes = ($first->delivered == 0 && $first->dibayar == 1) ? 'Kombinasi tidak biasa: sudah dibayar tapi belum pernah diantar — perlu ditinjau manual.' : null;

        if ($dryRun) {
            $stats['invoices']++;
            $stats['items'] += count($validRows);
            $stats[$paymentStatus === 'PAID' ? 'amount_paid' : 'amount_unpaid'] += $totalAmount;
            $this->logGroup($rows, $batch, null, $totalAmount, $notes);
            return;
        }

        // Idempotency: skip if this exact group was already migrated in a prior run.
        $alreadyMigrated = DB::table('legacy_migration_log')
            ->where('batch', 'final')
            ->where('legacy_idcust', $first->idcust)
            ->where('legacy_dateorder', $first->dateorder)
            ->where('legacy_delivered', $first->delivered)
            ->where('legacy_dibayar', $first->dibayar)
            ->where('legacy_paymentmethod', $first->paymentmethod)
            ->whereNotNull('order_id')
            ->exists();

        if ($alreadyMigrated) {
            return;
        }

        $dateKey = date('Ymd', $orderDateTimestamp);
        $orderNumber = 'LEGACY-' . $dateKey . '-' . str_pad($this->nextSequenceFor($dateKey), 5, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($validRows, $customerId, $status, $paymentStatus, $first, $productMap, $totalAmount, &$stats, $batch, $notes, $rows, $orderNumber) {

            $customer = \App\Models\User::find($customerId);

            $order = TrxOrder::create([
                'order_number' => $orderNumber,
                'customer_id' => $customerId,
                'customer_name' => $customer->name,
                'customer_address' => optional($customer->customerProfile)->address ?? '(alamat historis tidak tercatat)',
                'payment_method' => $first->paymentmethod ?: 'unknown',
                'total_amount' => $totalAmount,
                'total_discount' => 0,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'source' => 'legacy_migration',
            ]);

            // created_at isn't mass-assignable, so it's silently dropped by
            // create() above (Eloquent then auto-stamps "now" on save) —
            // force the real historical date in directly, bypassing timestamps().
            $order->timestamps = false;
            $order->created_at = $first->dateorder;
            $order->updated_at = $first->dateorder;
            $order->save();

            foreach ($validRows as $row) {
                TrxOrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productMap[$row->idbarang],
                    'original_price' => $row->harga,
                    'quantity' => $row->jumlah,
                    'price' => $row->harga,
                    'discount_amount' => 0,
                    'subtotal' => $row->harga * $row->jumlah,
                ]);
            }

            $order->invoice()->create([
                'invoice_number' => $orderNumber,
                'issue_date' => $first->dateorder,
                'due_date' => $first->dateorder,
                'status' => $paymentStatus,
                'total_amount' => $totalAmount,
            ]);

            $stats['invoices']++;
            $stats['items'] += count($validRows);
            $stats[$paymentStatus === 'PAID' ? 'amount_paid' : 'amount_unpaid'] += $totalAmount;

            $this->logGroup($rows, $batch, $order->id, $totalAmount, $notes);
        });
    }

    private function nextSequenceFor(string $dateKey): int
    {
        if (!isset($this->sequenceCounters[$dateKey])) {
            $lastNumber = TrxOrder::where('order_number', 'like', "LEGACY-{$dateKey}-%")
                ->orderByDesc('order_number')
                ->value('order_number');

            $this->sequenceCounters[$dateKey] = $lastNumber ? (int) substr($lastNumber, -5) : 0;
        }

        return ++$this->sequenceCounters[$dateKey];
    }

    private function logGroup(array $rows, string $batch, ?int $orderId, float $totalAmount, ?string $notes): void
    {
        $first = $rows[0];

        DB::table('legacy_migration_log')->insert([
            'batch' => $batch,
            'legacy_idcust' => $first->idcust,
            'legacy_dateorder' => $first->dateorder,
            'legacy_delivered' => $first->delivered,
            'legacy_dibayar' => $first->dibayar,
            'legacy_paymentmethod' => $first->paymentmethod,
            'item_count' => count($rows),
            'total_amount' => $totalAmount,
            'order_id' => $orderId,
            'legacy_transaksi_ids' => implode(',', array_map(fn ($r) => $r->idTrans, $rows)),
            'notes' => $notes,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
