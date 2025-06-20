<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the current route name
        $routeName = $request->route()->getName();

        // If no route name, allow access (or you might want to deny)
        if (!$routeName) {
            return $next($request);
        }

        // Map route names to permission names
        $permission = $this->mapRouteToPermission($routeName);


        if($permission === 'login-as.use' && session('original_user_id')) {
            return $next($request);
        }

        // Check if user has the permission
        if($permission && !$request->user()->hasPermissionTo($permission)) {
            throw new AuthorizationException('This action is unauthorized.');
        }

        return $next($request);
    }

    /**
     * Map route names to permission names based on your defined structure
     */
    protected function mapRouteToPermission(string $routeName): ?string
    {
        // Split route name by dots
        $parts = explode('.', $routeName);

        // Default mapping pattern: {resource}.{action} => {resource}.{action}
        $resource = $parts[0];
        if ($resource === 'dashboard' || $resource === 'settings') {
            return null;
        }

        $action = $parts[1];

        if(!$action) {
            Throw new \Exception('Action not found');
        }

        return "{$resource}.{$action}";
    }
}
