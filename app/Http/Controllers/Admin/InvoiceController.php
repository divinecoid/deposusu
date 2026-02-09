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

        $query = TrxInvoice::with(['order.customerProfile']); // Eager load order and customer

        if ($status !== 'all') {
            $query->where('status', strtoupper($status));
        }

        $invoices = $query->latest()->paginate(20);

        $counts = [
            'all' => TrxInvoice::count(),
            'unpaid' => TrxInvoice::where('status', 'UNPAID')->count(),
            'paid' => TrxInvoice::where('status', 'PAID')->count(),
            'cancelled' => TrxInvoice::where('status', 'CANCELLED')->count(),
        ];

        return view('admin.invoices.index', compact('invoices', 'status', 'counts'));
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
