<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UpdateUserLastActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Update last_active_at for authenticated users
        if ($request->user()) {
            // Use cache to prevent updating on every request (update once per minute)
            $cacheKey = 'user_last_active_' . $request->user()->id;

            if (!Cache::has($cacheKey)) {
                // Update without firing events for better performance
                DB::table('users')
                    ->where('id', $request->user()->id)
                    ->update(['last_active_at' => now()]);

                // Cache for 60 seconds to prevent too many updates
                Cache::put($cacheKey, true, 60);
            }
        }

        return $next($request);
    }
}
