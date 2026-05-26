<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CacheControl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $type
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $type = 'dynamic')
    {
        $response = $next($request);

        switch ($type) {
            case 'static':
                // Aggressive caching for versioned static assets
                $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
                $response->headers->set('Expires', now()->addYear()->toRfc7231String());
                break;

            case 'api':
                // No cache for API responses
                $response->headers->set('Cache-Control', 'no-cache, private');
                $response->headers->set('Vary', 'Authorization, Accept-Encoding');
                break;

            case 'dynamic':
            default:
                // No cache for dynamic HTML content
                $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
                $response->headers->set('Pragma', 'no-cache');
                $response->headers->set('Expires', '0');
                $response->headers->set('Vary', 'Accept-Encoding, User-Agent');
                break;
        }

        return $response;
    }
}