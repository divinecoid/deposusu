<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdxDriver extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'license_plate', 'vehicle_type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        // Actually orders are linked to the user_id (driver), but maybe through this profile?
        // The migration linked orders.driver_id to users.id.
        // So this relation is via the user.
        return $this->hasManyThrough(TrxOrder::class, User::class, 'id', 'driver_id', 'user_id', 'id');
    }
}
