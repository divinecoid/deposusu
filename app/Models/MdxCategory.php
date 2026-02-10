<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdxCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'icon'];

    public function products()
    {
        return $this->belongsToMany(MdxProduct::class, 'mdx_category_product', 'mdx_category_id', 'mdx_product_id');
    }
}
