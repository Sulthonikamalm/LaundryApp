<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * CacheResponse - HTTP Response Caching Middleware
 * 
 * DeepPerformance: Cache HTML response untuk menghindari re-render Livewire.
 * DeepReasoning: Filament admin pages jarang berubah dalam 1 menit.
 * DeepTeknik: Cache per user + route untuk isolasi.
 */
class CacheResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int  $ttl  TTL in seconds (default 60)
     * @return mixed
     */
    public function handle(Request $request, Closure $next, int $ttl = 60): Response
    {
        // Only cache GET requests
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        // Skip caching for Livewire AJAX requests
        if ($request->header('X-Livewire')) {
            return $next($request);
        }

        // Generate cache key
        $cacheKey = $this->getCacheKey($request);

        // Try to get cached response
        $cachedResponse = Cache::get($cacheKey);
        if ($cachedResponse) {
            return response($cachedResponse['content'])
                ->withHeaders($cachedResponse['headers'])
                ->header('X-Cache', 'HIT');
        }

        // Process request
        $response = $next($request);

        // Cache successful HTML responses
        if ($response->isSuccessful() && $this->shouldCache($response)) {
            Cache::put($cacheKey, [
                'content' => $response->getContent(),
                'headers' => $response->headers->all(),
            ], $ttl);
        }

        return $response->header('X-Cache', 'MISS');
    }

    /**
     * Generate cache key.
     * 
     * @param Request $request
     * @return string
     */
    protected function getCacheKey(Request $request): string
    {
        $userId = auth()->id() ?? 'guest';
        $route = $request->path();
        $query = md5(serialize($request->query()));

        return "response_cache:{$userId}:{$route}:{$query}";
    }

    /**
     * Determine if response should be cached.
     * 
     * @param Response $response
     * @return bool
     */
    protected function shouldCache(Response $response): bool
    {
        $contentType = $response->headers->get('Content-Type', '');
        
        return str_contains($contentType, 'text/html');
    }
}
