<?php

namespace App\Providers;

use Cloudinary\Cloudinary;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
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
        // Admin login
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)
                ->by(strtolower($request->input('email')).'|'.$request->ip());
        });

        // Organization registration
        RateLimiter::for('register', function (Request $request) {
            return Limit::perHour(3)
                ->by($request->ip());
        });

        // Password reset
        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perMinute(3)
                ->by(strtolower($request->input('email')).'|'.$request->ip());
        });

        // Voter login
        RateLimiter::for('voter-login', function (Request $request) {
            return Limit::perMinute(5)
                ->by(
                    strtolower($request->input('email', $request->input('identifier', '')))
                    .'|'.$request->ip()
                );
        });

        // OTP verification
        RateLimiter::for('otp', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->ip());
        });

        // OTP resend
        RateLimiter::for('resend-otp', function (Request $request) {
            return Limit::perMinute(2)
                ->by($request->ip());
        });

        // Voting (prevent rapid duplicate submissions)
        RateLimiter::for('vote', function (Request $request) {
            return Limit::perMinute(2)->by(
                Auth::guard('voter')->id() ?? $request->ip()
            );
        });
    }
}