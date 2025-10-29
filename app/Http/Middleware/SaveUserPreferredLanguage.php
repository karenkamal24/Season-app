<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SaveUserPreferredLanguage
{
    /**
     * Handle an incoming request.
     * 
     * Automatically saves user's preferred language from Accept-Language header
     * if it's different from the currently saved language.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only for authenticated users
        if ($request->user()) {
            // Get language from Accept-Language header (ar or en)
            $headerLang = $request->header('Accept-Language', 'ar');
            
            // Normalize to 'ar' or 'en' only
            $normalizedLang = (strtolower($headerLang) === 'en') ? 'en' : 'ar';
            
            // Get user's current saved language
            $currentLang = $request->user()->preferred_language;
            
            // Update only if different (avoid unnecessary DB queries)
            if ($currentLang !== $normalizedLang) {
                $request->user()->update([
                    'preferred_language' => $normalizedLang
                ]);
            }
        }
        
        return $next($request);
    }
}
