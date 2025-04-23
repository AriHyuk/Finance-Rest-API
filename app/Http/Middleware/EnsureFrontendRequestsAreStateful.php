<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;

class EnsureFrontendRequestsAreStateful
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
        // Pastikan frontend menggunakan stateful session dengan Sanctum
        // Ensure Sanctum is properly configured for stateful requests
        config(['sanctum.stateful' => config('sanctum.stateful')]);

        return $next($request);
    }
}
