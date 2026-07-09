<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kuitansi {{ $payment->receipt_number }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 13px; color: #333; margin: 0; padding: 40px; }
        .header { text-align: center; border-bottom: 3px double #1e40af; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { font-size: 28px; font-weight: bold; color: #1e40af; margin: 0; letter-spacing: 3px; }
        .header p { margin: 5px 0 0; font-size: 11px; color: #666; }
        .title { text-align: center; font-size: 20px; font-weight: bold; letter-spacing: 5px; margin-bottom: 30px; color: #1e40af; }
        .info-table { width: 100%; margin-bottom: 25px; border-collapse: collapse; }
        .info-table td { padding: 6px 10px; vertical-align: top; }
        .info-table .label { font-weight: bold; width: 180px; color: #555; }
        .amount-box { background: #f0f4ff; border: 2px solid #1e40af; border-radius: 8px; padding: 15px 20px; text-align: center; margin: 25px 0; }
        .amount-box .amount { font-size: 24px; font-weight: bold; color: #1e40af; }
        .amount-box .words { font-size: 11px; font-style: italic; color: #666; margin-top: 5px; }
        .footer { margin-top: 60px; display: flex; justify-content: space-between; }
        .signature { text-align: center; width: 45%; float: right; }
        .signature .line { border-top: 1px solid #333; margin-top: 60px; padding-top: 5px; }
        .stamp { text-align: center; width: 45%; float: left; margin-top: 40px; font-size: 11px; color: #999; }
        .note { font-size: 10px; color: #999; text-align: center; margin-top: 40px; border-top: 1px solid #eee; padding-top: 10px; clear: both; }
    </style>
</head>
<body>
    <div class="header">
        <h1>DEPOSUSU</h1>
        <p>Distribusi & Grocery — Jakarta</p>
    </div>

    <div class="title">KUITANSI</div>

    <table class="info-table">
        <tr>
            <td class="label">No. Kuitansi</td>
            <td>: {{ $payment->receipt_number }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td>
            <td>: {{ $payment->payment_date->format('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Sudah Diterima Dari</td>
            <td>: {{ $payment->company_name ?: ($payment->invoice->order->customer_name ?? '-') }}</td>
        </tr>
        @if($payment->company_name)
        <tr>
            <td class="label">Perusahaan</td>
            <td>: {{ $payment->company_name }}</td>
        </tr>
        @endif
        <tr>
            <td class="label">No. Invoice</td>
            <td>: {{ $payment->invoice->invoice_number ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Metode Pembayaran</td>
            <td>: {{ ucfirst($payment->payment_method) }}</td>
        </tr>
        @if($payment->reference_number)
        <tr>
            <td class="label">No. Referensi</td>
            <td>: {{ $payment->reference_number }}</td>
        </tr>
        @endif
        <tr>
            <td class="label">Untuk Pembayaran</td>
            <td>: {{ $payment->payment_purpose ?: 'Pembayaran invoice ' . ($payment->invoice->invoice_number ?? '') }}</td>
        </tr>
    </table>

    <div class="amount-box">
        <div class="amount">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
    </div>

    <div class="footer">
        <div class="stamp">
            Jakarta, {{ $payment->payment_date->format('d F Y') }}<br><br>
            <strong>DEPOSUSU</strong>
        </div>
        <div class="signature">
            <div>Diterima oleh,</div>
            <div class="line">
                <strong>{{ auth()->user()->name ?? 'Admin' }}</strong>
            </div>
        </div>
    </div>

    <div class="note">
        Kuitansi ini sah sebagai bukti pembayaran. Dicetak oleh sistem DEPOSUSU pada {{ now()->format('d/m/Y H:i') }}.
    </div>
</body>
</html>
