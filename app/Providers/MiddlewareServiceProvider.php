<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;
use App\Http\Middleware\LogRequestResponseTime;

class MiddlewareServiceProvider extends ServiceProvider
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
        
        // Dynamically check configuration to register middleware

        if (config('middleware.enable_custom_middleware', false)) {
            $kernel = $this->app->make(Kernel::class);
            $kernel->pushMiddleware(LogRequestResponseTime::class);
        }
    }
}
