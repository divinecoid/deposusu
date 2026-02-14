<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    protected $fillable = [
        'type',
        'title',
        'subtitle',
        'image_path',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    // Scope for ordering
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    // Scope for active slides
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
