<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromPreferences
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);

        if (in_array($locale, ['en', 'es'])) {
            App::setLocale($locale);
            // Set cookie for fallback
            cookie('app_locale', $locale, 60 * 24 * 365); // 1 year
        }

        return $next($request);
    }

    /**
     * Resolve the locale based on priority.
     */
    private function resolveLocale(Request $request): string
    {
        // 1. ?lang= query parameter (for testing and links)
        if ($request->has('lang') && in_array($request->query('lang'), ['en', 'es'])) {
            return $request->query('lang');
        }

        // 2. Authenticated user's preference
        if (Auth::check() && Auth::user()->locale) {
            return Auth::user()->locale;
        }

        // 3. Cookie
        if ($request->cookie('app_locale') && in_array($request->cookie('app_locale'), ['en', 'es'])) {
            return $request->cookie('app_locale');
        }

        // 4. Accept-Language header (first supported language)
        $acceptLanguage = $request->getPreferredLanguage(['en', 'es']);
        if ($acceptLanguage) {
            return $acceptLanguage;
        }

        // 5. Fallback to config
        return config('app.locale', 'en');
    }
}
