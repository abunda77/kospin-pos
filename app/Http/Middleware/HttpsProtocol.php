<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;

class HttpsProtocol
{
    public function handle($request, Closure $next)
    {
        if (App::environment('production')) {
            URL::forceScheme('https');

            if (!$request->secure()) {
                $request->setTrustedProxies(
                    [$request->getClientIp()],
                    Request::HEADER_X_FORWARDED_AWS_ELB
                );
                return redirect()->secure($request->getRequestUri());
            }
        }

        return $next($request);
    }
}
