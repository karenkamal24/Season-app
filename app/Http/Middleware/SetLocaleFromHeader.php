<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocaleFromHeader
{
    public function handle(Request $request, Closure $next)
    {
        $headerLang = $request->header('Accept-Language');

        if ($headerLang) {
            // Extract language code (handle formats like 'en', 'en-US', 'ar-SA', etc.)
            $lang = strtolower(explode('-', $headerLang)[0]);

            if (in_array($lang, ['ar', 'en'])) {
                app()->setLocale($lang);
            }
        }

        return $next($request);
    }
}
