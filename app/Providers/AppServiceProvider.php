<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;

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
       /* Activity::creating(function ($activity) {
            if (Auth::check() && Auth::user()?->organization_id) {
                $activity->organization_id = Auth::user()->organization_id;
            }
        }); */
    }
}
