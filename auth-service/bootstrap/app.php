<?php

use App\Http\Middleware\DenyPassportDirectAccess;
use App\Http\Middleware\EnsureInternalRequest;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'internal' => EnsureInternalRequest::class,
        ]);
        // Block direct access to Passport's /oauth/* routes from external sources
        $middleware->appendToGroup('web', DenyPassportDirectAccess::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Return JSON for API authentication exceptions
        $exceptions->shouldRenderJsonWhen(function ($request, $e) {
            return $request->is('api/*') || $request->expectsJson();
        });
    })
    ->create();
