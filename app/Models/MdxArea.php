<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdxArea extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'description', 'branch_id', 'latitude', 'longitude', 'is_active',
        'is_monday', 'is_tuesday', 'is_wednesday', 'is_thursday', 'is_friday', 'is_saturday', 'is_sunday'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_monday' => 'boolean',
        'is_tuesday' => 'boolean',
        'is_wednesday' => 'boolean',
        'is_thursday' => 'boolean',
        'is_friday' => 'boolean',
        'is_saturday' => 'boolean',
        'is_sunday' => 'boolean',
    ];

    public function getActiveDaysAttribute()
    {
        $days = [];
        if ($this->is_monday) $days[] = 'Senin';
        if ($this->is_tuesday) $days[] = 'Selasa';
        if ($this->is_wednesday) $days[] = 'Rabu';
        if ($this->is_thursday) $days[] = 'Kamis';
        if ($this->is_friday) $days[] = 'Jumat';
        if ($this->is_saturday) $days[] = 'Sabtu';
        if ($this->is_sunday) $days[] = 'Minggu';
        return $days;
    }

    public function branch()
    {
        return $this->belongsTo(MdxBranch::class, 'branch_id');
    }

    public function customers()
    {
        return $this->hasMany(MdxCustomer::class, 'area_id');
    }

    public function deliverySchedules()
    {
        return $this->hasMany(AreaDeliverySchedule::class, 'area_id');
    }
}
