<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), payment=(), usb=(), geolocation=(self)');

        if (! $response->headers->has('Content-Security-Policy')) {
            $isLocal = app()->environment('local', 'testing');

            $viteSources = $isLocal
                ? ' http://localhost:5173 http://127.0.0.1:5173'
                : '';

            $viteConnectSources = $isLocal
                ? ' http://localhost:5173 http://127.0.0.1:5173 ws://localhost:5173 ws://127.0.0.1:5173'
                : '';

            $directives = [
                "default-src 'self'".$viteSources,
                "base-uri 'self'",
                "object-src 'none'",
                "frame-ancestors 'self'",
                "form-action 'self'",
                "img-src 'self' data: blob: https:".$viteSources,
                "font-src 'self' data:".$viteSources,
                "style-src 'self' 'unsafe-inline' https:".$viteSources,
                "script-src 'self' 'unsafe-inline' 'unsafe-eval'".$viteSources,
                "connect-src 'self' http://127.0.0.1:11434 http://localhost:11434 https:".$viteConnectSources,
            ];

            if (! $isLocal) {
                $directives[] = 'upgrade-insecure-requests';
            }

            $response->headers->set(
                'Content-Security-Policy',
                implode('; ', $directives)
            );
        }

        return $response;
    }
}
