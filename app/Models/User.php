<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Fortify\TwoFactorAuthenticatable;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'photo',
        'fcm_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
    public function customerProfile()
    {
        return $this->hasOne(MdxCustomer::class);
    }

    public function driverProfile()
    {
        return $this->hasOne(MdxDriver::class);
    }

    public function attendances()
    {
        return $this->hasMany(DriverAttendance::class);
    }

    public function locations()
    {
        return $this->hasMany(DriverLocation::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(DriverActivityLog::class);
    }

    // Helpers
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    public function isDriver()
    {
        return $this->role === 'driver';
    }
    public function isPreparist()
    {
        return $this->role === 'preparist';
    }
    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function wishlistProducts()
    {
        return $this->belongsToMany(MdxProduct::class, 'wishlists', 'user_id', 'product_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(TrxSubscription::class, 'customer_id');
    }
}
