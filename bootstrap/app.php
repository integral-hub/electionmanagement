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
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'guest' => \Illuminate\Auth\Middleware\RedirectIfAuthenticated::class,
            'token.check' => \App\Http\Middleware\RedirectIfTokenInvalid::class,
            'voter.guest' => \App\Http\Middleware\RedirectIfVoterAuthenticated::class,
            'voter.auth' => \App\Http\Middleware\EnsureVoterIsAuthenticated::class,
            'authorize.login' => \App\Http\Middleware\AuthorizeVoterLogin::class,
            'portal.ready' => \App\Http\Middleware\EnsureVotingPortalReady::class,
        ]);
        
        $middleware->appendToGroup('auth', [
                \App\Http\Middleware\SetPermissionTeam::class,
                \App\Http\Middleware\AuthRequestLogs::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
