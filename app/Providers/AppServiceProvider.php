<?php

namespace App\Providers;

use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Cloudinary::class, function () {
            return new Cloudinary(
                config('cloudinary.url')
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
}
