<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale') ?? session('locale', config('app.locale'));

        if (! in_array($locale, ['ar', 'en'], true)) {
            $locale = config('app.locale');
        }

        app()->setLocale($locale);
        session(['locale' => $locale]);
        URL::defaults(['locale' => $locale]);

        // Prevent locale from being injected as first controller argument.
        if ($request->route()) {
            $request->route()->forgetParameter('locale');
        }

        return $next($request);
    }
}
