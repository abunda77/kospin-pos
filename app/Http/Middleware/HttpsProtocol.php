<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;

class HttpsProtocol
{
    public function handle($request, Closure $next)
    {
        if (!$request->secure() && App::environment('production')) {
            $request->setTrustedProxies([$request->getClientIp()], Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO);
            return redirect()->secure($request->getRequestUri());
        }
        return $next($request);
    }
}
