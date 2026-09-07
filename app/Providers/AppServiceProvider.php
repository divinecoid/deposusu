<?php

namespace App\Providers;

use App\Models\TrxOrder;
use App\Observers\TrxOrderObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        TrxOrder::observe(TrxOrderObserver::class);
    }
}
