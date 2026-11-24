<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale', config('app.locale'));
        app()->setLocale(in_array($locale, ['ro', 'en'], true) ? $locale : 'ro');

        return $next($request);
    }
}
