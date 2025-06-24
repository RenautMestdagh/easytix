<?php

use App\Http\Middleware\ForceHttps;
use App\Http\Middleware\SubdomainOrganizationMiddleware;
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
        $middleware->web(append: [
            ForceHttps::class,
            SubdomainOrganizationMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // Handle 404 - Not Found
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if (!app()->environment('production')) {
                return;
            }

            logger()->error("404 - Not Found: " . $e->getMessage(), [
                'exception' => $e,
                'url' => $request->url(),
            ]);

            // Try to find the matched route by the request path
            $route = collect(Route::getRoutes())->first(function ($route) use ($request) {
                return $route->matches($request);
            });

            // Check if the matched route exists and has 'auth' middleware
            if ($route && in_array('auth', $route->gatherMiddleware())) {
                return new RedirectResponse('/dashboard');
            }

            // Default to home for guest or unknown routes
            $subdomain = $request->route('subdomain');
            $url = $request->getScheme() . '://' . ($subdomain ? $subdomain . '.' : '') . config('app.domain');
            return redirect()->to($url);
        });

        // Handle 403 - Unauthorized (Forbidden)
        $exceptions->render(function (AccessDeniedHttpException $e, $request) {
            if (!app()->environment('production')) {
                return;
            }

            logger()->error("Unauthorized request: " . $e->getMessage(), [
                'exception' => $e,
                'url' => $request->url(),
            ]);

            if (auth()->check()) {
                return new RedirectResponse('/dashboard');
            } else {
                $subdomain = $request->route('subdomain');
                $url = $request->getScheme() . '://' . ($subdomain ? $subdomain . '.' : '') . config('app.domain');
                return redirect()->to($url);
            }
        });

        $exceptions->render(function (Throwable $e, $request) {
            if (!app()->environment('production')) {
                return;
            }

            logger()->error("Unhandled exception: " . $e->getMessage(), [
                'exception' => $e,
                'url' => $request->url(),
            ]);

            $subdomain = $request->route('subdomain');
            $url = $request->getScheme() . '://' . ($subdomain ? $subdomain . '.' : '') . config('app.domain');
            return redirect()->to($url);
        });
    })
    ->create();
