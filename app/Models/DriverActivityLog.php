<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverActivityLog extends Model
{
    use HasFactory;

    // Only created_at is needed
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'activity',
        'description',
        'latitude',
        'longitude',
        'created_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
