<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasCookie('locale')) {
            $locale = $request->cookie('locale');
            if (in_array($locale, ['en', 'nl'])) {
                app()->setLocale($locale);
            }
        }

        return $next($request);
    }
}
