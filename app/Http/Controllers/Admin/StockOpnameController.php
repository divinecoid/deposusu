<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MdxProduct;
use App\Models\StockHistory; // Assuming this model exists
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockOpnameController extends Controller
{
    public function index()
    {
        $products = MdxProduct::all();
        $histories = StockHistory::with(['product', 'user'])->latest()->paginate(20);

        return view('admin.stock.index', compact('products', 'histories'));
    }

    public function adjust(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:mdx_products,id',
            'new_stock' => 'required|integer|min:0',
            'note' => 'nullable|string',
        ]);

        $product = MdxProduct::findOrFail($request->product_id);
        $oldStock = $product->stock;
        $newStock = $request->new_stock;
        $difference = $newStock - $oldStock;

        if ($difference == 0) {
            return redirect()->back()->with('info', 'No changes made to stock.');
        }

        // Update Product Stock
        $product->stock = $newStock;
        $product->save();

        // Log History
        StockHistory::create([
            'product_id' => $product->id,
            'old_stock' => $oldStock,
            'new_stock' => $newStock,
            'difference' => $difference,
            'type' => 'adjustment',
            'reference' => $request->note ?? 'Manual Adjustment',
            'user_id' => Auth::id(), // Ensure user is logged in
        ]);

        return redirect()->back()->with('success', 'Stock updated successfully.');
    }
}
