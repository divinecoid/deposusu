<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrxInvoice;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $deliveryStatus = $request->query('delivery_status', 'all');
        $customer = trim((string) $request->query('customer', ''));
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $query = TrxInvoice::with(['order']); // Eager load order

        if ($status !== 'all') {
            $query->where('status', strtoupper($status));
        }

        if ($deliveryStatus !== 'all') {
            if ($deliveryStatus === 'shipped') {
                $query->whereHas('order', function ($q) {
                    $q->whereIn('status', ['delivered', 'partialdelivered', 'done']);
                });
            } elseif ($deliveryStatus === 'pending') {
                $query->whereHas('order', function ($q) {
                    $q->whereNotIn('status', ['delivered', 'partialdelivered', 'done']);
                });
            }
        }

        if ($customer !== '') {
            $query->whereHas('order', function ($q) use ($customer) {
                $q->where('customer_name', 'like', "%{$customer}%");
            });
        }

        if ($dateFrom) {
            $query->whereDate('issue_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('issue_date', '<=', $dateTo);
        }

        $invoices = $query->latest()->paginate(20)->withQueryString();

        // Calculate counts dynamically preserving delivery_status/customer/date filters
        $countQuery = TrxInvoice::query();
        if ($deliveryStatus !== 'all') {
            if ($deliveryStatus === 'shipped') {
                $countQuery->whereHas('order', function ($q) {
                    $q->whereIn('status', ['delivered', 'partialdelivered', 'done']);
                });
            } elseif ($deliveryStatus === 'pending') {
                $countQuery->whereHas('order', function ($q) {
                    $q->whereNotIn('status', ['delivered', 'partialdelivered', 'done']);
                });
            }
        }

        if ($customer !== '') {
            $countQuery->whereHas('order', function ($q) use ($customer) {
                $q->where('customer_name', 'like', "%{$customer}%");
            });
        }

        if ($dateFrom) {
            $countQuery->whereDate('issue_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $countQuery->whereDate('issue_date', '<=', $dateTo);
        }

        $counts = [
            'all' => (clone $countQuery)->count(),
            'unpaid' => (clone $countQuery)->where('status', 'UNPAID')->count(),
            'paid' => (clone $countQuery)->where('status', 'PAID')->count(),
            'cancelled' => (clone $countQuery)->where('status', 'CANCELLED')->count(),
        ];

        return view('admin.invoices.index', compact('invoices', 'status', 'counts', 'deliveryStatus', 'customer', 'dateFrom', 'dateTo'));
    }

    public function show(TrxInvoice $invoice)
    {
        $invoice->load(['order.items.product', 'payments']);
        return view('admin.invoices.show', compact('invoice'));
    }

    public function downloadPdf(TrxInvoice $invoice)
    {
        $invoice->load(['order.items.product']);
        $pdf = Pdf::loadView('admin.invoices.pdf', compact('invoice'));
        return $pdf->download("Invoice-{$invoice->invoice_number}.pdf");
    }

    public function updateStatus(Request $request, TrxInvoice $invoice)
    {
        $request->validate([
            'status' => 'required|in:UNPAID,PAID,CANCELLED',
        ]);

        $invoice->update(['status' => $request->status]);

        return back()->with('success', 'Invoice status updated successfully.');
    }
}

