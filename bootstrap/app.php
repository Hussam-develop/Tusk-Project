<?php

use App\Http\Middleware\GuardFromToken;
use App\Http\Middleware\MultiGuard;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 'auth.guard'=>\App\Http\Middleware\AssignGuard::class,
        $middleware->alias([
            'auth.guard' => MultiGuard::class,
            'auth.guardFromToken'=>GuardFromToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
