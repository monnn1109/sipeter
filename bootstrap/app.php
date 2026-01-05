<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ✅ Register custom middleware aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\CheckAdmin::class,
            'internal' => \App\Http\Middleware\CheckInternal::class,
            'check.document.owner' => \App\Http\Middleware\CheckDocumentOwner::class,
        ]);

        // ✅ NEW: Exclude CSRF untuk route tertentu (TEMPORARY untuk debugging)
        $middleware->validateCsrfTokens(except: [
            'admin/documents/*/update-status', // Temporary fix
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
