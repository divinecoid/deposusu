<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdxProductVariant extends Model
{
    protected $fillable = ['mdx_product_id', 'sku', 'name', 'price', 'stock'];

    public function product()
    {
        return $this->belongsTo(MdxProduct::class, 'mdx_product_id');
    }
}
