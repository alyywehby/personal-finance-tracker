<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale {
    public function handle(Request $request, Closure $next): Response {
        if (auth()->check()) {
            $locale = auth()->user()->locale ?? 'en';
        } else {
            $acceptLanguage = $request->getPreferredLanguage(['en', 'ar']);
            $locale = $request->hasSession()
                ? $request->session()->get('locale', $acceptLanguage ?? 'en')
                : ($acceptLanguage ?? 'en');
        }

        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en';
        }

        App::setLocale($locale);

        return $next($request);
    }
}
