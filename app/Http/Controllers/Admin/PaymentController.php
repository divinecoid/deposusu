<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrxPayment;
use App\Models\TrxInvoice;
use App\Models\TrxOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $method = $request->query('method', 'all');

        $query = TrxPayment::with(['invoice.order', 'confirmedBy'])->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }
        if ($method !== 'all') {
            $query->where('payment_method', $method);
        }

        $payments = $query->paginate(15);

        $counts = [
            'all'      => TrxPayment::count(),
            'pending'  => TrxPayment::where('status', 'pending')->count(),
            'approved' => TrxPayment::where('status', 'approved')->count(),
            'rejected' => TrxPayment::where('status', 'rejected')->count(),
        ];

        return view('admin.payments.index', compact('payments', 'status', 'method', 'counts'));
    }

    public function show(TrxPayment $payment)
    {
        $payment->load(['invoice.order.items.product', 'confirmedBy']);
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Create a new payment entry for an invoice.
     */
    public function create(Request $request)
    {
        $invoiceId = $request->query('invoice_id');
        $invoice = $invoiceId ? TrxInvoice::with('order')->find($invoiceId) : null;

        $unpaidInvoices = TrxInvoice::where('status', 'UNPAID')
            ->with('order')
            ->latest()
            ->get();

        return view('admin.payments.create', compact('invoice', 'unpaidInvoices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_id'       => 'required|exists:trx_invoices,id',
            'payment_method'   => 'required|in:transfer,cash,qris,wallet,cod,piutang',
            'amount'           => 'required|numeric|min:1',
            'payment_date'     => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'proof_image'      => 'nullable|image|max:2048',
            'notes'            => 'nullable|string|max:500',
        ]);

        $data = $request->only([
            'invoice_id', 'payment_method', 'amount', 'payment_date',
            'reference_number', 'notes',
        ]);

        $invoice = TrxInvoice::find($request->invoice_id);
        $data['order_id'] = $invoice->order_id ?? null;
        $data['status'] = 'pending';

        if ($request->hasFile('proof_image')) {
            $data['proof_image'] = $request->file('proof_image')->store('payments', 'public');
        }

        TrxPayment::create($data);

        return redirect()->route('admin.payments.index')
            ->with('success', 'Pembayaran berhasil dicatat. Menunggu konfirmasi admin.');
    }

    public function approve(TrxPayment $payment)
    {
        $payment->update([
            'status'       => 'approved',
            'confirmed_by' => Auth::id(),
            'confirmed_at' => now(),
        ]);

        // Update invoice status to PAID
        if ($payment->invoice) {
            $payment->invoice->update(['status' => 'PAID']);
        }

        return back()->with('success', 'Pembayaran berhasil di-approve.');
    }

    public function reject(Request $request, TrxPayment $payment)
    {
        $request->validate(['notes' => 'nullable|string|max:500']);

        $payment->update([
            'status'       => 'rejected',
            'confirmed_by' => Auth::id(),
            'confirmed_at' => now(),
            'notes'        => $request->notes ?: $payment->notes,
        ]);

        return back()->with('success', 'Pembayaran ditolak.');
    }

    /**
     * Generate kuitansi PDF for corporate/PT customers.
     */
    public function receipt(TrxPayment $payment)
    {
        $payment->load(['invoice.order.items.product']);

        if (!$payment->receipt_number) {
            $payment->update([
                'receipt_number' => 'KW-' . now()->format('Ymd') . '-' . str_pad($payment->id, 5, '0', STR_PAD_LEFT),
            ]);
            $payment->refresh();
        }

        $pdf = Pdf::loadView('admin.payments.receipt-pdf', compact('payment'));
        return $pdf->download("Kuitansi-{$payment->receipt_number}.pdf");
    }

    /**
     * Form to fill corporate kuitansi details before generating.
     */
    public function receiptForm(TrxPayment $payment)
    {
        $payment->load(['invoice.order']);
        return view('admin.payments.receipt-form', compact('payment'));
    }

    public function receiptStore(Request $request, TrxPayment $payment)
    {
        $request->validate([
            'company_name'    => 'required|string|max:255',
            'payment_purpose' => 'required|string|max:500',
        ]);

        $receiptNumber = 'KW-' . now()->format('Ymd') . '-' . str_pad($payment->id, 5, '0', STR_PAD_LEFT);

        $payment->update([
            'receipt_number'  => $receiptNumber,
            'company_name'    => $request->company_name,
            'payment_purpose' => $request->payment_purpose,
        ]);

        return redirect()->route('admin.payments.receipt', $payment->id);
    }
}
