<?php

namespace App\Traits;

use Illuminate\Auth\Access\AuthorizationException;

trait AuthorizesWithPermission
{
    protected function authorizePermission()
    {
        // get route name
        $routeName = request()->route()->getName();

        // If this is a Livewire request, try to get the original route
        if ($routeName === 'livewire.update') {
            $referer = request()->header('referer');
            if ($referer) {
                $route = app('router')->getRoutes()->match(
                    request()->create($referer)
                );
                $routeName = $route->getName();
            }
        }
        $permission = $routeName;
        if (!auth()->user()->can($permission)) {
            throw new AuthorizationException;
        }
    }
}
