<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdxProductWholesale extends Model
{
    protected $fillable = ['mdx_product_id', 'min_qty', 'price'];

    public function product()
    {
        return $this->belongsTo(MdxProduct::class, 'mdx_product_id');
    }
}
