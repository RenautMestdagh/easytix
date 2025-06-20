<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        if(app()->environment('local'))
            return;

        // Handle 404 - Not Found
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            // Try to find the matched route by the request path
            $route = collect(Route::getRoutes())->first(function ($route) use ($request) {
                return $route->matches($request);
            });

            // Check if the matched route exists and has 'auth' middleware
            if ($route && in_array('auth', $route->gatherMiddleware())) {
                return new RedirectResponse('/dashboard');
            }

            // Default to home for guest or unknown routes
            return new RedirectResponse('/');
        });

        // Handle 403 - Unauthorized (Forbidden)
        $exceptions->render(function (AccessDeniedHttpException $e, $request) {
            if (auth()->check()) {
                return new RedirectResponse('/dashboard');
            } else {
                return new RedirectResponse('/');
            }
        });

        return new RedirectResponse('/');
    })
    ->create();
