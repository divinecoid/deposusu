<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrxCart extends Model
{
    use HasFactory;

    protected $table = 'trx_carts';

    protected $fillable = [
        'user_id',
        'session_id',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(TrxCartItem::class, 'cart_id');
    }

    /**
     * Get total number of items in cart
     */
    public function getTotalItems()
    {
        return $this->items()->sum('quantity');
    }

    /**
     * Get total price of all items
     */
    public function getTotalPrice()
    {
        return $this->items()->get()->sum(function ($item) {
            return $item->quantity * $item->price;
        });
    }

    /**
     * Get subtotal before discount
     */
    public function getSubtotalBeforeDiscount()
    {
        return $this->items()->get()->sum(function ($item) {
            // Get original price from product
            $originalPrice = $item->product->price ?? $item->price;
            return $item->quantity * $originalPrice;
        });
    }

    /**
     * Get total discount amount
     */
    public function getTotalDiscount()
    {
        return $this->getSubtotalBeforeDiscount() - $this->getTotalPrice();
    }


    /**
     * Add item to cart or update quantity if exists
     */
    public function addItem($productId, $quantity = 1)
    {
        $product = MdxProduct::findOrFail($productId);

        // Check if item already exists in cart
        $cartItem = $this->items()->where('product_id', $productId)->first();

        if ($cartItem) {
            // Update quantity
            $cartItem->quantity += $quantity;
            $cartItem->price = $product->discounted_price; // Refresh price to latest discounted price
            $cartItem->save();
        } else {
            // Create new cart item
            $cartItem = $this->items()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $product->discounted_price,
            ]);
        }

        return $cartItem;
    }

    /**
     * Update item quantity
     */
    public function updateItemQuantity($cartItemId, $quantity)
    {
        $cartItem = $this->items()->findOrFail($cartItemId);

        if ($quantity <= 0) {
            $cartItem->delete();
            return null;
        }

        $cartItem->quantity = $quantity;
        $cartItem->save();

        return $cartItem;
    }

    /**
     * Remove item from cart
     */
    public function removeItem($cartItemId)
    {
        $cartItem = $this->items()->findOrFail($cartItemId);
        $cartItem->delete();

        return true;
    }

    /**
     * Clear all items from cart
     */
    public function clearCart()
    {
        $this->items()->delete();
        return true;
    }
}
