<?php

use App\Helpers\ApiHelper;
use App\Http\Middleware\EnsureComposerUserAgent;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Laravel\Passport\Http\Middleware\EnsureClientIsResourceOwner;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        then: function () {
            Route::middleware(['api', 'client_credentials'])
                ->prefix('webhooks')
                ->name('webhooks.')
                ->group(base_path('routes/webhooks.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'client_credentials' => EnsureClientIsResourceOwner::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'composer' => EnsureComposerUserAgent::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(using: function (NotFoundHttpException $e, $request): JsonResponse | Response {
            if ($request->is('api/*')) {
                return response()->json(data: ApiHelper::getErrorResponseArray([
                    'message' => 'Resource not found.',
                ]), status: 404);
            } else {
                return response()->view(view: 'errors.404', status: 404);
            }
        });
        $exceptions->render(using: function (MethodNotAllowedHttpException $e, $request): JsonResponse | Response {
            if ($request->is('api/*')) {
                return response()->json(data: ApiHelper::getErrorResponseArray([
                    'message' => 'Method not allowed.',
                ]), status: 405);
            } else {
                return response()->view(view: 'errors.405', status: 405);
            }
        });
        $exceptions->render(using: function (AuthenticationException $e, $request): JsonResponse | Response {
            if ($request->is(['api/*', 'webhooks/*'])) {
                return response()->json(data: ApiHelper::getErrorResponseArray([
                    'message' => __('errors.401.message.p1') . ' ' . __('errors.401.message.p2'),
                ]), status: 401);
            } else {
                return response()->view(view: 'errors.401', status: 401);
            }
        });
    })->create();
