<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MdxProduct;
use App\Models\MdxProductBatch;
use App\Models\MdxWarehouse;
use App\Models\MdxWarehouseStock;
use App\Models\MdxStockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    /**
     * Stok per gudang
     */
    public function stock(Request $request)
    {
        $warehouses = MdxWarehouse::all();
        $selectedWarehouse = $request->warehouse_id
            ? MdxWarehouse::find($request->warehouse_id)
            : $warehouses->first();

        $stocks = collect();
        $batchesByProduct = collect();
        if ($selectedWarehouse) {
            $stocks = MdxWarehouseStock::with('product')
                ->where('warehouse_id', $selectedWarehouse->id)
                ->get();

            $batchesByProduct = MdxProductBatch::where('warehouse_id', $selectedWarehouse->id)
                ->where('quantity', '>', 0)
                ->orderBy('expiry_date')
                ->get()
                ->groupBy('product_id');
        }

        return view('admin.warehouse.stock', compact('warehouses', 'selectedWarehouse', 'stocks', 'batchesByProduct'));
    }

    /**
     * Riwayat pergerakan stok
     */
    public function movements(Request $request)
    {
        $query = MdxStockMovement::with(['product', 'warehouse', 'toWarehouse', 'user'])
            ->latest();

        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->warehouse_id) {
            $query->where(function ($q) use ($request) {
                $q->where('warehouse_id', $request->warehouse_id)
                  ->orWhere('to_warehouse_id', $request->warehouse_id);
            });
        }

        $movements = $query->paginate(25);
        $warehouses = MdxWarehouse::all();

        return view('admin.warehouse.movements', compact('movements', 'warehouses'));
    }

    /**
     * Form barang masuk
     */
    public function receiveForm()
    {
        $warehouses = MdxWarehouse::all();
        $products = MdxProduct::orderBy('name')->get();
        return view('admin.warehouse.receive', compact('warehouses', 'products'));
    }

    /**
     * Proses barang masuk
     */
    public function receiveStore(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:mdx_warehouses,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:mdx_products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.expiry_date' => 'required|date',
            'items.*.rack_location' => 'nullable|string',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->items as $item) {
                $warehouseStock = MdxWarehouseStock::firstOrCreate(
                    [
                        'warehouse_id' => $request->warehouse_id,
                        'product_id' => $item['product_id'],
                    ],
                    ['quantity' => 0, 'min_stock' => 5]
                );

                $stockBefore = $warehouseStock->quantity;
                $warehouseStock->quantity += $item['quantity'];
                if (!empty($item['rack_location'])) {
                    $warehouseStock->rack_location = $item['rack_location'];
                }
                $warehouseStock->save();

                // Update master product stock too
                $product = MdxProduct::find($item['product_id']);
                $product->stock += $item['quantity'];
                $product->save();

                // Track this receipt as its own batch, keyed by expiry date —
                // receiving the same product+expiry again just adds to that
                // batch instead of creating a duplicate row.
                $batch = MdxProductBatch::firstOrCreate(
                    [
                        'warehouse_id' => $request->warehouse_id,
                        'product_id' => $item['product_id'],
                        'expiry_date' => $item['expiry_date'],
                    ],
                    ['quantity' => 0, 'initial_quantity' => 0]
                );
                $batch->quantity += $item['quantity'];
                $batch->initial_quantity += $item['quantity'];
                $batch->received_at = now();
                if (!empty($item['rack_location'])) {
                    $batch->rack_location = $item['rack_location'];
                }
                $batch->reference = $request->reference;
                $batch->save();

                MdxStockMovement::create([
                    'product_id' => $item['product_id'],
                    'warehouse_id' => $request->warehouse_id,
                    'type' => 'IN',
                    'quantity' => $item['quantity'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $warehouseStock->quantity,
                    'reference' => $request->reference,
                    'notes' => trim(($request->notes ?? '') . " [Exp: {$item['expiry_date']}]"),
                    'user_id' => Auth::id(),
                ]);
            }
        });

        return redirect()->route('admin.warehouse.movements')
            ->with('success', 'Barang masuk berhasil dicatat.');
    }

    /**
     * Form transfer antar gudang
     */
    public function transferForm()
    {
        $warehouses = MdxWarehouse::all();
        $products = MdxProduct::orderBy('name')->get();
        return view('admin.warehouse.transfer', compact('warehouses', 'products'));
    }

    /**
     * Proses transfer
     */
    public function transferStore(Request $request)
    {
        $request->validate([
            'from_warehouse_id' => 'required|exists:mdx_warehouses,id',
            'to_warehouse_id' => 'required|exists:mdx_warehouses,id|different:from_warehouse_id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:mdx_products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->items as $item) {
                // Subtract from source
                $fromStock = MdxWarehouseStock::where('warehouse_id', $request->from_warehouse_id)
                    ->where('product_id', $item['product_id'])
                    ->firstOrFail();

                if ($fromStock->quantity < $item['quantity']) {
                    throw new \Exception("Stok tidak cukup untuk produk ID {$item['product_id']}");
                }

                $fromBefore = $fromStock->quantity;
                $fromStock->quantity -= $item['quantity'];
                $fromStock->save();

                // Add to destination
                $toStock = MdxWarehouseStock::firstOrCreate(
                    [
                        'warehouse_id' => $request->to_warehouse_id,
                        'product_id' => $item['product_id'],
                    ],
                    ['quantity' => 0, 'min_stock' => 5]
                );

                $toBefore = $toStock->quantity;
                $toStock->quantity += $item['quantity'];
                $toStock->save();

                MdxStockMovement::create([
                    'product_id' => $item['product_id'],
                    'warehouse_id' => $request->from_warehouse_id,
                    'to_warehouse_id' => $request->to_warehouse_id,
                    'type' => 'TRANSFER',
                    'quantity' => $item['quantity'],
                    'stock_before' => $fromBefore,
                    'stock_after' => $fromStock->quantity,
                    'reference' => 'Transfer',
                    'notes' => $request->notes,
                    'user_id' => Auth::id(),
                ]);
            }
        });

        return redirect()->route('admin.warehouse.movements')
            ->with('success', 'Transfer stok berhasil.');
    }

    /**
     * Batch/expiry tracking — every batch with remaining quantity > 0,
     * grouped by how urgent it is (already expired / within 30 days / safe).
     */
    public function expired(Request $request)
    {
        $query = MdxProductBatch::with(['product', 'warehouse'])->where('quantity', '>', 0);

        if ($request->warehouse_id) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $batches = $query->orderBy('expiry_date')->get();

        $expired = $batches->filter(fn ($b) => $b->expiryStatus() === 'expired');
        $nearExpiry = $batches->filter(fn ($b) => $b->expiryStatus() === 'near_expiry');
        $safe = $batches->filter(fn ($b) => $b->expiryStatus() === 'safe');

        $warehouses = MdxWarehouse::all();

        return view('admin.warehouse.expired', compact('expired', 'nearExpiry', 'safe', 'warehouses'));
    }
}
