<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Jenssegers\Agent\Agent;

class DetectMobileDevice
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika pengguna memiliki preferensi view yang disimpan di session, gunakan itu
        if ($request->session()->has('view_preference')) {
            return $next($request);
        }
        
        // Jika route sudah mengandung '/m/', biarkan saja
        if (strpos($request->path(), 'm/') === 0) {
            return $next($request);
        }
        
        // Detect mobile device
        $agent = new Agent();
        $isMobile = $agent->isMobile() || $agent->isTablet();
          // Jika halaman adalah catalog dan pengguna menggunakan perangkat mobile
        if ($isMobile && ($request->routeIs('catalog') || $request->routeIs('catalog.show'))) {
            // Redirect ke versi mobile dengan mempertahankan parameter
            $targetRoute = $request->routeIs('catalog.show') 
                ? route('catalog.mobile.show', $request->route('category')?->slug ?? $request->route('category'), $request->query()) 
                : route('catalog.mobile', $request->query());
            
            return redirect()->to($targetRoute);
        }
        
        return $next($request);
    }
}
