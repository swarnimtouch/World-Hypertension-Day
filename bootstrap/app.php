<?php

use App\Http\Middleware\TrustProxies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

            // Global middleware (runs on every request)
            $middleware->use([
                \Illuminate\Http\Middleware\TrustProxies::class,
                HandleCors::class,
                \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
                \App\Http\Middleware\TrimStrings::class,
                \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
                \App\Http\Middleware\SecurityHeadersMiddleware::class
            ]);

            // Web middleware group
            $middleware->web(append: [
                \App\Http\Middleware\EncryptCookies::class,
                \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
                \Illuminate\Session\Middleware\StartSession::class,
                \Illuminate\View\Middleware\ShareErrorsFromSession::class,
                \App\Http\Middleware\VerifyCsrfToken::class,
                \Illuminate\Routing\Middleware\SubstituteBindings::class,
            ]);

            // API middleware group
            $middleware->api(append: [
                \Illuminate\Routing\Middleware\SubstituteBindings::class,
            ], prepend: [
                \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            ]);

            // Middleware aliases (previously routeMiddleware)
            $middleware->alias([
                'auth' => \App\Http\Middleware\Authenticate::class,
                'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
                'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
                'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
                'can' => \Illuminate\Auth\Middleware\Authorize::class,
                'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
                'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
                'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
                'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
                'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
                'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

                'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
                'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
                'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
                // Your custom middleware
                'nocache' => \App\Http\Middleware\NoCache::class,
            ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->withProviders([
        RealRashid\SweetAlert\SweetAlertServiceProvider::class,
    ])->create();
