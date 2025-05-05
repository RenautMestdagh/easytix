<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SubdomainOrganizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $mainDomain = config('app.domain');
        $subdomain = str_replace('.' . $mainDomain, '', $host);

        if ($subdomain !== $host) {
            // Find the organization by subdomain
            $organization = Organization::where('subdomain', $subdomain)->first();
            if (!$organization) {
                abort(404, 'Organization not found');
            }
        }

        // Share the organization id with the request
        // retrieve with session('organization_id')
        session(['organization_id' => $organization->id ?? null]);

        // Share organization with all views
        // makes $organization available to all views
        View::share('organization', $organization ?? null);

        // Add organization to request for controller access
        // retrieve with request()->get('organization');
        $request->merge(['organization' => $organization ?? null]);

        return $next($request);
    }
}
