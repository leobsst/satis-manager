<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureComposerUserAgent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userAgent = $request->userAgent();

        // Vérifier si le User-Agent contient "Composer"
        if (! $userAgent || ! str_contains(strtolower($userAgent), 'composer')) {
            abort(403, 'Access denied. Composer User-Agent required.');
        }

        return $next($request);
    }
}
