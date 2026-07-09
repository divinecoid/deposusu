<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MdxProduct;
use App\Models\MdxCustomer;
use App\Models\FinanceTransaction;
use App\Models\LiveChat;
use App\Models\ChatMessage;
use App\Models\TrxOrder;
use App\Enums\OrderStatusEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinanceAndChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Update Product cost prices (approx 70% of price)
        $products = MdxProduct::all();
        foreach ($products as $product) {
            $product->update([
                'cost_price' => round($product->price * 0.70, -2) // e.g. 25000 * 0.7 = 17500
            ]);
        }

        // 2. Update Customer memberships
        $customers = User::where('role', 'customer')->get();
        $memberships = ['Gold', 'Silver', 'Bronze', 'VIP'];
        foreach ($customers as $index => $customer) {
            $profile = MdxCustomer::where('user_id', $customer->id)->first();
            if ($profile) {
                $membership = $memberships[$index % count($memberships)];
                // Let's make sure 'Budi' is Gold or VIP as requested
                if (str_contains(strtolower($customer->name), 'budi')) {
                    $membership = 'Gold';
                }
                $profile->update([
                    'membership' => $membership
                ]);
            }
        }

        // 3. Clear existing finance and chat tables for fresh seeding
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ChatMessage::truncate();
        LiveChat::truncate();
        FinanceTransaction::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 4. Seed Finance Transactions
        // We will seed data spanning the last 30 days
        $now = now();
        
        // Income from existing orders (completed ones)
        $doneOrders = TrxOrder::whereIn('status', [OrderStatusEnum::DELIVERED, OrderStatusEnum::DONE])->get();
        foreach ($doneOrders as $order) {
            FinanceTransaction::create([
                'type' => 'income',
                'category' => $order->source === 'kasir' ? 'sales_pos' : 'sales_app',
                'amount' => $order->total_amount,
                'reference_id' => $order->order_number,
                'description' => "Penjualan order #{$order->order_number} dari customer {$order->customer_name}",
                'transaction_date' => $order->created_at ?: $now,
            ]);
        }

        // Historical Income
        $historicalIncomes = [
            ['type' => 'income', 'category' => 'sales_app', 'amount' => 1250000.00, 'ref' => '#ORD-HIST-01', 'desc' => 'Penjualan aplikasi harian', 'days_ago' => 5],
            ['type' => 'income', 'category' => 'sales_pos', 'amount' => 450000.00, 'ref' => '#POS-HIST-01', 'desc' => 'Penjualan POS Offline Store', 'days_ago' => 4],
            ['type' => 'income', 'category' => 'sales_app', 'amount' => 1800000.00, 'ref' => '#ORD-HIST-02', 'desc' => 'Penjualan aplikasi harian', 'days_ago' => 3],
            ['type' => 'income', 'category' => 'sales_pos', 'amount' => 600000.00, 'ref' => '#POS-HIST-02', 'desc' => 'Penjualan POS Offline Store', 'days_ago' => 2],
            ['type' => 'income', 'category' => 'sales_app', 'amount' => 2100000.00, 'ref' => '#ORD-HIST-03', 'desc' => 'Penjualan aplikasi harian', 'days_ago' => 1],
        ];

        foreach ($historicalIncomes as $hist) {
            FinanceTransaction::create([
                'type' => $hist['type'],
                'category' => $hist['category'],
                'amount' => $hist['amount'],
                'reference_id' => $hist['ref'],
                'description' => $hist['desc'],
                'transaction_date' => (clone $now)->subDays($hist['days_ago']),
            ]);
        }

        // Operational Expenses
        $expenses = [
            ['amount' => 850000.00, 'category' => 'operational_utilities', 'desc' => 'Bayar Listrik & Wifi Gudang Utama', 'days_ago' => 10],
            ['amount' => 2000000.00, 'category' => 'operational_rent', 'desc' => 'Sewa ruko bulanan (Cabang Depok)', 'days_ago' => 8],
            ['amount' => 350000.00, 'category' => 'operational_marketing', 'desc' => 'Iklan Facebook & Instagram Ads', 'days_ago' => 5],
            ['amount' => 1200000.00, 'category' => 'operational_salary', 'desc' => 'Gaji Staff Helper & Driver Harian', 'days_ago' => 3],
            ['amount' => 150000.00, 'category' => 'operational_others', 'desc' => 'Pembelian ATK & Plastik Packing', 'days_ago' => 2],
        ];

        foreach ($expenses as $exp) {
            FinanceTransaction::create([
                'type' => 'expense',
                'category' => $exp['category'],
                'amount' => $exp['amount'],
                'reference_id' => 'EXP-' . rand(10000, 99999),
                'description' => $exp['desc'],
                'transaction_date' => (clone $now)->subDays($exp['days_ago']),
            ]);
        }

        // Refunds
        FinanceTransaction::create([
            'type' => 'refund',
            'category' => 'refund',
            'amount' => 150000.00,
            'reference_id' => '#ORD-003',
            'description' => 'Refund barang pecah untuk order #ORD-003',
            'transaction_date' => (clone $now)->subDays(1),
        ]);


        // 5. Seed Live Chats & Chat Messages
        // Chat 1: Budi - Unread
        $chat1 = LiveChat::create([
            'customer_name' => 'Budi',
            'customer_email' => 'customer_budi@deposusu.com',
            'status' => 'unread',
            'last_message' => 'Pesanan belum datang',
            'agent_name' => null,
        ]);
        ChatMessage::create(['chat_id' => $chat1->id, 'sender' => 'customer', 'message' => 'Halo admin, saya mau tanya status pengiriman.']);
        ChatMessage::create(['chat_id' => $chat1->id, 'sender' => 'customer', 'message' => 'Pesanan belum datang, padahal statusnya sudah dikirim dari 1 jam lalu.']);

        // Chat 2: Ani - Unread
        $chat2 = LiveChat::create([
            'customer_name' => 'Ani',
            'customer_email' => 'customer_ani@deposusu.com',
            'status' => 'unread',
            'last_message' => 'Mau tambah susu',
            'agent_name' => null,
        ]);
        ChatMessage::create(['chat_id' => $chat2->id, 'sender' => 'customer', 'message' => 'Sore admin, apakah saya masih bisa tambah susu kambing 2 botol lagi ke pesanan saya yang sedang diproses?']);

        // Chat 3: Ahmad - Active
        $chat3 = LiveChat::create([
            'customer_name' => 'Ahmad Pelanggan (DepoSusu App)',
            'customer_email' => 'customer_ahmad@deposusu.com',
            'status' => 'active',
            'last_message' => 'Mohon dikonfirmasi statusnya ya min.',
            'agent_name' => 'Felinika Admin',
        ]);
        ChatMessage::create(['chat_id' => $chat3->id, 'sender' => 'customer', 'message' => 'Siang admin, orderan saya #ORD-001 apa sudah dipacking?']);
        ChatMessage::create(['chat_id' => $chat3->id, 'sender' => 'agent', 'message' => 'Halo Kak Ahmad, mohon ditunggu sebentar ya. Sedang kami konfirmasi ke bagian preparist/packer di gudang.']);
        ChatMessage::create(['chat_id' => $chat3->id, 'sender' => 'customer', 'message' => 'Mohon dikonfirmasi statusnya ya min.']);

        // Chat 4: Siti - History
        $chat4 = LiveChat::create([
            'customer_name' => 'Siti Rahma (Shopee)',
            'customer_email' => 'customer_siti@deposusu.com',
            'status' => 'history',
            'last_message' => 'Terima kasih informasinya.',
            'agent_name' => 'Felinika Admin',
        ]);
        ChatMessage::create(['chat_id' => $chat4->id, 'sender' => 'customer', 'message' => 'Min, voucher free ongkir shopee kok gabisa dipakai ya?']);
        ChatMessage::create(['chat_id' => $chat4->id, 'sender' => 'agent', 'message' => 'Halo Kak Siti, untuk kendala voucher shopee bisa dicoba klaim ulang dulu di halaman depan Shopee lalu checkout ulang ya Kak.']);
        ChatMessage::create(['chat_id' => $chat4->id, 'sender' => 'customer', 'message' => 'Terima kasih informasinya.']);
    }
}
