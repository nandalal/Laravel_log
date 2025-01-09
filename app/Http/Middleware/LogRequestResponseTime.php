<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogRequestResponseTime
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        // Record the start time
        $startTime = microtime(true);

        // Proceed with the request
        $response = $next($request);

        // Record the end time
        $endTime = microtime(true);

        // Calculate the duration
        $duration = $endTime - $startTime;

        // Log the request and response time
        Log::info('Request Information', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'duration' => number_format($duration, 2) . ' seconds',
        ]);

        return $response;
    }
}
