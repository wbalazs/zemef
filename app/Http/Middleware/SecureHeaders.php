<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Build CSP from config
        $cspDirectives = [];
        foreach (config('secure-headers.csp', []) as $directive => $sources) {
            if (is_array($sources)) {
                $cspDirectives[] = $directive . ' ' . implode(' ', $sources);
            } else {
                $cspDirectives[] = $directive . ' ' . $sources;
            }
        }
         $cspHeader = implode('; ', $cspDirectives);
        \Log::debug('CSP Header Being Set:', ['csp' => $cspHeader]);
        error_log("CSP Header: " . $cspHeader);

        // Set CSP header
        $response->headers->set('Content-Security-Policy', implode('; ', $cspDirectives));
        
        // Set other security headers
        foreach (config('secure-headers.headers', []) as $header => $value) {
            $response->headers->set($header, $value);
        }
        
        return $response;
    }
}