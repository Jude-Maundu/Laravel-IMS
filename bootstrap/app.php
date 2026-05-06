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
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust all proxies for Render/Railway (so generated URLs are HTTPS)
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'check.role' => \App\Http\Middleware\CheckRole::class,
            'validate.scan' => \App\Http\Middleware\ValidateScanSession::class,
            'validate.receive' => \App\Http\Middleware\ValidateReceiveSession::class,
            'customer.auth' => \App\Http\Middleware\CustomerAuthMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
