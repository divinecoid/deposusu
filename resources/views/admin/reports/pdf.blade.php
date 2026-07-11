<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Deposusu - {{ ucfirst($tab) }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header-title {
            font-size: 20px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0;
        }
        .header-meta {
            font-size: 10px;
            color: #666666;
            margin-top: 5px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e3a8a;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }
        .kpi-container {
            width: 100%;
            margin-bottom: 20px;
        }
        .kpi-card {
            width: 30%;
            float: left;
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px;
            margin-right: 3%;
        }
        .kpi-title {
            font-size: 9px;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: bold;
        }
        .kpi-value {
            font-size: 16px;
            font-weight: bold;
            color: #111827;
            margin-top: 5px;
        }
        .clear {
            clear: both;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f9fafb;
            font-weight: bold;
            color: #374151;
            font-size: 9px;
            text-transform: uppercase;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .font-bold {
            font-weight: bold;
        }
        .text-green {
            color: #10b981;
        }
        .text-red {
            color: #ef4444;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-title">DEPOSUSU - LAPORAN {{ strtoupper($tab) }}</div>
        <div class="header-meta">
            Periode: <strong>{{ $startDate }}</strong> s/d <strong>{{ $endDate }}</strong> | 
            Gudang/Lokasi: <strong>{{ $warehouseName }}</strong> | 
            Dicetak pada: {{ now()->format('d M Y H:i') }}
        </div>
    </div>

    {{-- ─── FINANCE TAB ─── --}}
    @if($tab === 'finance')
        <div class="kpi-container">
            <div class="kpi-card">
                <div class="kpi-title">Total Pendapatan</div>
                <div class="kpi-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-title">Total Pengeluaran</div>
                <div class="kpi-value">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-title">Estimasi Net Profit</div>
                <div class="kpi-value {{ $netProfit >= 0 ? 'text-green' : 'text-red' }}">Rp {{ number_format($netProfit, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="clear"></div>

        <div class="section-title">Rincian Finansial</div>
        <table>
            <thead>
                <tr>
                    <th>Komponen Keuangan</th>
                    <th class="text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Total Pendapatan (Omzet Penjualan)</td>
                    <td class="text-right font-bold">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Harga Pokok Penjualan (HPP / Modal Barang)</td>
                    <td class="text-right text-red">- Rp {{ number_format($totalModal, 0, ',', '.') }}</td>
                </tr>
                <tr style="background-color: #f9fafb;">
                    <td class="font-bold">Gross Profit (Profit Kotor)</td>
                    <td class="text-right font-bold text-green">Rp {{ number_format($grossProfit, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Biaya Operasional (Pengeluaran)</td>
                    <td class="text-right text-red">- Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                </tr>
                <tr style="background-color: #f3f4f6;">
                    <td class="font-bold">Net Profit (Profit Bersih)</td>
                    <td class="text-right font-bold {{ $netProfit >= 0 ? 'text-green' : 'text-red' }}">Rp {{ number_format($netProfit, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="section-title">Piutang Customer (Accounts Receivable) - Total: Rp {{ number_format($totalPiutang, 0, ',', '.') }}</div>
        <table>
            <thead>
                <tr>
                    <th>Invoice / Order</th>
                    <th>Nama Customer</th>
                    <th>Metode Pembayaran</th>
                    <th class="text-right">Total Piutang</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($piutangList as $order)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->customer_name }}</td>
                    <td>{{ ucfirst($order->payment_method) }}</td>
                    <td class="text-right">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                    <td class="text-center text-red font-bold">{{ $order->payment_status }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada piutang customer aktif</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="section-title">Hutang Supplier (Accounts Payable) - Total: Rp {{ number_format($totalHutang, 0, ',', '.') }}</div>
        <table>
            <thead>
                <tr>
                    <th>PO Number</th>
                    <th>Supplier</th>
                    <th class="text-right">Total Tagihan</th>
                    <th class="text-right">Telah Dibayar</th>
                    <th class="text-right">Sisa Hutang</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hutangList as $po)
                <tr>
                    <td>{{ $po->po_number }}</td>
                    <td>{{ $po->supplier->name }}</td>
                    <td class="text-right">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($po->paid_amount, 0, ',', '.') }}</td>
                    <td class="text-right text-red font-bold">Rp {{ number_format($po->total_amount - $po->paid_amount, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada hutang supplier aktif</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    {{-- ─── SALES TAB ─── --}}
    @if($tab === 'sales')
        <div class="kpi-container">
            <div class="kpi-card">
                <div class="kpi-title">Total Transaksi</div>
                <div class="kpi-value">{{ $totalSalesCount }} orders</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-title">Total Omzet Penjualan</div>
                <div class="kpi-value">Rp {{ number_format($totalSalesAmount, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="clear"></div>

        <div class="section-title">Penjualan per Channel</div>
        <table>
            <thead>
                <tr>
                    <th>Channel Penjualan</th>
                    <th class="text-center">Jumlah Transaksi</th>
                    <th class="text-right">Total Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($salesByChannel as $src)
                <tr>
                    <td class="font-bold">{{ $src->source === 'admin' ? 'WhatsApp Order & POS (Manual/Toko)' : ($src->source === 'kasir' ? 'POS Toko Fisik' : 'Customer App') }}</td>
                    <td class="text-center">{{ $src->count }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($src->total, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center">Belum ada data channel penjualan</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="section-title">Produk Terlaris (Fast-Moving)</div>
        <table>
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th class="text-center">Jumlah Terjual</th>
                    <th class="text-right">Subtotal Omzet</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topProducts as $tp)
                <tr>
                    <td>{{ $tp->name }}</td>
                    <td class="text-center">{{ number_format($tp->qty_sold, 0) }} pcs</td>
                    <td class="text-right font-bold">Rp {{ number_format($tp->total_rev, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center">Belum ada data produk terjual</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    {{-- ─── STOCK TAB ─── --}}
    @if($tab === 'stock')
        <div class="section-title">Stok Barang per Gudang</div>
        <table>
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Gudang / Lokasi</th>
                    <th>Rak</th>
                    <th class="text-right">Stok Aktual</th>
                    <th class="text-right">Batas Minimum</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stocks as $stock)
                <tr>
                    <td class="font-bold">{{ $stock->product->name ?? 'Unknown' }}</td>
                    <td>{{ $stock->warehouse->name ?? 'Unknown' }}</td>
                    <td>{{ $stock->rack_location ?: '-' }}</td>
                    <td class="text-right font-bold {{ $stock->quantity <= $stock->min_stock ? 'text-red' : '' }}">{{ $stock->quantity }} pcs</td>
                    <td class="text-right">{{ $stock->min_stock }} pcs</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data stok per gudang</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="section-title">Pergerakan Stok Terbaru (Stock Movements)</div>
        <table>
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Produk</th>
                    <th>Asal</th>
                    <th>Tujuan</th>
                    <th>Tipe</th>
                    <th class="text-right">Qty</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $m)
                <tr>
                    <td>{{ $m->created_at->format('d/m/Y H:i') }}</td>
                    <td class="font-bold">{{ $m->product->name ?? 'Unknown' }}</td>
                    <td>{{ $m->warehouse->name ?? '-' }}</td>
                    <td>{{ $m->toWarehouse->name ?? '-' }}</td>
                    <td class="text-center font-bold">{{ $m->type }}</td>
                    <td class="text-right font-bold">{{ $m->quantity }} pcs</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada riwayat pergerakan stok dalam periode ini</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    {{-- ─── CUSTOMER TAB ─── --}}
    @if($tab === 'customer')
        <div class="kpi-container">
            <div class="kpi-card">
                <div class="kpi-title">Total Customer</div>
                <div class="kpi-value">{{ $totalCustomersCount }} orang</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-title">Customer Baru (Periode Ini)</div>
                <div class="kpi-value">{{ $newCustomersCount }} orang</div>
            </div>
        </div>
        <div class="clear"></div>

        <div class="section-title">Top Customer (Pembelanjaan Terbesar)</div>
        <table>
            <thead>
                <tr>
                    <th>Nama Customer</th>
                    <th>No. WhatsApp</th>
                    <th class="text-center">Total Transaksi</th>
                    <th class="text-right">Total Belanja</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topCustomers as $tc)
                <tr>
                    <td class="font-bold">{{ $tc->customer_name }}</td>
                    <td>{{ $tc->customer_phone ?: '-' }}</td>
                    <td class="text-center">{{ $tc->total_orders }} kali</td>
                    <td class="text-right font-bold text-green">Rp {{ number_format($tc->total_spend, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">Belum ada data belanja customer</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    {{-- ─── OPERATIONAL TAB ─── --}}
    @if($tab === 'operational')
        <div class="kpi-container">
            <div class="kpi-card">
                <div class="kpi-title">Total Order</div>
                <div class="kpi-value">{{ $totalOrders }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-title">Order Selesai</div>
                <div class="kpi-value">{{ $completedOrders }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-title">Sedang Diproses</div>
                <div class="kpi-value">{{ $processingOrders }}</div>
            </div>
        </div>
        <div class="clear"></div>

        <div class="section-title">Performa Pengiriman Kurir</div>
        <table>
            <thead>
                <tr>
                    <th>Nama Kurir</th>
                    <th class="text-center">Total Pengantaran</th>
                    <th class="text-center">Pengantaran Selesai</th>
                    <th class="text-center">Persentase Sukses</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deliveries as $d)
                <tr>
                    <td class="font-bold">{{ $d->driver->name ?? 'Unknown' }}</td>
                    <td class="text-center">{{ $d->total_deliveries }}</td>
                    <td class="text-center">{{ $d->completed_deliveries }}</td>
                    <td class="text-center font-bold text-green">
                        {{ $d->total_deliveries > 0 ? round(($d->completed_deliveries / $d->total_deliveries) * 100) : 0 }}%
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data performa kurir pengiriman</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    @endif

</body>
</html>
