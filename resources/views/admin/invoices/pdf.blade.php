<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; line-height: 1.5; margin: 0; padding: 20px; }
        .header { display: table; width: 100%; border-bottom: 2px solid #eaeaea; padding-bottom: 20px; margin-bottom: 20px; }
        .logo-section { display: table-cell; width: 50%; vertical-align: top; }
        .logo-section h1 { font-size: 24px; font-weight: bold; color: #1e40af; margin: 0; }
        .logo-section p { font-size: 10px; color: #666; margin: 3px 0 0 0; }
        .invoice-section { display: table-cell; width: 50%; text-align: right; vertical-align: top; }
        .invoice-section h2 { font-size: 18px; color: #1e40af; margin: 0; }
        .invoice-section p { margin: 3px 0 0 0; font-size: 11px; }
        .details-table { width: 100%; margin-bottom: 25px; border-collapse: collapse; }
        .details-table td { padding: 4px 0; vertical-align: top; }
        .details-table .label { font-weight: bold; color: #555; width: 120px; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th { background-color: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left; padding: 8px 10px; font-weight: bold; color: #475569; }
        .items-table td { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .totals-table { width: 40%; float: right; border-collapse: collapse; margin-top: 10px; }
        .totals-table td { padding: 6px 10px; }
        .totals-table tr.grand-total { background-color: #f1f5f9; font-weight: bold; font-size: 13px; color: #1e40af; }
        .footer-note { font-size: 10px; color: #94a3b8; text-align: center; margin-top: 50px; border-top: 1px solid #e2e8f0; padding-top: 10px; clear: both; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-section">
            <h1>DEPOSUSU</h1>
            <p>Distribusi & Grocery — Jakarta</p>
        </div>
        <div class="invoice-section">
            <h2>INVOICE</h2>
            <p><strong>#{{ $invoice->invoice_number }}</strong></p>
            <p>Tanggal: {{ $invoice->issue_date->format('d F Y') }}</p>
        </div>
    </div>

    <table class="details-table">
        <tr>
            <td class="label">Ditagih Ke</td>
            <td>: {{ $invoice->order->customer_name }}</td>
            <td class="label">Status Pengiriman</td>
            <td>: {{ $invoice->order->status->label() }}</td>
        </tr>
        <tr>
            <td class="label">No. Telepon</td>
            <td>: {{ $invoice->order->customer_phone ?: '-' }}</td>
            <td class="label">Tanggal Kirim</td>
            <td>: {{ $invoice->order->delivery_date ? \Carbon\Carbon::parse($invoice->order->delivery_date)->format('d F Y') : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Alamat Kirim</td>
            <td>: {{ $invoice->order->customer_address ?: '-' }}</td>
            <td class="label">Sesi Kirim</td>
            <td>: {{ ucfirst($invoice->order->delivery_slot ?: '-') }}</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th class="text-right">Harga</th>
                <th class="text-center" style="width: 60px;">Qty</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->order->items as $item)
            <tr>
                <td>{{ $item->product->name ?? 'N/A' }}</td>
                <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td>Subtotal:</td>
            <td class="text-right">Rp {{ number_format($invoice->subtotal_amount, 0, ',', '.') }}</td>
        </tr>
        @if($invoice->discount_amount > 0)
        <tr>
            <td>Diskon:</td>
            <td class="text-right" style="color: #ef4444;">- Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="grand-total">
            <td>Total Tagihan:</td>
            <td class="text-right">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="footer-note">
        Terima kasih atas kepercayaan Anda bertransaksi dengan Deposusu.<br>
        Ini adalah dokumen invoice resmi yang dihasilkan secara otomatis oleh sistem.
    </div>
</body>
</html>
