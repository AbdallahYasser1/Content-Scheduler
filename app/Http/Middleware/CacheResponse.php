<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Don't cache if user is not authenticated or if it's a POST/PUT/DELETE request
        if (!Auth::check() || !$request->isMethod('GET')) {
            return $next($request);
        }

        // Generate a unique cache key based on the full URL and user ID
        $cacheKey = 'page_' . md5($request->fullUrl() . '_user_' . Auth::id());
        
        // Check if we have a cached response
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Process the request
        $response = $next($request);
        
        // Cache the response for 5 minutes, but only if it's successful
        if ($response->isSuccessful()) {
            Cache::put($cacheKey, $response, now()->addMinutes(5));
        }

        return $response;
    }
}
