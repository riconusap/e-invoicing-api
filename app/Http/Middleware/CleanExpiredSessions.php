<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CleanExpiredSessions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only clean sessions for authenticated users
        if (auth('api')->check()) {
            $user = auth('api')->user();
            $user->cleanExpiredSessions();
        }

        return $next($request);
    }
}
