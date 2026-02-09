<?php

use App\Models\MdxProduct;
use Illuminate\Support\Facades\Route;

Route::get('/debug-images', function () {
    return \App\Models\MdxProduct::all()->map(function ($p) {
        return [
            'id' => $p->id,
            'name' => $p->name,
            'image_db' => $p->image,
            'asset_url' => asset($p->image),
        ];
    });
});
