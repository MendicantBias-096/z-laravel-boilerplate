<?php

namespace App\Http\Middleware;

use App\Enums\Language;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $locale = session('locale') ?? $request->user()->profile?->locale ?? config('app.locale');
            session(['locale' => $locale]);
        } else {
            $cookie = $request->cookie('locale');
            $locale = $cookie && in_array($cookie, Language::values(), true)
                ? $cookie
                : config('app.locale');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
