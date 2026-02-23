<?php

declare(strict_types=1);

namespace App\Http\Middleware\Packages;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePackagesAreBuilt
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! file_exists(storage_path('app/satis/index.html'))) {
            abort(503, 'Packages are not built yet.');
        }

        return $next($request);
    }
}
