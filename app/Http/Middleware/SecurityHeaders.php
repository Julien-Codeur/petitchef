<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Add security headers
        $response->header('X-Content-Type-Options', 'nosniff');
        $response->header('X-Frame-Options', 'DENY');
        $response->header('X-XSS-Protection', '1; mode=block');
        $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Force HTTPS in production
        if ($this->isProduction()) {
            $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }
        
        // Content Security Policy - allow Vite dev server and external resources
        $viteHost = env('APP_ENV') === 'production' ? '' : "http://127.0.0.1:5173 http://localhost:5173 ws://127.0.0.1:5173 ws://localhost:5173 wss://127.0.0.1:5173 wss://localhost:5173";
        $response->header('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline' {$viteHost} https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' {$viteHost} https://fonts.googleapis.com; img-src 'self' data: https:; font-src 'self' data: https://fonts.gstatic.com; connect-src 'self' {$viteHost}; media-src 'self' https:;");

        return $response;
    }

    private function isProduction(): bool
    {
        return app()->environment('production');
    }
}
