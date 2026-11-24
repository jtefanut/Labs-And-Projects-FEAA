<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePaymentOperator
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(in_array($request->user()?->role?->name, ['payment_operator', 'admin'], true), 403);

        return $next($request);
    }
}
