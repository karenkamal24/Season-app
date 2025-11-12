<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from authenticated user, session, or default
        $locale = Auth::check() 
            ? (Auth::user()->locale ?? config('app.locale'))
            : session('locale', config('app.locale'));
        
        // Validate locale
        if (!in_array($locale, ['ar', 'en'])) {
            $locale = config('app.locale');
        }
        
        // Set the application locale
        app()->setLocale($locale);
        
        return $next($request);
    }
}
