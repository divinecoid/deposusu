<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MdxSupplier;
use App\Models\TrxPurchaseOrder;
use App\Models\TrxPurchaseOrderItem;
use App\Models\MdxProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    // ─── SUPPLIERS CRUD ────────────────────────────────────────

    public function index(Request $request)
    {
        $q = $request->query('q');
        $query = MdxSupplier::withCount('purchaseOrders')->latest();

        if ($q) {
            $query->where(function ($qb) use ($q) {
                $qb->where('name', 'like', "%{$q}%")
                   ->orWhere('phone', 'like', "%{$q}%")
                   ->orWhere('city', 'like', "%{$q}%");
            });
        }

        $suppliers = $query->paginate(15);
        return view('admin.suppliers.index', compact('suppliers', 'q'));
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'code'              => 'nullable|string|max:20|unique:mdx_suppliers,code',
            'contact_person'    => 'nullable|string|max:100',
            'phone'             => 'nullable|string|max:30',
            'email'             => 'nullable|email|max:100',
            'address'           => 'nullable|string|max:500',
            'city'              => 'nullable|string|max:100',
            'bank_name'         => 'nullable|string|max:50',
            'bank_account'      => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:100',
            'credit_limit'      => 'nullable|numeric|min:0',
            'notes'             => 'nullable|string|max:1000',
        ]);

        if (empty($data['code'])) {
            $data['code'] = 'SUP-' . str_pad(MdxSupplier::count() + 1, 4, '0', STR_PAD_LEFT);
        }

        MdxSupplier::create($data);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function show(MdxSupplier $supplier)
    {
        $supplier->load(['purchaseOrders' => function ($q) {
            $q->latest()->take(10);
        }]);
        return view('admin.suppliers.show', compact('supplier'));
    }

    public function edit(MdxSupplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, MdxSupplier $supplier)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'code'              => "nullable|string|max:20|unique:mdx_suppliers,code,{$supplier->id}",
            'contact_person'    => 'nullable|string|max:100',
            'phone'             => 'nullable|string|max:30',
            'email'             => 'nullable|email|max:100',
            'address'           => 'nullable|string|max:500',
            'city'              => 'nullable|string|max:100',
            'bank_name'         => 'nullable|string|max:50',
            'bank_account'      => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:100',
            'credit_limit'      => 'nullable|numeric|min:0',
            'notes'             => 'nullable|string|max:1000',
            'is_active'         => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');
        $supplier->update($data);

        return redirect()->route('admin.suppliers.show', $supplier->id)
            ->with('success', 'Data supplier berhasil diperbarui.');
    }

    public function destroy(MdxSupplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier berhasil dihapus.');
    }

    // ─── PURCHASE ORDERS ───────────────────────────────────────

    public function purchaseOrders(Request $request)
    {
        $status = $request->query('status', 'all');

        $query = TrxPurchaseOrder::with(['supplier', 'items'])->latest();
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->paginate(15);

        $counts = [
            'all'      => TrxPurchaseOrder::count(),
            'draft'    => TrxPurchaseOrder::where('status', 'draft')->count(),
            'ordered'  => TrxPurchaseOrder::where('status', 'ordered')->count(),
            'received' => TrxPurchaseOrder::where('status', 'received')->count(),
        ];

        return view('admin.suppliers.purchase-orders', compact('orders', 'status', 'counts'));
    }

    public function purchaseOrderCreate()
    {
        $suppliers = MdxSupplier::where('is_active', true)->orderBy('name')->get();
        $products  = MdxProduct::orderBy('name')->get();
        return view('admin.suppliers.purchase-order-create', compact('suppliers', 'products'));
    }

    public function purchaseOrderStore(Request $request)
    {
        $request->validate([
            'supplier_id'        => 'required|exists:mdx_suppliers,id',
            'order_date'         => 'required|date',
            'expected_date'      => 'nullable|date|after_or_equal:order_date',
            'payment_due_date'   => 'nullable|date',
            'notes'              => 'nullable|string|max:1000',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:mdx_products,id',
            'items.*.name'       => 'required|string|max:255',
            'items.*.unit'       => 'required|string|max:20',
            'items.*.qty'        => 'required|numeric|min:0.01',
            'items.*.price'      => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $today    = now()->format('Ymd');
            $lastPO   = TrxPurchaseOrder::whereDate('created_at', now()->today())->orderBy('id', 'desc')->first();
            $sequence = $lastPO ? intval(substr($lastPO->po_number, -5)) + 1 : 1;
            $poNumber = 'PO-' . $today . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);

            $po = TrxPurchaseOrder::create([
                'po_number'        => $poNumber,
                'supplier_id'      => $request->supplier_id,
                'created_by'       => Auth::id(),
                'order_date'       => $request->order_date,
                'expected_date'    => $request->expected_date,
                'payment_due_date' => $request->payment_due_date,
                'notes'            => $request->notes,
                'status'           => 'draft',
                'payment_status'   => 'unpaid',
            ]);

            $subtotal = 0;
            foreach ($request->items as $item) {
                $itemSubtotal = floatval($item['qty']) * floatval($item['price']);
                TrxPurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id'        => $item['product_id'] ?: null,
                    'product_name'      => $item['name'],
                    'unit'              => $item['unit'],
                    'quantity'          => $item['qty'],
                    'unit_price'        => $item['price'],
                    'subtotal'          => $itemSubtotal,
                ]);
                $subtotal += $itemSubtotal;
            }

            $po->update([
                'subtotal'     => $subtotal,
                'total_amount' => $subtotal,
            ]);

            DB::commit();

            return redirect()->route('admin.suppliers.purchase-orders')
                ->with('success', "Purchase Order #{$poNumber} berhasil dibuat.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal membuat PO: ' . $e->getMessage());
        }
    }

    public function purchaseOrderShow(TrxPurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.product', 'createdBy']);
        return view('admin.suppliers.purchase-order-show', compact('purchaseOrder'));
    }

    public function purchaseOrderUpdateStatus(Request $request, TrxPurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'status' => 'required|in:draft,ordered,partial_received,received,cancelled',
        ]);

        $purchaseOrder->update(['status' => $request->status]);

        if ($request->status === 'received') {
            $purchaseOrder->update(['received_date' => now()]);
        }

        return back()->with('success', 'Status PO berhasil diupdate.');
    }
}
