<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplaceStore extends Model
{
    protected $fillable = [
        'platform_name',
        'store_name',
        'api_key',
        'api_secret',
        'access_token',
        'status',
        'last_sync',
    ];

    protected $casts = [
        'last_sync' => 'datetime',
    ];

    public function productMaps()
    {
        return $this->hasMany(MarketplaceProductMap::class);
    }
}
