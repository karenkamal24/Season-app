<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocaleFromHeader
{
    public function handle(Request $request, Closure $next)
    {
        $lang = $request->header('Accept-Language');

        if (in_array($lang, ['ar', 'en'])) {
            app()->setLocale($lang);
        }

        return $next($request);
    }
}
