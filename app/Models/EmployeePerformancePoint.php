<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePerformancePoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'order_id', 'activity_type', 'points', 'description', 'activity_date',
    ];

    protected $casts = [
        'activity_date' => 'datetime',
        'points' => 'integer',
    ];

    public const TYPE_PACKING  = 'packing_completed';
    public const TYPE_DELIVERY = 'delivery_completed';

    public const ACTIVITY_LABELS = [
        self::TYPE_PACKING  => 'Packing Selesai',
        self::TYPE_DELIVERY => 'Delivery Selesai',
    ];

    public function getActivityLabelAttribute(): string
    {
        return self::ACTIVITY_LABELS[$this->activity_type] ?? $this->activity_type;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(TrxOrder::class, 'order_id');
    }

    /**
     * Award packing point to a preparist
     */
    public static function awardPacking(int $userId, int $orderId, string $description = null): self
    {
        return self::create([
            'user_id'       => $userId,
            'order_id'      => $orderId,
            'activity_type' => self::TYPE_PACKING,
            'points'        => 1,
            'description'   => $description ?? 'Packing order selesai',
            'activity_date' => now(),
        ]);
    }

    /**
     * Award delivery point to a driver
     */
    public static function awardDelivery(int $userId, int $orderId, string $description = null): self
    {
        return self::create([
            'user_id'       => $userId,
            'order_id'      => $orderId,
            'activity_type' => self::TYPE_DELIVERY,
            'points'        => 1,
            'description'   => $description ?? 'Delivery order selesai',
            'activity_date' => now(),
        ]);
    }
}
