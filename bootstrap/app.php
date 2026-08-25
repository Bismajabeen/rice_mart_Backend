<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

    })

    ->withExceptions(function (Exceptions $exceptions): void {

        // Force JSON error responses for every /api/* route, regardless of
        // the request's Accept header. Without this, an unauthenticated hit
        // to an api/* route sent with Accept: text/html (e.g. typing the
        // URL directly into the browser) makes Laravel try to redirect to
        // a "login" route, which doesn't exist in this API-only app, and
        // throws RouteNotFoundException instead of a clean 401 JSON reply.
        $exceptions->shouldRenderJsonWhen(function (Request $request, \Throwable $e) {
            return $request->is('api/*') || $request->expectsJson();
        });

    })->create();
