<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrxInvoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $deliveryStatus = $request->query('delivery_status', 'all');

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

        $invoices = $query->latest()->paginate(20);

        // Calculate counts dynamically preserving delivery_status filter
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

        $counts = [
            'all' => (clone $countQuery)->count(),
            'unpaid' => (clone $countQuery)->where('status', 'UNPAID')->count(),
            'paid' => (clone $countQuery)->where('status', 'PAID')->count(),
            'cancelled' => (clone $countQuery)->where('status', 'CANCELLED')->count(),
        ];

        return view('admin.invoices.index', compact('invoices', 'status', 'counts', 'deliveryStatus'));
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
