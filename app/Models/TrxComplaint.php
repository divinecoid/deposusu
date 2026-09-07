<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrxComplaint extends Model
{
    const CATEGORIES = [
        'produk_rusak' => 'Produk Rusak/Cacat',
        'pesanan_tidak_sesuai' => 'Pesanan Tidak Sesuai',
        'keterlambatan' => 'Keterlambatan Pengiriman',
        'lainnya' => 'Lainnya',
    ];

    protected $fillable = [
        'customer_id',
        'order_id',
        'category',
        'description',
        'status',
        'resolution_note',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function order()
    {
        return $this->belongsTo(TrxOrder::class, 'order_id');
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }
}
