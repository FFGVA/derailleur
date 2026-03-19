<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        RateLimiter::for('form-submissions', function (Request $request) {
            return Limit::perHour(5)->by($request->ip())->response(function () {
                return response()->json([
                    'ok' => false,
                    'error' => 'Trop de messages. Réessayez plus tard.',
                ], 429);
            });
        });
    }
}
