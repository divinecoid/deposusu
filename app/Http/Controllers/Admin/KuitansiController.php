<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrxPayment;
use Illuminate\Http\Request;

class KuitansiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = TrxPayment::with(['invoice.order.customer', 'confirmedBy'])
            ->where('status', 'approved')
            ->latest();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                  ->orWhereHas('invoice.order.customer', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $kuitansis = $query->paginate(15);

        return view('admin.kuitansi.index', compact('kuitansis', 'search'));
    }
}
