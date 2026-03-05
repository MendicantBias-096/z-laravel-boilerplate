<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->user()?->profile?->locale
            ?? session('locale')
            ?? config('app.locale');

        App::setLocale($locale);

        return $next($request);
    }
}
