<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// --- Core Middleware imports ---
use Illuminate\Http\Middleware\TrustHosts;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Middleware\HandleCors;                           
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;

// --- Web Group imports ---
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

// --- Route Middleware aliases ---
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Routing\Middleware\ThrottleRequests;

return Application::configure(
    basePath: dirname(__DIR__)
)
->withRouting(
    web: __DIR__ . '/../routes/web.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
)
->withMiddleware(function (Middleware $middleware) {
    //
    // 1) Global middleware
    //
    $middleware->prepend(TrustHosts::class);
    $middleware->prepend(TrustProxies::class);
    $middleware->prepend(HandleCors::class);                       
    $middleware->prepend(PreventRequestsDuringMaintenance::class);
    $middleware->prepend(ValidatePostSize::class);
    $middleware->prepend(TrimStrings::class);
    $middleware->prepend(ConvertEmptyStringsToNull::class);

    //
    // 2) Web middleware group
    //
    $middleware->web(append: [
        EncryptCookies::class,
        AddQueuedCookiesToResponse::class,
        StartSession::class,
        ShareErrorsFromSession::class,
        VerifyCsrfToken::class,                                        // CSRF
        SubstituteBindings::class,
        HandleInertiaRequests::class,                                  // Inertia
        AddLinkHeadersForPreloadedAssets::class,
    ]);

    //
    // 3) API middleware group
    //
    $middleware->group('api', [
        'throttle:api',
        SubstituteBindings::class,
    ]);

    //
    // 4) Route middleware aliases
    //
    $middleware->alias([
        'auth'     => Authenticate::class,
        'guest'    => RedirectIfAuthenticated::class,
        'verified' => EnsureEmailIsVerified::class,
        'throttle' => ThrottleRequests::class,
    ]);
})
->withExceptions(function (Exceptions $exceptions) {
    //
})
->create();
