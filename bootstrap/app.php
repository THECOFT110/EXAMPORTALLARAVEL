<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

if (class_exists(\Composer\CaBundle\CaBundle::class)) {
    $caPath = \Composer\CaBundle\CaBundle::getSystemCaRootBundlePath();
    if (file_exists($caPath)) {
        @ini_set('curl.cainfo', $caPath);
        @ini_set('openssl.cafile', $caPath);
    }
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\StrictSessionTimeout::class,
        ]);

        $middleware->alias([
            'check.role' => \App\Http\Middleware\CheckRole::class,
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
