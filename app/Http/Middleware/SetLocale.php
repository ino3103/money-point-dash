<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        // Set locale from session if available, otherwise use default
        $locale = session('locale', config('app.locale'));
        
        if (in_array($locale, ['en', 'sw'])) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
